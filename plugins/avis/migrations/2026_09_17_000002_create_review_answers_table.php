<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('review_question_id')->nullable()->constrained('review_questions')->nullOnDelete();
            // Copie du texte de la question au moment de la réponse : reste
            // lisible même si la question est ensuite modifiée ou supprimée.
            $table->string('question_text');
            $table->text('answer_text')->nullable();
            $table->unsignedTinyInteger('answer_rating')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_answers');
    }
};
