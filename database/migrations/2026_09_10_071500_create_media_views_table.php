<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Une vue par (œuvre, visiteur, jour) : évite qu'un simple rafraîchissement
     * de page ne gonfle artificiellement le compteur, tout en recomptant une
     * vraie visite si le même visiteur revient un autre jour.
     */
    public function up(): void
    {
        Schema::create('media_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_id', 64);
            $table->date('viewed_on');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['media_id', 'visitor_id', 'viewed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_views');
    }
};
