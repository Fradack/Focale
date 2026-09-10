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
    /**
     * Les fichiers routes/plugins/{slug}.php appellent cette méthode dès leur
     * chargement (avant même le routage), y compris pendant `artisan migrate`
     * lui-même (le framework enregistre les routes au boot, quelle que soit
     * la commande) — sur une base tout juste créée, ni la table `plugins` ni
     * la table `cache` n'existent encore à cet instant précis. Sans ce
     * try/catch, un plugin réellement installé rendrait `artisan migrate`
     * (et toute la suite de tests) impossible à exécuter sur une base
     * fraîche.
     */
    public static function enabled(string $slug): bool
    {
        try {
            return (bool) Cache::rememberForever(
                "plugin:{$slug}:enabled",
                fn () => (bool) Plugin::where('slug', $slug)->value('enabled')
            );
        } catch (\Throwable) {
            return false;
        }
    }

    public static function installed(string $slug): bool
    {
        try {
            return Plugin::whereKey($slug)->whereNotNull('installed_at')->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function forget(string $slug): void
    {
        Cache::forget("plugin:{$slug}:enabled");
    }
}
