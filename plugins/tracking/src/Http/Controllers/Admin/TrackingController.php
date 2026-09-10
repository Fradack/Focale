<?php

namespace App\Plugins\Tracking\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Plugins\Tracking\Models\TrackingVisit;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Nombre de nuances de la rampe séquentielle utilisée pour la mini-carte
     * (un seul ton, clair -> foncé — voir world-map.blade.php et le
     * <style> généré dans index.blade.php pour les valeurs de couleur).
     */
    private const MAP_BUCKETS = 5;

    public function index(): View
    {
        $byCountry = TrackingVisit::query()
            ->whereNotNull('country_code')
            ->selectRaw('country_code, max(country) as country_name, count(*) as total')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->get();

        return view('plugins.tracking.admin.index', [
            'recent' => TrackingVisit::latest('created_at')->limit(100)->get(),
            'byDevice' => TrackingVisit::selectRaw('device_type, count(*) as total')
                ->groupBy('device_type')
                ->pluck('total', 'device_type'),
            'byOs' => TrackingVisit::selectRaw('os, count(*) as total')
                ->groupBy('os')
                ->orderByDesc('total')
                ->pluck('total', 'os'),
            'byCountry' => $byCountry,
            'countryBuckets' => $this->bucketCountries($byCountry),
            'total' => TrackingVisit::count(),
        ]);
    }

    /**
     * Répartit les pays en {self::MAP_BUCKETS} paliers d'intensité,
     * proportionnellement à leur part du pays le plus visité — la mini-carte
     * et la légende « Top pays » colorent ensuite chaque palier avec la même
     * rampe séquentielle à teinte unique (voir le <style> de index.blade.php).
     *
     * @return array<string, int> code pays en minuscule => palier (1..N)
     */
    private function bucketCountries(Collection $byCountry): array
    {
        $max = (int) $byCountry->max('total');

        if ($max <= 0) {
            return [];
        }

        return $byCountry
            ->mapWithKeys(function ($row) use ($max) {
                $bucket = (int) ceil(($row->total / $max) * self::MAP_BUCKETS);

                return [strtolower($row->country_code) => max(1, min(self::MAP_BUCKETS, $bucket))];
            })
            ->all();
    }
}
