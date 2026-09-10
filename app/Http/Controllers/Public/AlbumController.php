<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Services\MediaProcessingStatus;
use App\Services\QueuePump;
use App\Services\SpamGuard;
use App\Support\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AlbumController extends Controller
{
    public function index(): View
    {
        return view('public.albums', [
            'albums' => Album::listable()->with('cover.variants')->latest('published_at')->paginate(12),
        ]);
    }

    public function show(Request $request, Album $album): View|Response
    {
        $isOwner = $request->user() !== null;

        if (! $isOwner) {
            if (! in_array($album->status, ['published', 'unlisted'], true) || $album->visibility === 'private') {
                abort(404);
            }
        }

        if ($album->visibility === 'password' && ! $isOwner && ! $this->isUnlocked($request, $album)) {
            return response()->view('public.album-lock', ['album' => $album])->setStatusCode(200);
        }

        $album->load(['media.variants', 'approvedComments', 'approvedGuestbookEntries']);

        return view('public.album', [
            'album' => $album,
            'previous' => $this->neighbour($album, '<'),
            'next' => $this->neighbour($album, '>'),
            'processingStatus' => MediaProcessingStatus::forAlbum($album->id),
            'liked' => $album->isLikedBy(Visitor::id()),
        ]);
    }

    public function toggleLike(Album $album): JsonResponse
    {
        abort_unless(in_array($album->status, ['published', 'unlisted'], true), 404);

        $visitorId = Visitor::id();
        $existing = $album->likes()->where('visitor_id', $visitorId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $album->likes()->create(['visitor_id' => $visitorId]);
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'count' => $album->likes()->count()]);
    }

    public function processingStatus(Album $album): JsonResponse
    {
        QueuePump::pumpIfDue();

        return response()->json(MediaProcessingStatus::forAlbum($album->id));
    }

    public function unlock(Request $request, Album $album): RedirectResponse
    {
        $request->validate(['password' => ['required', 'string']]);

        if (! $album->checkPassword($request->input('password'))) {
            return back()->withErrors(['password' => "Mot de passe incorrect."]);
        }

        $unlocked = $request->session()->get('unlocked_albums', []);
        $unlocked[] = $album->id;
        $request->session()->put('unlocked_albums', array_unique($unlocked));

        return redirect()->route('public.album', $album);
    }

    public function storeComment(Request $request, Album $album, SpamGuard $spamGuard): RedirectResponse
    {
        if (! $album->comments_enabled) {
            abort(404);
        }

        // Un client connecté commente sous le nom de son compte — pas besoin
        // de ressaisir un nom à chaque fois, et le commentaire lui reste
        // rattaché (voir Comment::user()).
        $customer = $request->user()?->is_customer ? $request->user() : null;

        $data = $request->validate([
            'author_name' => [$customer ? 'nullable' : 'required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'website' => ['prohibited'], // champ honeypot : doit rester vide
        ]);

        if ($spamGuard->looksAutomated($request) || $spamGuard->hasTooManyLinks($data['body'])) {
            return back()->with('status', 'comment-submitted');
        }

        $album->comments()->create([
            'user_id' => $customer?->id,
            'author_name' => $customer->name ?? $data['author_name'],
            'body' => $data['body'],
            'status' => 'pending',
            'ip' => $request->ip(),
        ]);

        return back()->with('status', 'comment-submitted');
    }

    private function isUnlocked(Request $request, Album $album): bool
    {
        return in_array($album->id, $request->session()->get('unlocked_albums', []), true);
    }

    private function neighbour(Album $album, string $operator): ?Album
    {
        return Album::listable()
            ->where('published_at', $operator, $album->published_at)
            ->orderBy('published_at', $operator === '<' ? 'desc' : 'asc')
            ->first();
    }
}
