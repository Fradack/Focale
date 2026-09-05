<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use ZipArchive;

class UpdateService
{
    /**
     * Dossiers de l'app remplacés lors d'une mise à jour. Tout le reste
     * (storage/, .env, public/storage, public/build) n'est jamais touché.
     */
    private const UPDATABLE_PATHS = ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'vendor'];

    /**
     * @return array{current: string, latest: ?string, url: ?string, updateAvailable: bool, name: ?string, notes: ?string, publishedAt: ?string, htmlUrl: ?string, assetSize: ?int}
     */
    public function checkForUpdate(bool $fresh = false): array
    {
        $current = config('focale.version');

        if ($fresh) {
            Cache::forget('focale.update_check');
        }

        $release = Cache::remember('focale.update_check', now()->addHours(6), function () {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['Accept' => 'application/vnd.github+json'])
                    ->get('https://api.github.com/repos/'.config('focale.update_repo').'/releases/latest');

                if (! $response->successful()) {
                    return null;
                }

                $asset = collect($response->json('assets'))->first();

                return [
                    'tag' => ltrim($response->json('tag_name', ''), 'v'),
                    'name' => $response->json('name'),
                    'notes' => $response->json('body'),
                    'published_at' => $response->json('published_at'),
                    'html_url' => $response->json('html_url'),
                    'download_url' => $asset['browser_download_url'] ?? null,
                    'asset_size' => $asset['size'] ?? null,
                ];
            } catch (\Throwable) {
                return null;
            }
        });

        $latest = $release['tag'] ?? null;

        return [
            'current' => $current,
            'latest' => $latest,
            'url' => $release['download_url'] ?? null,
            'updateAvailable' => $latest && version_compare($latest, $current, '>'),
            'name' => $release['name'] ?? null,
            'notes' => $release['notes'] ?? null,
            'publishedAt' => $release['published_at'] ?? null,
            'htmlUrl' => $release['html_url'] ?? null,
            'assetSize' => $release['asset_size'] ?? null,
        ];
    }

    public function applyUpdate(): void
    {
        $update = $this->checkForUpdate();

        if (! $update['updateAvailable'] || ! $update['url']) {
            throw new \RuntimeException('Aucune mise à jour disponible.');
        }

        $this->backup();

        $tmpZip = storage_path('app/tmp-update.zip');
        $tmpExtract = storage_path('app/tmp-update-extract');

        File::put($tmpZip, Http::timeout(120)->get($update['url'])->body());

        File::deleteDirectory($tmpExtract);
        File::makeDirectory($tmpExtract, 0755, true);

        $zip = new ZipArchive;
        $zip->open($tmpZip);
        $zip->extractTo($tmpExtract);
        $zip->close();

        $releaseRoot = $this->findReleaseRoot($tmpExtract);
        $swapped = [];

        try {
            foreach (self::UPDATABLE_PATHS as $path) {
                $source = $releaseRoot.'/'.$path;
                if (! File::exists($source)) {
                    continue;
                }

                // On renomme l'ancien dossier plutôt que de le supprimer tout de
                // suite : en cas d'erreur plus loin, il reste possible de revenir
                // en arrière manuellement (voir aussi la sauvegarde zip ci-dessus).
                if (File::exists(base_path($path))) {
                    File::moveDirectory(base_path($path), base_path($path).'.old', true);
                    $swapped[] = $path;
                }

                File::copyDirectory($source, base_path($path));
            }
        } catch (\Throwable $e) {
            foreach ($swapped as $path) {
                File::deleteDirectory(base_path($path));
                File::moveDirectory(base_path($path).'.old', base_path($path));
            }

            throw $e;
        }

        foreach ($swapped as $path) {
            File::deleteDirectory(base_path($path).'.old');
        }

        File::delete($tmpZip);
        File::deleteDirectory($tmpExtract);

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        $this->updateVersionConfig($update['latest']);
        Cache::forget('focale.update_check');
    }

    private function backup(): void
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);
        $timestamp = now()->format('Y-m-d_His');

        $dbConfig = config('database.connections.'.config('database.default'));
        $dumpPath = "{$dir}/db-{$timestamp}.sql";

        // Process::run() ne redirige pas nativement stdout vers un fichier :
        // on passe par une commande shell complète pour le pipe "> fichier".
        $result = Process::run(sprintf(
            '%s -h %s -P %s -u %s --password=%s %s > %s',
            escapeshellarg(config('focale.mysqldump_path')),
            escapeshellarg($dbConfig['host']),
            escapeshellarg((string) $dbConfig['port']),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($dumpPath)
        ));
        $result->throw();

        $this->zipCodebase("{$dir}/code-{$timestamp}.zip");
    }

    private function zipCodebase(string $destination): void
    {
        $zip = new ZipArchive;
        $zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (self::UPDATABLE_PATHS as $path) {
            $root = base_path($path);
            if (! File::isDirectory($root)) {
                continue;
            }

            /** @var \SplFileInfo $file */
            foreach (File::allFiles($root) as $file) {
                $zip->addFile($file->getPathname(), $path.'/'.$file->getRelativePathname());
            }
        }

        $zip->close();
    }

    private function findReleaseRoot(string $extractPath): string
    {
        $entries = File::directories($extractPath);

        // Les archives GitHub extraient toujours dans un unique sous-dossier
        // nommé "depot-tag/".
        return count($entries) === 1 ? $entries[0] : $extractPath;
    }

    private function updateVersionConfig(string $version): void
    {
        $path = config_path('focale.php');
        $content = File::get($path);
        $content = preg_replace("/'version' => '[^']*'/", "'version' => '{$version}'", $content);
        File::put($path, $content);
    }
}
