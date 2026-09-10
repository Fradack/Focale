<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaLike;
use App\Models\MediaView;
use App\Models\SiteVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Statistiques de trafic — construites sur le journal `site_visits` et les
 * compteurs `media_likes`/`media_views` (voir AssignVisitorId/LogSiteVisit).
 * Tout est anonyme (aucune IP stockée) : un visiteur = un cookie navigateur.
 */
class StatsController extends Controller
{
    private const PERIOD_DAYS = 30;

    public function index(): View
    {
        $since = now()->subDays(self::PERIOD_DAYS)->startOfDay();

        $totalVisits = SiteVisit::count();
        $totalUniqueVisitors = SiteVisit::distinct('visitor_id')->count('visitor_id');

        $periodVisits = SiteVisit::where('visited_on', '>=', $since)->count();
        $periodUniqueVisitors = SiteVisit::where('visited_on', '>=', $since)->distinct('visitor_id')->count('visitor_id');

        $dailyVisits = SiteVisit::where('visited_on', '>=', $since)
            ->select('visited_on', DB::raw('count(*) as visits'), DB::raw('count(distinct visitor_id) as uniques'))
            ->groupBy('visited_on')
            ->orderBy('visited_on')
            ->get()
            ->keyBy(fn ($row) => $row->visited_on->format('Y-m-d'));

        $days = collect(range(0, self::PERIOD_DAYS - 1))
            ->map(fn ($i) => now()->subDays(self::PERIOD_DAYS - 1 - $i)->format('Y-m-d'))
            ->map(fn ($date) => [
                'date' => $date,
                'visits' => (int) ($dailyVisits[$date]->visits ?? 0),
                'uniques' => (int) ($dailyVisits[$date]->uniques ?? 0),
            ]);

        $topPaths = SiteVisit::where('visited_on', '>=', $since)
            ->select('path', DB::raw('count(*) as visits'))
            ->groupBy('path')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $topReferrers = SiteVisit::where('visited_on', '>=', $since)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', DB::raw('count(*) as visits'))
            ->groupBy('referrer')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $topLiked = Media::withCount('likes')
            ->whereNull('trashed_at')
            ->having('likes_count', '>', 0)
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get();

        $topViewed = Media::withCount('views')
            ->whereNull('trashed_at')
            ->having('views_count', '>', 0)
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();

        return view('admin.stats.index', [
            'totalVisits' => $totalVisits,
            'totalUniqueVisitors' => $totalUniqueVisitors,
            'periodVisits' => $periodVisits,
            'periodUniqueVisitors' => $periodUniqueVisitors,
            'totalLikes' => MediaLike::count(),
            'totalViews' => MediaView::count(),
            'days' => $days,
            'topPaths' => $topPaths,
            'topReferrers' => $topReferrers,
            'topLiked' => $topLiked,
            'topViewed' => $topViewed,
            'periodDays' => self::PERIOD_DAYS,
        ]);
    }
}
