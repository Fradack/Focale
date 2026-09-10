<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Services\IpCountryResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restriction géographique du site public — voir /administration/reglages.
 * Deux modes configurables : liste noire (bloque les pays cochés) ou liste
 * blanche (n'autorise que les pays cochés). Désactivé par défaut.
 *
 * Discipline "fail open" stricte, à l'image de App\Support\Plugins et de
 * l'incident de production du plugin Tracking (classe référencée avant
 * d'être installée → 500 sur tout le site) : aucune panne possible ici ne
 * doit jamais bloquer un visiteur ni, a fortiori, l'équipe du site.
 * - Un utilisateur authentifié (staff ou client) passe toujours, quel que
 *   soit le mode : ce ne sont pas des visiteurs anonymes.
 * - Le mode "désactivé" (valeur par défaut/absente) court-circuite avant
 *   même de tenter une résolution géographique (aucun appel API superflu).
 * - Un pays non résolu (échec de l'API, timeout, réponse inattendue) est
 *   toujours autorisé, dans les deux modes.
 */
class EnsureCountryAllowed
{
    public function __construct(private readonly IpCountryResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null) {
            return $next($request);
        }

        if ($this->shouldBlock($request)) {
            return response()->view('public.country-blocked', [], 403);
        }

        return $next($request);
    }

    /**
     * Toute la logique de décision est isolée ici, sous un seul try/catch :
     * une panne de la table `settings`, du cache ou de l'appel HTTP externe
     * ne doit jamais empêcher le traitement normal de la requête (voir
     * App\Support\Plugins pour le même idiome).
     */
    private function shouldBlock(Request $request): bool
    {
        try {
            $mode = Setting::get('country_restriction_mode', 'disabled');

            if ($mode !== 'blocklist' && $mode !== 'allowlist') {
                return false;
            }

            $country = $this->resolver->resolve((string) $request->ip());

            if ($country === null) {
                return false;
            }

            $countries = json_decode(Setting::get('country_restriction_countries') ?? '[]', true);

            if (! is_array($countries)) {
                $countries = [];
            }

            $inList = in_array($country, $countries, true);

            return $mode === 'blocklist' ? $inList : ! $inList;
        } catch (\Throwable) {
            return false;
        }
    }
}
