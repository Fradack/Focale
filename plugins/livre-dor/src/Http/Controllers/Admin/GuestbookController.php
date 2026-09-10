<?php

namespace App\Plugins\LivreDor\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Plugins\LivreDor\Models\GuestbookEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuestbookController extends Controller
{
    public function index(): View
    {
        return view('plugins.livre-dor.admin.index', [
            'entries' => GuestbookEntry::with(['album', 'user'])
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString(),
            'total' => GuestbookEntry::count(),
        ]);
    }

    public function approve(GuestbookEntry $entry): RedirectResponse
    {
        $entry->update(['status' => 'approved']);

        return back()->with('status', 'entry-approved');
    }

    public function destroy(GuestbookEntry $entry): RedirectResponse
    {
        $entry->delete();

        return back()->with('status', 'entry-deleted');
    }
}
