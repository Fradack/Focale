<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque l'accès à l'installateur une fois Focale déjà installé, pour
 * qu'un attaquant ne puisse pas rejouer /installation sur un site en prod.
 */
class EnsureNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path('app/installed.lock'))) {
            return redirect('/');
        }

        return $next($request);
    }
}
