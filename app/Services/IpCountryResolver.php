<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Résout une adresse IP en code pays ISO 3166-1 alpha-2 via ipwho.is —
 * même idiome défensif que App\Services\ReverseGeocoder et le
 * IpGeolocator du plugin Tracking : API gratuite non authentifiée,
 * User-Agent identifiant, timeout court, null silencieux sur tout échec
 * (jamais d'exception qui remonterait jusqu'au middleware appelant).
 *
 * HTTPS uniquement (contrairement à ip-api.com, dont l'offre gratuite
 * n'autorise que du HTTP simple, port 80) : un hébergement mutualisé qui
 * bloque les connexions sortantes en port 80 — cas constaté en production —
 * ferait échouer silencieusement toute détection, et la restriction
 * géographique ne bloquerait alors plus jamais personne (voir le
 * diagnostic exposé dans Admin\SettingController::testGeo()).
 *
 * Résultat mis en cache 12h par IP : cette API gratuite est limitée en
 * fréquence (l'incident de production du plugin Tracking l'a déjà montré
 * avec un 429), et une IP visiteuse fait généralement plusieurs requêtes
 * de suite (page, images, etc.) qui ne doivent déclencher qu'un seul appel
 * sortant.
 */
class IpCountryResolver
{
    public function resolve(string $ip): ?string
    {
        try {
            return Cache::remember(
                "geo-country:{$ip}",
                now()->addHours(12),
                fn () => $this->lookup($ip)
            );
        } catch (\Throwable) {
            // Le cache lui-même peut échouer (store indisponible) : ne
            // jamais laisser ça faire tomber la requête du visiteur.
            return null;
        }
    }

    private function lookup(string $ip): ?string
    {
        try {
            $response = Http::timeout(3)
                ->withHeaders(['User-Agent' => 'Focale-CMS-Photo (restriction géographique)'])
                ->get("https://ipwho.is/{$ip}", ['fields' => 'success,country_code']);

            if (! $response->successful() || $response->json('success') !== true) {
                return null;
            }

            $countryCode = $response->json('country_code');

            return is_string($countryCode) && $countryCode !== '' ? strtoupper($countryCode) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
