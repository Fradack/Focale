<?php

namespace App\Plugins\Avis\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Plugins\Avis\Models\Review;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('plugins.avis.admin.index', [
            'reviews' => Review::with(['user', 'answers'])
                ->orderByDesc('submitted_at')
                ->paginate(20)
                ->withQueryString(),
            'total' => Review::count(),
        ]);
    }
}
