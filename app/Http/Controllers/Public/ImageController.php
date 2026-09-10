<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaView;
use App\Support\Visitor;
use Illuminate\Http\JsonResponse;
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
            'liked' => $media->isLikedBy(Visitor::id()),
        ]);
    }

    /**
     * Cœur/like anonyme — un par (œuvre, visiteur), identifié par le cookie
     * posé par AssignVisitorId. Bascule l'état plutôt que d'exposer un
     * "unlike" séparé.
     */
    public function toggleLike(Media $media): JsonResponse
    {
        abort_unless($media->status === 'published', 404);

        $visitorId = Visitor::id();
        $existing = $media->likes()->where('visitor_id', $visitorId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $media->likes()->create(['visitor_id' => $visitorId]);
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'count' => $media->likes()->count()]);
    }

    /**
     * Appelé côté client après 10s passées sur la fiche (voir public/image.blade.php)
     * — une "vue" par (œuvre, visiteur, jour), pas par simple chargement de page.
     */
    public function recordView(Media $media): JsonResponse
    {
        abort_unless($media->status === 'published', 404);

        MediaView::firstOrCreate([
            'media_id' => $media->id,
            'visitor_id' => Visitor::id(),
            'viewed_on' => now()->toDateString(),
        ]);

        return response()->json(['ok' => true]);
    }
}
