<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La colonne était en DATE : l'heure de prise de vue (pourtant bien
        // lue depuis l'EXIF par MediaIngestService) était perdue au niveau
        // du stockage, pas seulement à l'affichage. `->change()` nécessite
        // doctrine/dbal (non installé) — on modifie la colonne en SQL brut.
        // SQLite (utilisé par les tests) n'a pas de vrai type DATE : sa
        // colonne stocke déjà l'heure sans troncature, rien à corriger.
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE media MODIFY taken_at DATETIME NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE media MODIFY taken_at DATE NULL');
    }
};
