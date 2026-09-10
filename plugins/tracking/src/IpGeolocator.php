<?php

namespace App\Plugins\Tracking;

use Illuminate\Support\Facades\Http;

/**
 * Géolocalisation approximative (commune, pays) déduite de l'adresse IP —
 * jamais de position précise/GPS. Même idiome défensif que
 * App\Services\ReverseGeocoder : API gratuite non authentifiée,
 * User-Agent identifiant, timeout court, null silencieux sur tout échec.
 * Classe isolée pour rester facilement remplaçable si le service change.
 */
class IpGeolocator
{
    /**
     * @return array{commune: ?string, country: ?string, country_code: ?string}|null
     */
    public function locate(string $ip): ?array
    {
        try {
            // HTTPS uniquement — ip-api.com n'autorise le HTTP simple (port
            // 80) que sur son offre gratuite, ce qui échoue silencieusement
            // sur un hébergement qui bloque les connexions sortantes sur ce
            // port (cas constaté en production sur ce même besoin pour la
            // restriction géographique, voir App\Services\IpCountryResolver).
            $response = Http::timeout(3)
                ->withHeaders(['User-Agent' => 'Focale-CMS-Photo (plugin tracking)'])
                ->get("https://ipwho.is/{$ip}", ['fields' => 'success,city,country,country_code']);

            if (! $response->successful() || $response->json('success') !== true) {
                return null;
            }

            return [
                'commune' => $response->json('city'),
                'country' => $response->json('country'),
                'country_code' => $response->json('country_code'),
            ];
        } catch (\Throwable) {
            return null;
        }
    }
}
