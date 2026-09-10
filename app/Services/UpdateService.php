<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use PDO;
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
        // Un simple rafraîchissement/fermeture d'onglet côté navigateur ne
        // doit jamais pouvoir interrompre l'échange de dossiers ci-dessous en
        // plein milieu — ça laisserait l'application dans un état à moitié
        // mis à jour, bien plus cassé qu'un message d'erreur. ignore_user_abort
        // fait continuer le script même si le client se déconnecte ;
        // set_time_limit(0) évite qu'une limite d'exécution par défaut trop
        // courte (fréquente en hébergement mutualisé) ne coupe le script au
        // même endroit pour une autre raison.
        ignore_user_abort(true);
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        // Toujours revérifier plutôt que réutiliser le cache de 6h : appliquer
        // une mise à jour est une action rare et déclenchée volontairement,
        // elle ne doit jamais retélécharger une release déjà périmée.
        $update = $this->checkForUpdate(fresh: true);

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
        $publicBuildSwapped = false;

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

            // Les assets compilés (CSS/JS) ne sont pas dans les UPDATABLE_PATHS
            // ci-dessus (ils ne vivent pas sous base_path() sur un déploiement
            // où public/ est hébergé ailleurs — voir bootstrap/public_path.php),
            // mais ils font bien partie de la release et doivent être remplacés
            // à chaque mise à jour, sinon le style ne suit jamais le code.
            $publicBuildSource = $releaseRoot.'/public/build';
            if (File::exists($publicBuildSource)) {
                if (File::exists(public_path('build'))) {
                    File::moveDirectory(public_path('build'), public_path('build').'.old', true);
                    $publicBuildSwapped = true;
                }

                File::copyDirectory($publicBuildSource, public_path('build'));
            }
        } catch (\Throwable $e) {
            foreach ($swapped as $path) {
                File::deleteDirectory(base_path($path));
                File::moveDirectory(base_path($path).'.old', base_path($path));
            }

            if ($publicBuildSwapped) {
                File::deleteDirectory(public_path('build'));
                File::moveDirectory(public_path('build').'.old', public_path('build'));
            }

            throw $e;
        }

        // bootstrap/public_path.php (voir bootstrap/app.php) est un fichier propre
        // à ce déploiement, jamais inclus dans une release : on le restaure après
        // le remplacement du dossier bootstrap/, sinon un hébergement où public/
        // vit hors de la racine de l'app perdrait ce réglage à chaque mise à jour.
        $oldPublicPathOverride = base_path('bootstrap').'.old/public_path.php';
        if (in_array('bootstrap', $swapped, true) && File::exists($oldPublicPathOverride)) {
            File::copy($oldPublicPathOverride, base_path('bootstrap/public_path.php'));
        }

        foreach ($swapped as $path) {
            File::deleteDirectory(base_path($path).'.old');
        }

        if ($publicBuildSwapped) {
            File::deleteDirectory(public_path('build').'.old');
        }

        File::delete($tmpZip);
        File::deleteDirectory($tmpExtract);

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        $this->updateVersionConfig($update['latest']);
        Cache::forget('focale.update_check');

        Setting::set('last_update_version', $update['latest']);
        Setting::set('last_update_name', $update['name']);
        Setting::set('last_update_notes', $update['notes']);
        Setting::set('last_update_published_at', $update['publishedAt']);
        Setting::set('last_update_html_url', $update['htmlUrl']);
        Setting::set('last_update_applied_at', now()->toIso8601String());
    }

    /**
     * Détails de la dernière mise à jour effectivement installée sur ce
     * déploiement, pour affichage en bas de la page Mises à jour. Vient du
     * réglage enregistré par applyUpdate() ; si absent (aucune mise à jour
     * appliquée depuis que ce suivi existe — par exemple juste après cette
     * fonctionnalité elle-même), on retombe sur la release GitHub
     * correspondant à la version actuellement installée, quand elle existe.
     *
     * @return array{version: string, name: ?string, notes: ?string, publishedAt: ?string, htmlUrl: ?string, appliedAt: ?string}|null
     */
    public function lastInstalledUpdate(): ?array
    {
        $version = Setting::get('last_update_version');

        if ($version && $version === config('focale.version')) {
            return [
                'version' => $version,
                'name' => Setting::get('last_update_name'),
                'notes' => Setting::get('last_update_notes'),
                'publishedAt' => Setting::get('last_update_published_at'),
                'htmlUrl' => Setting::get('last_update_html_url'),
                'appliedAt' => Setting::get('last_update_applied_at'),
            ];
        }

        return $this->fetchReleaseForCurrentVersion();
    }

    /**
     * @return array{version: string, name: ?string, notes: ?string, publishedAt: ?string, htmlUrl: ?string, appliedAt: ?string}|null
     */
    private function fetchReleaseForCurrentVersion(): ?array
    {
        $current = config('focale.version');

        $release = Cache::remember("focale.release_details.{$current}", now()->addDay(), function () use ($current) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['Accept' => 'application/vnd.github+json'])
                    ->get('https://api.github.com/repos/'.config('focale.update_repo')."/releases/tags/v{$current}");

                if (! $response->successful()) {
                    return null;
                }

                return [
                    'name' => $response->json('name'),
                    'notes' => $response->json('body'),
                    'published_at' => $response->json('published_at'),
                    'html_url' => $response->json('html_url'),
                ];
            } catch (\Throwable) {
                return null;
            }
        });

        if (! $release) {
            return null;
        }

        return [
            'version' => $current,
            'name' => $release['name'] ?? null,
            'notes' => $release['notes'] ?? null,
            'publishedAt' => $release['published_at'] ?? null,
            'htmlUrl' => $release['html_url'] ?? null,
            'appliedAt' => null,
        ];
    }

    private function backup(): void
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);
        $timestamp = now()->format('Y-m-d_His');

        $this->dumpDatabase("{$dir}/db-{$timestamp}.sql");
        $this->zipCodebase("{$dir}/code-{$timestamp}.zip");
    }

    /**
     * Dump SQL en PHP pur (via PDO), sans dépendre du binaire `mysqldump` ni
     * de `proc_open` : beaucoup d'hébergements mutualisés désactivent
     * l'exécution de processus externes pour des raisons de sécurité.
     */
    private function dumpDatabase(string $path): void
    {
        $pdo = DB::connection()->getPdo();
        $database = config('database.connections.'.config('database.default').'.database');

        $handle = fopen($path, 'w');
        fwrite($handle, "-- Focale backup — {$database} — ".now()->toDateTimeString()."\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            $createSql = $createRow['Create Table'] ?? null;

            if (! $createSql) {
                continue;
            }

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n");

            $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            if ($count === 0) {
                continue;
            }

            $batchSize = 500;
            $columns = null;

            for ($offset = 0; $offset < $count; $offset += $batchSize) {
                $rows = $pdo->query("SELECT * FROM `{$table}` LIMIT {$batchSize} OFFSET {$offset}")
                    ->fetchAll(PDO::FETCH_ASSOC);

                if (! $rows) {
                    break;
                }

                $columns ??= array_map(fn ($c) => "`{$c}`", array_keys($rows[0]));

                $values = array_map(
                    fn ($row) => '('.implode(',', array_map(
                        fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v),
                        $row
                    )).')',
                    $rows
                );

                fwrite($handle, "INSERT INTO `{$table}` (".implode(',', $columns).") VALUES\n".implode(",\n", $values).";\n\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
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
