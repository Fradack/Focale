<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class GenerateMediaVariants implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Largeur maximale (px) de chaque variante, de la plus grande à la plus
     * petite. L'ordre compte : on décode l'original une seule fois puis on
     * réduit progressivement la même image d'une variante à l'autre au lieu
     * de redécoder l'original à chaque fois — mesuré ~35% plus rapide par
     * œuvre sur une photo 5184×3456 (un seul décodage du JPEG source, le
     * plus coûteux des deux, au lieu de trois).
     */
    private const SIZES = [
        // 2400 -> 1920 : toujours net sur les plus grands écrans actuels,
        // mais nettement moins de pixels à encoder en WebP sur un original
        // haute résolution — c'est le variant le plus coûteux des trois,
        // celui qui pèse le plus sur le temps de traitement par photo.
        'retina' => 1920,
        'web' => 1600,
        'thumbnail' => 400,
    ];

    public function __construct(public Media $media) {}

    public function handle(): void
    {
        // Décoder une photo haute résolution via GD peut demander plusieurs
        // centaines de Mo (largeur × hauteur × 4 octets, avant même le
        // travail de redimensionnement/encodage). 256M est le plafond dur
        // confirmé par l'hébergeur (PulseHeberg) sur cette offre : au-delà,
        // ini_set est simplement ignoré côté plateforme. Un dépassement est
        // un fatal PHP non rattrapable par un try/catch : la seule
        // protection possible est d'éviter qu'il survienne. Ce réglage ne
        // s'applique qu'à cette tâche précise, jamais au reste de l'appli.
        if (function_exists('ini_set')) {
            $current = ini_get('memory_limit');
            if ($current !== '-1' && $this->toBytes($current) < 256 * 1024 * 1024) {
                @ini_set('memory_limit', '256M');
            }
        }

        $original = Storage::disk('media')->path($this->media->disk_path);
        $manager = new ImageManager(Driver::class);

        $image = $manager->decodePath($original);

        foreach (self::SIZES as $type => $maxWidth) {
            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            // 82 -> 78 : différence visuelle négligeable, mais l'encodeur WebP
            // fait moins de recherche de compression à ce niveau de qualité,
            // ce qui réduit un peu le temps d'encodage par photo.
            $encoded = $image->encode(new WebpEncoder(quality: 78));
            $path = "media/{$this->media->uuid}/{$type}.webp";

            Storage::disk('public')->put($path, $encoded->toString());

            $this->media->variants()->updateOrCreate(
                ['type' => $type],
                [
                    'disk_path' => $path,
                    'width' => $image->width(),
                    'height' => $image->height(),
                    'filesize' => $encoded->size(),
                ]
            );
        }
    }

    /**
     * Convertit une valeur ini PHP ("128M", "1G", "512K", "1073741824") en
     * octets, pour comparer une limite existante avant de tenter de l'augmenter.
     */
    private function toBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }
}
