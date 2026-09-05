<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImageController extends Controller
{
    public function index(): View
    {
        return view('public.gallery', [
            'media' => Media::published()->latest()->paginate(24),
        ]);
    }

    public function show(Request $request, Media $media): View
    {
        $isOwner = $request->user() !== null;

        if (! $isOwner && $media->status !== 'published') {
            abort(404);
        }

        $albumsQuery = $media->albums()->where('visibility', '!=', 'private');
        if (! $isOwner) {
            $albumsQuery->where('status', 'published');
        }
        $album = $albumsQuery->first();

        $previous = null;
        $next = null;

        if ($album) {
            $ordered = $album->media()->orderByPivot('sort_order')->get();
            $index = $ordered->search(fn ($m) => $m->id === $media->id);
            $previous = $index !== false && $index > 0 ? $ordered[$index - 1] : null;
            $next = $index !== false && $index < $ordered->count() - 1 ? $ordered[$index + 1] : null;
        } else {
            // Œuvre non rattachée à un album : navigation dans l'ordre de la
            // galerie générale plutôt que de bloquer l'accès.
            $previous = Media::published()->where('id', '<', $media->id)->orderByDesc('id')->first();
            $next = Media::published()->where('id', '>', $media->id)->orderBy('id')->first();
        }

        return view('public.image', [
            'media' => $media->load('tags'),
            'album' => $album,
            'previous' => $previous,
            'next' => $next,
        ]);
    }
}
