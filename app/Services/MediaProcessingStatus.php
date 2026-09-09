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
     * Estime le temps restant à partir du débit observé ces dernières minutes
     * (nombre d'œuvres ayant terminé leurs 3 variantes). Retourne null tant
     * qu'on n'a pas assez de données récentes pour estimer un débit fiable.
     */
    private static function estimateEtaMinutes(int $pending): ?int
    {
        $recentlyCompleted = DB::table('media_variants')
            ->select('media_id')
            ->where('created_at', '>=', now()->subMinutes(self::RATE_WINDOW_MINUTES))
            ->groupBy('media_id')
            ->havingRaw('COUNT(*) >= ?', [self::EXPECTED_VARIANTS])
            ->get()
            ->count();

        if ($recentlyCompleted <= 0) {
            return null;
        }

        $rate = $recentlyCompleted / self::RATE_WINDOW_MINUTES;

        return (int) ceil($pending / $rate);
    }
}
