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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('title')->nullable();
            $table->string('slug')->unique();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->string('credit')->nullable();
            $table->string('copyright')->nullable();
            $table->string('author')->nullable();
            $table->string('license')->nullable();
            $table->date('taken_at')->nullable();
            $table->string('location')->nullable();
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->boolean('hide_gps')->default(false);
            $table->json('exif')->nullable();
            $table->string('mime_type');
            $table->string('disk_path');
            $table->unsignedBigInteger('filesize');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('checksum', 64)->index();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('trashed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
