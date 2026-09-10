<?php

namespace App\Services;

use App\Jobs\GenerateMediaVariants;
use App\Models\Media;
use App\Support\Plugins;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Édition d'image "en place" (recadrage, rotation) pour le plugin PhotoEdit
 * — images uniquement, jamais de vidéo. Garde toujours une sauvegarde de
 * l'original avant la toute première édition, pour pouvoir y revenir.
 */
class PhotoEditor
{
    /**
     * @param  array{x:int,y:int,width:int,height:int}|null  $crop
     */
    public function applyEdit(Media $media, ?array $crop, int $rotate): void
    {
        $this->guardIsImage($media);

        $disk = $this->originalDisk($media);

        if (! $media->original_backup_path) {
            $backupPath = 'originals-backup/'.basename($media->disk_path);
            Storage::disk($disk)->copy($media->disk_path, $backupPath);
            $media->original_backup_path = $backupPath;
        }

        $path = Storage::disk($disk)->path($media->disk_path);

        $manager = new ImageManager(Driver::class);
        $image = $manager->decodePath($path);

        if ($crop && $crop['width'] > 0 && $crop['height'] > 0) {
            $image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
        }

        if ($rotate % 360 !== 0) {
            $image->rotate(-$rotate);
        }

        // Ré-encode dans le même format que l'original (extension du fichier)
        // plutôt que de forcer un format — save() choisit l'encodeur d'après
        // l'extension du chemin de destination.
        $image->save($path);

        $media->width = $image->width();
        $media->height = $image->height();
        $media->save();

        $this->regenerateVariants($media);
    }

    public function revertToOriginal(Media $media): void
    {
        $this->guardIsImage($media);

        if (! $media->original_backup_path) {
            return;
        }

        $disk = $this->originalDisk($media);

        Storage::disk($disk)->copy($media->original_backup_path, $media->disk_path);

        [$width, $height] = @getimagesize(Storage::disk($disk)->path($media->disk_path)) ?: [$media->width, $media->height];

        Storage::disk($disk)->delete($media->original_backup_path);

        $media->original_backup_path = null;
        $media->width = $width;
        $media->height = $height;
        $media->save();

        $this->regenerateVariants($media);
    }

    private function guardIsImage(Media $media): void
    {
        if ($media->isVideo()) {
            throw new \RuntimeException('PhotoEdit ne prend en charge que les images, pas les vidéos.');
        }
    }

    /**
     * Le disque de l'original dépend du mode d'import : disque privé "media"
     * en temps normal, disque "public" quand le plugin NIP a importé ce
     * fichier tel quel (voir MediaIngestService::ingest()).
     */
    private function originalDisk(Media $media): string
    {
        return Storage::disk('media')->exists($media->disk_path) ? 'media' : 'public';
    }

    /**
     * En mode NIP, les 3 lignes de variantes pointent déjà directement vers
     * l'original — pas de vraies variantes WebP à régénérer, juste les
     * dimensions à mettre à jour sur ces mêmes lignes. Sinon, un simple
     * nouveau passage de GenerateMediaVariants recrée proprement les 3
     * variantes depuis l'original modifié (updateOrCreate).
     */
    private function regenerateVariants(Media $media): void
    {
        $webVariant = $media->variants()->where('type', 'web')->first();

        if (Plugins::enabled('nip') && $webVariant && $webVariant->disk_path === $media->disk_path) {
            $media->variants()->update(['width' => $media->width, 'height' => $media->height]);

            return;
        }

        GenerateMediaVariants::dispatchSync($media);
    }
}
