<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Services\IpCountryResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restriction d'accès au site public — voir /administration/reglages. Deux
 * mécanismes indépendants, l'un pouvant être actif sans l'autre :
 * - géographique : liste noire (bloque les pays cochés) ou liste blanche
 *   (n'autorise que les pays cochés). Désactivée par défaut.
 * - robots : bloque toute requête dont l'en-tête User-Agent ressemble à un
 *   bot/crawler/script (voir isBot()) — délibérément SANS exception pour les
 *   moteurs de recherche légitimes (Googlebot, Bingbot…) à la demande
 *   explicite de l'éditeur du site, qui accepte la conséquence (disparition
 *   progressive des résultats de recherche). Désactivé par défaut.
 *
 * Discipline "fail open" stricte pour le volet géographique, à l'image de
 * App\Support\Plugins et de l'incident de production du plugin Tracking
 * (classe référencée avant d'être installée → 500 sur tout le site) :
 * aucune panne possible ici ne doit jamais bloquer un visiteur ni, a
 * fortiori, l'équipe du site.
 * - Un utilisateur authentifié (staff ou client) passe toujours, quel que
 *   soit le mode : ce ne sont pas des visiteurs anonymes.
 * - Le mode géographique "désactivé" (valeur par défaut/absente)
 *   court-circuite avant même de tenter une résolution (aucun appel API
 *   superflu).
 * - Un pays non résolu (échec de l'API, timeout, réponse inattendue) est
 *   toujours autorisé, dans les deux modes. Voir
 *   Admin\SettingController::testGeo() pour diagnostiquer un échec de
 *   détection (ex. hébergement bloquant les connexions sortantes).
 * - La détection de robot, elle, est une simple comparaison de texte locale
 *   (aucun appel réseau) : rien à faire échouer "ouvert", elle bloque
 *   simplement quand le motif correspond.
 */
class EnsureCountryAllowed
{
    /**
     * Motifs identifiant un robot/crawler/script dans le User-Agent —
     * volontairement large (inclut les moteurs de recherche légitimes) pour
     * satisfaire un blocage "tous les robots sans exception".
     */
    private const BOT_UA_PATTERN = '/bot|crawl|spider|slurp|fetch|curl|wget|python-requests|python-urllib|scrapy|headless|phantomjs|puppeteer|playwright|facebookexternalhit|whatsapp|telegrambot|discordbot|embedly|quora link preview|outbrain|pinterest|preview|monitor|pingdom|uptime|ahrefs|semrush|mj12bot|dotbot|petalbot|bingpreview|yandex|baiduspider|duckduckbot|archive\.org_bot|ia_archiver/i';

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
            if (Setting::get('bot_restriction_enabled', '0') === '1' && $this->isBot($request)) {
                return true;
            }

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

    private function isBot(Request $request): bool
    {
        $userAgent = trim((string) $request->userAgent());

        // Un vrai navigateur envoie toujours un User-Agent : son absence
        // est elle-même un signal fort de script/bot.
        if ($userAgent === '') {
            return true;
        }

        return (bool) preg_match(self::BOT_UA_PATTERN, $userAgent);
    }
}
