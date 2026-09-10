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
     * juste plus lentement. Retourne null seulement si rien n'a été traité
     * du tout sur les dernières 24h (traitement réellement à l'arrêt).
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

        return $completed > 0 ? $completed / $windowMinutes : null;
    }
}
