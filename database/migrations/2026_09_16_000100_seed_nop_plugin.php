<?php

use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;

/**
 * Désactivé par défaut — l'admin doit l'activer volontairement depuis
 * /administration/plugins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Plugin::firstOrCreate(['slug' => 'nop'], [
            'label' => 'NOP (no image processing)',
            'description' => "Publie le fichier importé tel quel, sans générer de vignettes WebP — l'import est immédiat, mais l'original (souvent plus lourd) est servi directement sur le site.",
            'version' => '1.0.0',
            'enabled' => false,
            'installed_at' => now(),
        ]);
    }

    public function down(): void
    {
        Plugin::where('slug', 'nop')->delete();
    }
};
