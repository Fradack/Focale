<?php

namespace App\Services;

use App\Jobs\GenerateMediaVariants;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaIngestService
{
    /**
     * @return array{media: Media, duplicate: bool}
     */
    public function ingest(UploadedFile $file): array
    {
        $checksum = hash_file('sha256', $file->getRealPath());

        $existing = Media::where('checksum', $checksum)->whereNull('trashed_at')->first();
        if ($existing) {
            return ['media' => $existing, 'duplicate' => true];
        }

        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $diskPath = "originals/{$uuid}.{$extension}";

        Storage::disk('media')->put($diskPath, file_get_contents($file->getRealPath()));

        [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

        $exif = $this->readExif($file->getRealPath(), $file->getMimeType());

        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $media = Media::create([
            'uuid' => $uuid,
            'title' => $title,
            'slug' => $this->uniqueSlug($title),
            'mime_type' => $file->getMimeType(),
            'disk_path' => $diskPath,
            'filesize' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'checksum' => $checksum,
            'status' => 'draft',
            'taken_at' => $exif['taken_at'] ?? null,
            'gps_lat' => $exif['gps_lat'] ?? null,
            'gps_lng' => $exif['gps_lng'] ?? null,
            'exif' => $exif['raw'] ?? null,
        ]);

        GenerateMediaVariants::dispatch($media);

        return ['media' => $media, 'duplicate' => false];
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'oeuvre';
        $slug = $base;
        $i = 1;

        while (Media::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * @return array{taken_at: ?string, gps_lat: ?float, gps_lng: ?float, raw: ?array}
     */
    private function readExif(string $path, ?string $mimeType): array
    {
        if ($mimeType !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return ['taken_at' => null, 'gps_lat' => null, 'gps_lng' => null, 'raw' => null];
        }

        $data = @exif_read_data($path, 'ANY_TAG', true);
        if (! $data) {
            return ['taken_at' => null, 'gps_lat' => null, 'gps_lng' => null, 'raw' => null];
        }

        $ifd0 = $data['IFD0'] ?? [];
        $exif = $data['EXIF'] ?? [];
        $gpsRaw = $data['GPS'] ?? [];

        $takenAt = $exif['DateTimeOriginal'] ?? $ifd0['DateTime'] ?? null;
        $takenAt = $takenAt ? str_replace(':', '-', substr($takenAt, 0, 10)).substr($takenAt, 10) : null;

        $lat = $this->gpsToDecimal($gpsRaw['GPSLatitude'] ?? null, $gpsRaw['GPSLatitudeRef'] ?? null);
        $lng = $this->gpsToDecimal($gpsRaw['GPSLongitude'] ?? null, $gpsRaw['GPSLongitudeRef'] ?? null);

        $normalized = [
            'camera' => trim(($ifd0['Make'] ?? '').' '.($ifd0['Model'] ?? '')) ?: null,
            'lens' => $exif['UndefinedTag:0xA434'] ?? null,
            'focal_length' => isset($exif['FocalLength']) ? $this->evalFraction($exif['FocalLength']).' mm' : null,
            'aperture' => isset($exif['FNumber']) ? 'f/'.round($this->evalFraction($exif['FNumber']), 1) : null,
            'shutter_speed' => $exif['ExposureTime'] ?? null,
            'iso' => $exif['ISOSpeedRatings'] ?? null,
        ];

        return [
            'taken_at' => $takenAt,
            'gps_lat' => $lat,
            'gps_lng' => $lng,
            'raw' => $normalized,
        ];
    }

    private function evalFraction(string $fraction): float
    {
        if (! str_contains($fraction, '/')) {
            return (float) $fraction;
        }

        [$num, $den] = explode('/', $fraction);

        return (float) $den !== 0.0 ? (float) $num / (float) $den : 0.0;
    }

    private function gpsToDecimal(?array $coordinate, ?string $hemisphere): ?float
    {
        if (! $coordinate || count($coordinate) !== 3) {
            return null;
        }

        $degrees = $this->evalFraction($coordinate[0]);
        $minutes = $this->evalFraction($coordinate[1]);
        $seconds = $this->evalFraction($coordinate[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        if (in_array($hemisphere, ['S', 'W'], true)) {
            $decimal *= -1;
        }

        return round($decimal, 7);
    }
}
