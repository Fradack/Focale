<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Media;
use App\Services\MediaIngestService;
use App\Services\MediaProcessingStatus;
use App\Services\QueuePump;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Media::query()->whereNull('trashed_at');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('tags', fn ($t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $media = $query->latest()->paginate(60)->withQueryString();

        return view('admin.media.index', [
            'media' => $media,
            'view' => $request->query('view') === 'list' ? 'list' : 'grid',
            'albums' => Album::orderBy('title')->get(['id', 'title']),
            'counts' => [
                'all' => Media::whereNull('trashed_at')->count(),
                'draft' => Media::whereNull('trashed_at')->where('status', 'draft')->count(),
                'published' => Media::whereNull('trashed_at')->where('status', 'published')->count(),
                'trashed' => Media::whereNotNull('trashed_at')->count(),
            ],
        ]);
    }

    public function processingStatus(): JsonResponse
    {
        QueuePump::pumpIfDue();

        return response()->json(MediaProcessingStatus::global());
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:media,id'],
            'do' => ['required', 'in:publish,unpublish,trash,add_to_album'],
            'album_id' => ['required_if:do,add_to_album', 'nullable', 'exists:albums,id'],
        ]);

        $items = Media::whereIn('id', $data['ids'])->get();

        switch ($data['do']) {
            case 'publish':
                Media::whereIn('id', $data['ids'])->update(['status' => 'published']);
                $message = count($items).' œuvre(s) publiée(s).';
                break;

            case 'unpublish':
                Media::whereIn('id', $data['ids'])->update(['status' => 'draft']);
                $message = count($items).' œuvre(s) repassée(s) en brouillon.';
                break;

            case 'trash':
                Media::whereIn('id', $data['ids'])->update(['trashed_at' => now()]);
                $message = count($items).' œuvre(s) mise(s) à la corbeille.';
                break;

            case 'add_to_album':
                $album = Album::findOrFail($data['album_id']);
                $nextOrder = ($album->media()->max('sort_order') ?? -1) + 1;

                foreach ($items as $item) {
                    if (! $album->media->contains('id', $item->id)) {
                        $album->media()->attach($item->id, ['sort_order' => $nextOrder]);
                        $nextOrder++;
                    }
                }

                if (! $album->cover_media_id) {
                    $album->update(['cover_media_id' => $items->first()?->id]);
                }

                $message = count($items)." œuvre(s) ajoutée(s) à l'album « {$album->title} ».";
                break;
        }

        return back()->with('status', $message);
    }

    public function create(): View
    {
        return view('admin.media.import');
    }

    public function store(Request $request, MediaIngestService $service): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimetypes:image/jpeg,image/png,image/webp,image/gif'],
        ]);

        $result = $service->ingest($request->file('file'));

        return response()->json([
            'duplicate' => $result['duplicate'],
            'media' => [
                'id' => $result['media']->id,
                'title' => $result['media']->title,
                'thumbnail_url' => $result['media']->variant('thumbnail')?->url(),
            ],
        ]);
    }

    public function edit(Media $media): View
    {
        return view('admin.media.edit', ['media' => $media->load('tags', 'categories')]);
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'credit' => ['nullable', 'string', 'max:255'],
            'copyright' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'license' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'hide_gps' => ['nullable', 'boolean'],
            'exif_hidden_fields' => ['nullable', 'array'],
            'exif_hidden_fields.*' => ['string', 'in:'.implode(',', array_keys(Media::EXIF_FIELDS))],
            'status' => ['required', 'in:draft,published'],
            'tags' => ['nullable', 'string'],
        ]);

        $data['hide_gps'] = $request->boolean('hide_gps');
        $data['exif_hidden_fields'] = $data['exif_hidden_fields'] ?? [];

        $media->update($data);

        if (isset($data['tags'])) {
            $names = array_filter(array_map('trim', explode(',', $data['tags'])));
            $ids = collect($names)->map(fn ($name) => \App\Models\Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name]
            )->id);
            $media->tags()->sync($ids);
        }

        return redirect()->route('admin.media.index')->with('status', 'media-updated');
    }

    public function destroy(Media $media): RedirectResponse
    {
        $media->update(['trashed_at' => now()]);

        return back()->with('status', 'media-trashed');
    }

    public function restore(Media $media): RedirectResponse
    {
        $media->update(['trashed_at' => null]);

        return back()->with('status', 'media-restored');
    }
}
