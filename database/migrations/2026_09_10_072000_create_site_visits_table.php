<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Journal brut des pages publiques vues (hors administration) : sert à la
     * fois au compteur de visiteurs uniques et au tableau de bord statistiques
     * (pages les plus vues, référents, évolution dans le temps).
     */
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 64);
            $table->string('path', 500);
            $table->string('referrer', 500)->nullable();
            $table->date('visited_on');
            $table->timestamp('created_at')->useCurrent();
            $table->index('visited_on');
            $table->index(['visitor_id', 'visited_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
