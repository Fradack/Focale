<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Renomme le plugin "NOP" en "NIP" — mise à jour d'une ligne déjà migrée
 * (2026_09_16_000100_seed_nop_plugin), donc une nouvelle migration plutôt
 * qu'une modification de l'ancienne (déjà appliquée en production).
 * Un simple UPDATE (pas delete+recreate) pour préserver enabled/installed_at
 * si le plugin a déjà été activé.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('plugins')->where('slug', 'nop')->update([
            'slug' => 'nip',
            'label' => 'NIP (no image processing)',
        ]);
    }

    public function down(): void
    {
        DB::table('plugins')->where('slug', 'nip')->update([
            'slug' => 'nop',
            'label' => 'NOP (no image processing)',
        ]);
    }
};
