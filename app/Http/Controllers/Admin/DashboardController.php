<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaLike;
use App\Models\SiteVisit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'publishedCount' => Media::published()->count(),
            'visitors7d' => SiteVisit::where('visited_on', '>=', now()->subDays(7)->startOfDay())->distinct('visitor_id')->count('visitor_id'),
            'likesCount' => MediaLike::count(),
        ]);
    }
}
