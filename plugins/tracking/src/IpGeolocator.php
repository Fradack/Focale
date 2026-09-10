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
     * @return array{commune: ?string, country: ?string}|null
     */
    public function locate(string $ip): ?array
    {
        try {
            $response = Http::timeout(3)
                ->withHeaders(['User-Agent' => 'Focale-CMS-Photo (plugin tracking)'])
                ->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,city,country']);

            if (! $response->successful() || $response->json('status') !== 'success') {
                return null;
            }

            return [
                'commune' => $response->json('city'),
                'country' => $response->json('country'),
            ];
        } catch (\Throwable) {
            return null;
        }
    }
}
