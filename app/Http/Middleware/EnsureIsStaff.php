<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque l'accès à /administration aux comptes client (inscription publique
 * cachée sous /compte) — appliqué après 'auth' sur le groupe de routes admin.
 * Sans ce garde-fou, n'importe quel compte authentifié (client compris)
 * pourrait atteindre l'administration : 'auth' vérifie seulement qu'on est
 * connecté, jamais le rôle.
 */
class EnsureIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isStaff(), 403);

        return $next($request);
    }
}
