<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Media;
use App\Models\Setting;
use App\Services\MediaIngestService;
use App\Services\MediaProcessingStatus;
use App\Services\QueuePump;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MediaController extends Controller
{
    private const PER_PAGE_OPTIONS = [25, 50, 75, 100, 150, 200];

    /**
     * Une œuvre "en cours de traitement" depuis plus longtemps que ça est
     * considérée bloquée plutôt que simplement en attente normale — voir
     * clearStuck(). Un import qui vient de démarrer ne doit jamais être
     * proposé à la suppression.
     */
    private const STUCK_AFTER_MINUTES = 30;

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
            if ($status === 'processing') {
                $query->has('variants', '<', 3);
            } elseif ($status === 'videos') {
                $query->where('mime_type', 'like', 'video/%');
            } else {
                $query->where('status', $status);
            }
        }

        $perPage = (int) $request->query('per_page', 50);
        if (! in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 50;
        }

        $media = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.media.index', [
            'media' => $media,
            'view' => $request->query('view') === 'list' ? 'list' : 'grid',
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'albums' => Album::orderBy('title')->get(['id', 'title']),
            'counts' => [
                'all' => Media::whereNull('trashed_at')->count(),
                'draft' => Media::whereNull('trashed_at')->where('status', 'draft')->count(),
                'published' => Media::whereNull('trashed_at')->where('status', 'published')->count(),
                'trashed' => Media::whereNotNull('trashed_at')->count(),
                'processing' => Media::whereNull('trashed_at')->has('variants', '<', 3)->count(),
                'videos' => Media::whereNull('trashed_at')->where('mime_type', 'like', 'video/%')->count(),
                'stuck' => $this->stuckQuery()->count(),
            ],
        ]);
    }

    private function stuckQuery()
    {
        return Media::whereNull('trashed_at')
            ->where('created_at', '<', now()->subMinutes(self::STUCK_AFTER_MINUTES))
            ->has('variants', '<', 3);
    }

    /**
     * Bouton de secours pour une file bloquée (voir la file "jobs" qui peut
     * rester coincée sur un hébergement sans worker permanent) : met à la
     * corbeille les œuvres en traitement depuis plus de 30 minutes, sans
     * avoir besoin d'un accès direct à la base de données. Réversible
     * (corbeille), jamais les œuvres qui viennent tout juste d'être importées.
     */
    public function clearStuck(): RedirectResponse
    {
        $stuck = $this->stuckQuery()->get();

        $stuck->each(function (Media $item) {
            $item->update(['trashed_at' => now()]);
            $item->removeFromAlbums();
        });

        $message = $stuck->isEmpty()
            ? "Aucune œuvre bloquée détectée."
            : $stuck->count()." œuvre(s) bloquée(s) mise(s) à la corbeille.";

        return redirect()->route('admin.media.index')->with('status', $message);
    }

    public function processingStatus(): JsonResponse
    {
        QueuePump::pumpIfDue();

        return response()->json(MediaProcessingStatus::global());
    }

    /**
     * Statut de traitement d'une œuvre précise (utilisé par la file d'import
     * pour afficher la progression de chaque photo individuellement, juste
     * après son envoi).
     */
    public function itemProcessingStatus(Media $media): JsonResponse
    {
        QueuePump::pumpIfDue();

        $variants = $media->variants()->count();

        return response()->json([
            'variants' => $variants,
            'total' => 3,
            'done' => $variants >= 3,
        ]);
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
                $items->each(fn (Media $item) => $item->removeFromAlbums());
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
        $importDir = MediaIngestService::importFolderPath();

        return view('admin.media.import', [
            'importFolderPath' => $importDir,
            'importFolderCount' => is_dir($importDir)
                ? collect(File::files($importDir))
                    ->filter(fn ($f) => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true))
                    ->count()
                : 0,
            'importOneByOne' => Setting::get('import_one_by_one') !== '0',
        ]);
    }

    public function importFromFolder(MediaIngestService $service): RedirectResponse
    {
        if (Setting::get('import_one_by_one') !== '0') {
            return redirect()->route('admin.media.import')
                ->with('status', "L'import de masse est désactivé tant que le réglage « une photo par une photo » est actif (Réglages → Médiathèque).");
        }

        $result = $service->ingestFromFolder();

        $message = "{$result['imported']} œuvre(s) importée(s)";
        if ($result['duplicates'] > 0) {
            $message .= ", {$result['duplicates']} doublon(s) ignoré(s)";
        }
        if ($result['remaining'] > 0) {
            $message .= ". {$result['remaining']} fichier(s) restant(s) — relance l'import pour continuer.";
        } else {
            $message .= '.';
        }

        return redirect()->route('admin.media.import')->with('status', $message);
    }

    public function store(Request $request, MediaIngestService $service): JsonResponse
    {
        $request->validate([
            // Plafond commun image+vidéo : reste sous upload_max_filesize/
            // post_max_size par défaut (souvent 25-32 Mo sur un hébergement
            // mutualisé) — une vidéo plus lourde nécessite d'augmenter ces
            // réglages PHP côté hébergement, ce que Focale ne peut pas faire lui-même.
            'file' => ['required', 'file', 'max:24576', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm'],
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
        $media->removeFromAlbums();

        return back()->with('status', 'media-trashed');
    }

    public function restore(Media $media): RedirectResponse
    {
        $media->update(['trashed_at' => null]);

        return back()->with('status', 'media-restored');
    }
}
