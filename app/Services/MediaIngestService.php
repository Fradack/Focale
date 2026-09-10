<?php

namespace App\Services;

use App\Jobs\GenerateMediaVariants;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaIngestService
{
    private const IMPORT_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm'];

    /**
     * Nombre max. de fichiers traités par appel, pour rester dans le temps
     * d'exécution autorisé sur un hébergement mutualisé — un gros dépôt FTP
     * (des centaines de fichiers) se vide donc en plusieurs passages plutôt
     * qu'en un seul, chaque clic (ou sondage automatique) avançant d'un cran.
     */
    private const IMPORT_FOLDER_BATCH = 20;

    private const IMPORT_FOLDER_SECONDS = 20;

    /**
     * Dossier où déposer des fichiers par FTP/SFTP pour les gros transferts,
     * sans passer par l'envoi navigateur (utile pour des centaines de
     * photos d'un coup, ou un hébergement où l'upload HTTP est trop lent/
     * limité). Jamais servi publiquement : sous storage/app, hors webroot.
     */
    public static function importFolderPath(): string
    {
        return storage_path('app/import');
    }

    /**
     * @return array{imported: int, duplicates: int, remaining: int}
     */
    public function ingestFromFolder(): array
    {
        $importDir = self::importFolderPath();
        $doneDir = $importDir.'/importes';
        File::ensureDirectoryExists($importDir);
        File::ensureDirectoryExists($doneDir);

        $files = collect(File::files($importDir))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), self::IMPORT_EXTENSIONS, true))
            ->values();

        $imported = 0;
        $duplicates = 0;
        $deadline = microtime(true) + self::IMPORT_FOLDER_SECONDS;

        foreach ($files as $file) {
            if ($imported + $duplicates >= self::IMPORT_FOLDER_BATCH || microtime(true) > $deadline) {
                break;
            }

            $uploaded = new UploadedFile(
                $file->getPathname(),
                $file->getFilename(),
                File::mimeType($file->getPathname()) ?: null,
                null,
                true
            );

            $result = $this->ingest($uploaded);
            $result['duplicate'] ? $duplicates++ : $imported++;

            // Déplacé (pas supprimé) après import : on garde une trace de ce
            // qui a déjà été traité plutôt que de le faire disparaître.
            File::move($file->getPathname(), $doneDir.'/'.$file->getFilename());
        }

        $remaining = collect(File::files($importDir))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), self::IMPORT_EXTENSIONS, true))
            ->count();

        return ['imported' => $imported, 'duplicates' => $duplicates, 'remaining' => $remaining];
    }

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
        $isVideo = str_starts_with((string) $file->getMimeType(), 'video/');

        // Les images restent sur le disque privé "media" (originaux jamais
        // servis directement — voir GenerateMediaVariants qui en dérive des
        // variantes publiques). Une vidéo n'a pas de variante dérivée : elle
        // doit être servie telle quelle, donc directement sur le disque
        // "public" plutôt que passer par un disque privé sans jamais en sortir.
        $diskPath = $isVideo ? "videos/{$uuid}.{$extension}" : "originals/{$uuid}.{$extension}";
        Storage::disk($isVideo ? 'public' : 'media')->put($diskPath, file_get_contents($file->getRealPath()));

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

        if ($isVideo) {
            // Pas de traitement asynchrone pour une vidéo (pas de redimensionnement
            // possible sans ffmpeg, absent de cet environnement) : on crée tout de
            // suite les 3 lignes de variantes attendues par MediaProcessingStatus
            // (sinon une vidéo resterait indéfiniment "en cours de traitement"),
            // pointant vers le fichier vidéo lui-même. L'affichage public ignore
            // ces lignes pour les vidéos et rend un <video> ou une tuile dédiée
            // à la place — voir Media::isVideo()/sourceUrl().
            foreach (['thumbnail', 'web', 'retina'] as $type) {
                $media->variants()->create([
                    'type' => $type,
                    'disk_path' => $diskPath,
                    'width' => 0,
                    'height' => 0,
                    'filesize' => $file->getSize(),
                ]);
            }
        } else {
            GenerateMediaVariants::dispatch($media);
        }

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
