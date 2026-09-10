<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 64);
            $table->string('path', 500);
            $table->string('ip', 45)->nullable();
            $table->string('commune')->nullable();
            $table->string('country')->nullable();
            $table->string('device_type', 20)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_visits');
    }
};
