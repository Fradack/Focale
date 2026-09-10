<?php

namespace App\Plugins\LivreDor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Plugins\LivreDor\Models\GuestbookEntry;
use App\Services\SpamGuard;
use App\Support\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestbookController extends Controller
{
    public function store(Request $request, Album $album, SpamGuard $spamGuard): RedirectResponse
    {
        abort_unless($album->guestbook_enabled, 404);

        $customer = $request->user()?->is_customer ? $request->user() : null;

        $data = $request->validate([
            'author_name' => [$customer ? 'nullable' : 'required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'website' => ['prohibited'],
        ]);

        if ($spamGuard->looksAutomated($request) || $spamGuard->hasTooManyLinks($data['message'])) {
            return back()->with('status', 'guestbook-submitted');
        }

        GuestbookEntry::create([
            'album_id' => $album->id,
            'visitor_id' => $customer ? null : Visitor::id(),
            'user_id' => $customer?->id,
            'author_name' => $customer->name ?? $data['author_name'],
            'message' => $data['message'],
            'status' => 'pending',
            'ip' => $request->ip(),
        ]);

        return back()->with('status', 'guestbook-submitted');
    }
}
