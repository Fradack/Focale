<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use App\Support\Visitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journal des pages publiques vues, pour le compteur de visiteurs uniques et
 * les statistiques de trafic — jamais posé sur les routes d'administration.
 */
class LogSiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get') && ! $request->user()) {
            SiteVisit::create([
                'visitor_id' => Visitor::id(),
                'path' => $request->path(),
                'referrer' => substr((string) $request->headers->get('referer'), 0, 500) ?: null,
                'visited_on' => now()->toDateString(),
            ]);
        }

        return $next($request);
    }
}
