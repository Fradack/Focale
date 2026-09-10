<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MediaProcessingStatus
{
    /**
     * Nombre de variantes (thumbnail/web/retina) qu'une œuvre doit avoir
     * pour être considérée comme entièrement traitée.
     */
    private const EXPECTED_VARIANTS = 3;

    private const RATE_WINDOW_MINUTES = 10;

    /**
     * En dessous de ce nombre d'œuvres terminées dans la fenêtre observée,
     * le débit est trop bruité pour être extrapolé de façon fiable — 1 ou 2
     * achèvements espacés (le temps d'attente entre deux chargements de page
     * admin compte dans l'écart, pas seulement le vrai temps de traitement)
     * peuvent donner un débit artificiellement lent et donc un ETA absurde
     * pour un tout petit nombre d'œuvres restantes (ex. "~5 min" pour 1 seule
     * photo). Mieux vaut élargir la fenêtre, ou renoncer à l'estimation,
     * plutôt qu'afficher un chiffre précis mais faux.
     */
    private const MIN_SAMPLE_SIZE = 3;

    /**
     * @return array{total: int, processed: int, pending: int, percent: int, eta_minutes: ?int}
     */
    public static function global(): array
    {
        return self::forQuery(Media::query()->whereNull('trashed_at'));
    }

    /**
     * @return array{total: int, processed: int, pending: int, percent: int, eta_minutes: ?int}
     */
    public static function forAlbum(int $albumId): array
    {
        return self::forQuery(
            Media::query()
                ->whereNull('trashed_at')
                ->whereHas('albums', fn ($q) => $q->where('albums.id', $albumId))
        );
    }

    /**
     * @return array{total: int, processed: int, pending: int, percent: int, eta_minutes: ?int}
     */
    public static function forQuery(Builder $query): array
    {
        $total = (clone $query)->count();
        $processed = (clone $query)->has('variants', '>=', self::EXPECTED_VARIANTS)->count();
        $pending = max(0, $total - $processed);

        return [
            'total' => $total,
            'processed' => $processed,
            'pending' => $pending,
            'percent' => $total > 0 ? (int) round($processed / $total * 100) : 100,
            'eta_minutes' => $pending > 0 ? self::estimateEtaMinutes($pending) : 0,
        ];
    }

    /**
     * Estime le temps restant à partir du débit observé (nombre d'œuvres
     * ayant terminé leurs 3 variantes par minute). La fenêtre de 10 minutes
     * donne le débit le plus à jour, mais si rien ne s'est terminé pendant
     * cette fenêtre précise (traitement lent, ou juste malchance sur le
     * moment du sondage), on élargit progressivement plutôt que d'afficher
     * indéfiniment « estimation en cours » alors que ça avance bel et bien,
     * juste plus lentement. Retourne null si moins de MIN_SAMPLE_SIZE œuvres
     * ont été traitées même sur 24h — pas assez de données pour extrapoler
     * sans produire un chiffre trompeur.
     */
    private static function estimateEtaMinutes(int $pending): ?int
    {
        foreach ([self::RATE_WINDOW_MINUTES, 60, 24 * 60] as $windowMinutes) {
            $rate = self::completionRatePerMinute($windowMinutes);

            if ($rate !== null) {
                return (int) ceil($pending / $rate);
            }
        }

        return null;
    }

    private static function completionRatePerMinute(int $windowMinutes): ?float
    {
        $completed = DB::table('media_variants')
            ->select('media_id')
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->groupBy('media_id')
            ->havingRaw('COUNT(*) >= ?', [self::EXPECTED_VARIANTS])
            ->get()
            ->count();

        return $completed >= self::MIN_SAMPLE_SIZE ? $completed / $windowMinutes : null;
    }
}
