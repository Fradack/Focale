<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identifiant anonyme par navigateur (cookie longue durée, aucune IP, aucune
 * empreinte matérielle) — utilisé pour les likes, le compteur de vues et les
 * statistiques de visite. Plusieurs personnes d'un même foyer, chacune sur
 * son propre navigateur, obtiennent chacune leur propre identifiant.
 */
class AssignVisitorId
{
    public const COOKIE = 'focale_visitor';

    private const LIFETIME_MINUTES = 60 * 24 * 365 * 2;

    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->cookie(self::COOKIE);
        $isNew = ! $id;

        if ($isNew) {
            $id = (string) Str::uuid();
        }

        $request->attributes->set('visitor_id', $id);

        $response = $next($request);

        if ($isNew) {
            $response->headers->setCookie(cookie(self::COOKIE, $id, self::LIFETIME_MINUTES));
        }

        return $response;
    }
}
