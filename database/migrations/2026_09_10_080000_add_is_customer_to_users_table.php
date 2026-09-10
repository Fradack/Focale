<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue les comptes client (inscription publique, cachée — voir
     * routes/web.php préfixe /compte) des comptes d'équipe (admin/editor/
     * contributor/viewer, créés uniquement via l'installateur ou la gestion
     * des utilisateurs). Colonne à part plutôt qu'une valeur d'enum sur
     * `role` : `role` est un ENUM NOT NULL DEFAULT 'admin' — l'ajouter en
     * touchant l'enum demanderait de le modifier sur MySQL et SQLite avec
     * des syntaxes différentes, alors qu'un simple booléen additif suffit et
     * ne risque pas de faire hériter un compte client du rôle admin par défaut.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_customer')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_customer');
        });
    }
};
