<?php

use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;

/**
 * Boutique et J'aime existaient déjà avant le système de plugins — on les
 * marque "installés et activés" pour ne rien casser sur les sites existants.
 * Tracking n'est volontairement PAS créé ici : il n'apparaît qu'une fois
 * réellement installé depuis /administration/plugins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Plugin::firstOrCreate(['slug' => 'boutique'], [
            'label' => 'Boutique',
            'description' => 'Vente de tirages et produits dérivés.',
            'enabled' => true,
            'installed_at' => now(),
        ]);

        Plugin::firstOrCreate(['slug' => 'likes'], [
            'label' => "J'aime",
            'description' => 'Cœur/like anonyme sur les photos et les albums.',
            'enabled' => true,
            'installed_at' => now(),
        ]);
    }

    public function down(): void
    {
        Plugin::whereIn('slug', ['boutique', 'likes'])->delete();
    }
};
