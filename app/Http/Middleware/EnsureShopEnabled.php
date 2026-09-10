<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque entièrement la boutique (catalogue, fiches produit, panier) tant
 * qu'elle n'a pas été activée depuis /administration/reglages — pas
 * seulement le lien de navigation, pour éviter qu'une URL connue ne
 * contourne la désactivation.
 */
class EnsureShopEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Setting::get('shop_enabled')) {
            abort(404);
        }

        return $next($request);
    }
}
