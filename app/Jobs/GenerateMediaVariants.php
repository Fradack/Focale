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
     * Largeur maximale (px) de chaque variante. L'image n'est jamais agrandie.
     */
    private const SIZES = [
        'thumbnail' => 400,
        'web' => 1600,
        'retina' => 2400,
    ];

    public function __construct(public Media $media) {}

    public function handle(): void
    {
        $original = Storage::disk('media')->path($this->media->disk_path);
        $manager = new ImageManager(Driver::class);

        foreach (self::SIZES as $type => $maxWidth) {
            $image = $manager->decodePath($original);

            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            $encoded = $image->encode(new WebpEncoder(quality: 82));
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
}
