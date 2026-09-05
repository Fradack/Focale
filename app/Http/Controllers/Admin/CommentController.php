<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::with('album')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.comments.index', [
            'comments' => $query->paginate(30)->withQueryString(),
            'counts' => [
                'all' => Comment::count(),
                'pending' => Comment::pending()->count(),
                'approved' => Comment::approved()->count(),
            ],
        ]);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'approved']);

        return back()->with('status', 'comment-approved');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('status', 'comment-deleted');
    }
}
