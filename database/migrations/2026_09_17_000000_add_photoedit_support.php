<?php

use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Désactivé par défaut, comme les autres plugins récents — l'admin doit
 * l'activer volontairement depuis /administration/plugins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('original_backup_path')->nullable()->after('disk_path');
        });

        Plugin::firstOrCreate(['slug' => 'photoedit'], [
            'label' => 'PhotoEdit',
            'description' => "Éditeur d'image intégré (recadrage, rotation) avec sauvegarde de l'original.",
            'version' => '1.0.0',
            'enabled' => false,
            'installed_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('original_backup_path');
        });

        Plugin::where('slug', 'photoedit')->delete();
    }
};
