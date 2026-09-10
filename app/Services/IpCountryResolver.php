<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Résout une adresse IP en code pays ISO 3166-1 alpha-2 via ip-api.com —
 * même idiome défensif que App\Services\ReverseGeocoder et le
 * IpGeolocator du plugin Tracking : API gratuite non authentifiée,
 * User-Agent identifiant, timeout court, null silencieux sur tout échec
 * (jamais d'exception qui remonterait jusqu'au middleware appelant).
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
                ->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,countryCode']);

            if (! $response->successful() || $response->json('status') !== 'success') {
                return null;
            }

            $countryCode = $response->json('countryCode');

            return is_string($countryCode) && $countryCode !== '' ? strtoupper($countryCode) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
