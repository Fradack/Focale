<?php

namespace App\Services;

use App\Models\Plugin;
use App\Models\Setting;
use App\Support\GithubRelease;
use App\Support\Plugins;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Installe des plugins depuis le dossier plugins/ du dépôt GitHub
 * (Fradack/Focale, même dépôt que les releases) — réutilise le mécanisme de
 * téléchargement/extraction de UpdateService, mais copie des sous-dossiers
 * précis plutôt que de remplacer l'application entière.
 */
class PluginManager
{
    /**
     * @return array<string, array{slug:string,label:?string,description:?string,version:?string}>
     */
    public function listAvailableFromGithub(): array
    {
        return Cache::remember('focale.plugins_catalog', now()->addHours(6), function () {
            try {
                $repo = config('focale.update_repo');
                $response = Http::timeout(5)
                    ->withHeaders(['Accept' => 'application/vnd.github+json'])
                    ->get("https://api.github.com/repos/{$repo}/contents/plugins");

                if (! $response->successful()) {
                    return [];
                }

                $catalog = [];
                foreach ($response->json() as $entry) {
                    if (($entry['type'] ?? null) !== 'dir') {
                        continue;
                    }

                    $manifest = $this->fetchManifest($repo, $entry['name']);
                    if ($manifest) {
                        $catalog[$entry['name']] = $manifest;
                    }
                }

                return $catalog;
            } catch (\Throwable) {
                return [];
            }
        });
    }

    private function fetchManifest(string $repo, string $slug): ?array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['Accept' => 'application/vnd.github+json'])
                ->get("https://api.github.com/repos/{$repo}/contents/plugins/{$slug}/plugin.json");

            if (! $response->successful()) {
                return null;
            }

            $manifest = json_decode(base64_decode($response->json('content')), true);

            return is_array($manifest) ? $manifest : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Télécharge la dernière release GitHub (le zip contient déjà plugins/
     * puisqu'il vit dans le même dépôt) puis copie uniquement le sous-dossier
     * plugins/{slug} vers ses emplacements définitifs. N'active PAS le
     * plugin — étape distincte et volontaire (voir enable()), notamment pour
     * Tracking à cause du consentement RGPD.
     */
    public function install(string $slug): void
    {
        ignore_user_abort(true);
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $update = app(UpdateService::class)->checkForUpdate(fresh: true);
        if (! $update['url']) {
            throw new \RuntimeException('Impossible de récupérer la dernière release GitHub.');
        }

        $tmpZip = storage_path("app/tmp-plugin-{$slug}.zip");
        $tmpExtract = storage_path("app/tmp-plugin-{$slug}-extract");

        File::put($tmpZip, Http::timeout(120)->get($update['url'])->body());
        File::deleteDirectory($tmpExtract);
        File::makeDirectory($tmpExtract, 0755, true);

        $zip = new ZipArchive;
        $zip->open($tmpZip);
        $zip->extractTo($tmpExtract);
        $zip->close();

        $releaseRoot = GithubRelease::findExtractedRoot($tmpExtract);
        $source = "{$releaseRoot}/plugins/{$slug}";

        if (! File::exists($source)) {
            File::delete($tmpZip);
            File::deleteDirectory($tmpExtract);
            throw new \RuntimeException("Le plugin « {$slug} » n'existe pas dans cette release.");
        }

        $manifestPath = "{$source}/plugin.json";
        $manifest = File::exists($manifestPath) ? json_decode(File::get($manifestPath), true) : [];

        $studly = Str::studly($slug);
        $swappedDirs = [];
        $copiedFiles = [];

        try {
            $this->swapDirectory("{$source}/src", app_path("Plugins/{$studly}"), $swappedDirs);
            $this->swapDirectory("{$source}/views", resource_path("views/plugins/{$slug}"), $swappedDirs);

            $routesTarget = base_path("routes/plugins/{$slug}.php");
            if (File::exists("{$source}/routes.php")) {
                File::ensureDirectoryExists(base_path('routes/plugins'));
                File::copy("{$source}/routes.php", $routesTarget);
                $copiedFiles[] = $routesTarget;
            }

            foreach (File::glob("{$source}/migrations/*.php") as $migrationFile) {
                $target = database_path('migrations/'.basename($migrationFile));
                if (! File::exists($target)) {
                    File::copy($migrationFile, $target);
                    $copiedFiles[] = $target;
                }
            }
        } catch (\Throwable $e) {
            $this->rollback($swappedDirs, $copiedFiles);
            File::delete($tmpZip);
            File::deleteDirectory($tmpExtract);
            throw $e;
        }

        foreach ($swappedDirs as $target => $hadOld) {
            if ($hadOld) {
                File::deleteDirectory("{$target}.old");
            }
        }

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        Plugin::updateOrCreate(['slug' => $slug], [
            'label' => $manifest['label'] ?? $slug,
            'description' => $manifest['description'] ?? null,
            'version' => $manifest['version'] ?? null,
            'installed_at' => now(),
            'enabled' => false,
        ]);
        Plugins::forget($slug);

        File::delete($tmpZip);
        File::deleteDirectory($tmpExtract);
    }

    private function swapDirectory(string $source, string $target, array &$swapped): void
    {
        if (! File::exists($source)) {
            return;
        }

        if (File::exists($target)) {
            File::moveDirectory($target, "{$target}.old", true);
            $swapped[$target] = true;
        } else {
            $swapped[$target] = false;
        }

        File::copyDirectory($source, $target);
    }

    private function rollback(array $swappedDirs, array $copiedFiles): void
    {
        foreach ($swappedDirs as $target => $hadOld) {
            File::deleteDirectory($target);
            if ($hadOld) {
                File::moveDirectory("{$target}.old", $target);
            }
        }

        foreach ($copiedFiles as $file) {
            File::delete($file);
        }
    }

    public function enable(string $slug): void
    {
        Plugin::whereKey($slug)->update(['enabled' => true]);
        Plugins::forget($slug);

        if ($slug === 'boutique') {
            Setting::set('shop_enabled', '1');
        }
    }

    public function disable(string $slug): void
    {
        Plugin::whereKey($slug)->update(['enabled' => false]);
        Plugins::forget($slug);

        if ($slug === 'boutique') {
            Setting::set('shop_enabled', '0');
        }
    }
}
