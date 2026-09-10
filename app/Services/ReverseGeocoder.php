<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Résout des coordonnées GPS en nom de lieu lisible (commune, pays) via
 * Nominatim (OpenStreetMap) — gratuit, sans clé, mais soumis à sa politique
 * d'usage (1 requête/seconde max, User-Agent identifiable obligatoire).
 * N'est appelé qu'à l'import d'une photo géolocalisée, jamais à l'affichage,
 * ce qui reste largement sous cette limite même en import en rafale (le
 * traitement des photos est de toute façon séquentiel, voir
 * MediaIngestService). Échoue toujours silencieusement : une erreur ou une
 * indisponibilité de service ne doit jamais empêcher l'import d'une photo,
 * juste laisser son lieu retomber sur les coordonnées brutes (voir
 * Media::displayLocation()).
 */
class ReverseGeocoder
{
    public function communeFor(float $lat, float $lng): ?string
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'Focale-CMS-Photo (import reverse geocoding)'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => 10,
                    'accept-language' => 'fr',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $address = $response->json('address', []);

            $commune = $address['city'] ?? $address['town'] ?? $address['village']
                ?? $address['municipality'] ?? $address['county'] ?? null;

            if (! $commune) {
                return null;
            }

            $country = $address['country'] ?? null;

            return $country ? "{$commune}, {$country}" : $commune;
        } catch (\Throwable) {
            return null;
        }
    }
}
