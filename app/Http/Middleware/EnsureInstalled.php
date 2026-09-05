<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force le passage par l'installateur tant que Focale n'a pas été installé
 * (comptable admin + base de données configurée) — comme WordPress.
 */
class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! file_exists(storage_path('app/installed.lock'))
            && ! $request->is('installation', 'installation/*', 'up')) {
            return redirect('/installation');
        }

        return $next($request);
    }
}
