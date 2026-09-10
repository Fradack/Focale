<?php

namespace App\Http\Middleware;

use App\Support\Plugins;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque le cœur/like anonyme tant que le plugin "likes" n'est pas activé
 * depuis /administration/plugins — même principe que EnsureShopEnabled.
 */
class EnsureLikesEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Plugins::enabled('likes'), 404);

        return $next($request);
    }
}
