<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->text('intro_text')->nullable();
            $table->string('external_link')->nullable();
            $table->text('credits_text')->nullable();
            $table->date('created_on')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('status', ['draft', 'unlisted', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'password', 'private'])->default('public');
            $table->string('password_hash')->nullable();
            $table->string('layout_type')->default('grid');
            $table->boolean('seo_auto')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('seo_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('comments_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
