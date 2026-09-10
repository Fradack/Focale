<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AlbumController extends Controller
{
    public function index(Request $request): View
    {
        $query = Album::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $albums = $query->withCount('media')->latest('updated_at')->paginate(20)->withQueryString();

        return view('admin.albums.index', [
            'albums' => $albums,
            'counts' => [
                'all' => Album::count(),
                'published' => Album::where('status', 'published')->count(),
                'draft' => Album::where('status', 'draft')->count(),
                'archived' => Album::where('status', 'archived')->count(),
            ],
        ]);
    }

    public function store(): RedirectResponse
    {
        $title = 'Nouvel album';
        $slug = $title;
        $i = 1;
        while (Album::where('slug', Str::slug($slug))->exists()) {
            $slug = $title.' '.(++$i);
        }

        $album = Album::create([
            'title' => $title,
            'slug' => Str::slug($slug),
            'status' => 'draft',
            'visibility' => 'public',
        ]);

        return redirect()->route('admin.albums.edit', $album);
    }

    public function edit(Album $album): View
    {
        $album->load(['media' => fn ($q) => $q->orderByPivot('sort_order')]);

        return view('admin.albums.edit', ['album' => $album]);
    }

    public function update(Request $request, Album $album): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:albums,slug,'.$album->id],
            'intro_text' => ['nullable', 'string'],
            'created_on' => ['nullable', 'date'],
            'published_at' => ['nullable', 'date'],
            'credits_text' => ['nullable', 'string'],
            'external_link' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:draft,unlisted,published,archived'],
            'visibility' => ['required', 'in:public,password,private'],
            'password' => ['nullable', 'string', 'min:4'],
            'seo_auto' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'comments_enabled' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'guestbook_enabled' => ['nullable', 'boolean'],
        ]);

        $password = $data['password'] ?? null;
        unset($data['password']);

        $data['seo_auto'] = $request->boolean('seo_auto');
        $data['comments_enabled'] = $request->boolean('comments_enabled');
        $data['is_featured'] = $request->boolean('is_featured');

        // La colonne `guestbook_enabled` n'existe que si le plugin Livre
        // d'or a été installé (sa migration l'ajoute) — ne jamais tenter de
        // l'écrire avant, sous peine d'une erreur SQL "Unknown column".
        if (\App\Support\Plugins::enabled('livre-dor')) {
            $data['guestbook_enabled'] = $request->boolean('guestbook_enabled');
        } else {
            unset($data['guestbook_enabled']);
        }

        $album->fill($data);

        if ($album->visibility === 'password' && $password) {
            $album->setPassword($password);
        } elseif ($album->visibility !== 'password') {
            $album->password_hash = null;
        }

        if ($album->status === 'published' && ! $album->published_at) {
            $album->published_at = now();
        }

        $album->save();

        return redirect()->route('admin.albums.edit', $album)->with('status', 'album-updated');
    }

    public function duplicate(Album $album): RedirectResponse
    {
        $copy = $album->replicate(['slug', 'published_at']);
        $copy->title = $album->title.' (copie)';
        $copy->slug = Str::slug($copy->title.'-'.Str::random(4));
        $copy->status = 'draft';
        $copy->published_at = null;
        $copy->save();

        $copy->media()->sync(
            $album->media->mapWithKeys(fn ($m) => [$m->id => ['sort_order' => $m->pivot->sort_order]])
        );

        return redirect()->route('admin.albums.edit', $copy);
    }

    public function destroy(Album $album): RedirectResponse
    {
        $album->delete();

        return redirect()->route('admin.albums.index')->with('status', 'album-deleted');
    }

    public function attachMedia(Request $request, Album $album): RedirectResponse
    {
        $ids = collect(explode(',', (string) $request->input('media_ids')))->filter()->map(fn ($id) => (int) $id);
        $nextOrder = ($album->media()->max('sort_order') ?? -1) + 1;

        foreach ($ids as $id) {
            if (! $album->media->contains('id', $id)) {
                $album->media()->attach($id, ['sort_order' => $nextOrder]);
                $nextOrder++;
            }
        }

        if (! $album->cover_media_id && $first = $ids->first()) {
            $album->update(['cover_media_id' => $first]);
        }

        return back()->with('status', 'media-attached');
    }

    public function detachMedia(Album $album, Media $media): RedirectResponse
    {
        $album->media()->detach($media->id);

        if ($album->cover_media_id === $media->id) {
            $newCover = $album->media()->first();
            $album->update(['cover_media_id' => $newCover?->id]);
        }

        return back()->with('status', 'media-detached');
    }

    public function reorderMedia(Request $request, Album $album): RedirectResponse
    {
        $order = $request->validate(['order' => ['required', 'array']])['order'];

        foreach ($order as $position => $mediaId) {
            $album->media()->updateExistingPivot($mediaId, ['sort_order' => $position]);
        }

        return back();
    }

    public function setCover(Request $request, Album $album): RedirectResponse
    {
        $data = $request->validate(['media_id' => ['required', 'exists:media,id']]);
        $album->update(['cover_media_id' => $data['media_id']]);

        return back();
    }
}
