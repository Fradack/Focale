<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Partagé entre UpdateService (mises à jour) et PluginManager (installation
 * de plugins) : les deux téléchargent et extraient une archive GitHub.
 */
class GithubRelease
{
    /**
     * Les archives GitHub extraient toujours dans un unique sous-dossier
     * nommé "depot-tag/".
     */
    public static function findExtractedRoot(string $extractPath): string
    {
        $entries = File::directories($extractPath);

        return count($entries) === 1 ? $entries[0] : $extractPath;
    }
}
