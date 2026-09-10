<?php

namespace App\Support;

use App\Models\Plugin;
use Illuminate\Support\Facades\Cache;

/**
 * Registre des plugins — même idiome que Setting::get()/set() (cache
 * indéfini, invalidé explicitement à l'écriture) mais adossé à une vraie
 * table (plugins) plutôt qu'au store clé/valeur, puisqu'on a besoin de
 * lister/énumérer les plugins installés, pas seulement lire une valeur
 * scalaire par clé connue à l'avance.
 */
class Plugins
{
    public static function enabled(string $slug): bool
    {
        return (bool) Cache::rememberForever(
            "plugin:{$slug}:enabled",
            fn () => (bool) Plugin::where('slug', $slug)->value('enabled')
        );
    }

    public static function installed(string $slug): bool
    {
        return Plugin::whereKey($slug)->whereNotNull('installed_at')->exists();
    }

    public static function forget(string $slug): void
    {
        Cache::forget("plugin:{$slug}:enabled");
    }
}
