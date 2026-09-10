<?php

use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;

/**
 * Nouveaux plugins, désactivés par défaut (comme Tracking) — l'admin doit
 * les activer volontairement depuis /administration/plugins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Plugin::firstOrCreate(['slug' => 'antipillage'], [
            'label' => 'Anti-pillage',
            'description' => "Empêche le clic droit et le glisser-déposer sur les photos publiques (dissuasif, pas une protection absolue).",
            'version' => '1.0.0',
            'enabled' => false,
            'installed_at' => now(),
        ]);

        Plugin::firstOrCreate(['slug' => 'stats-boutique'], [
            'label' => 'Statistiques boutique',
            'description' => 'Chiffre d\'affaires, meilleures ventes et répartition des commandes par statut.',
            'version' => '1.0.0',
            'enabled' => false,
            'installed_at' => now(),
        ]);
    }

    public function down(): void
    {
        Plugin::whereIn('slug', ['antipillage', 'stats-boutique'])->delete();
    }
};
