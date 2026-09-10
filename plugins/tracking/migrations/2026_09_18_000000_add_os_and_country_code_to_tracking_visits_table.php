<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_visits', function (Blueprint $table) {
            $table->string('os', 20)->nullable()->after('device_type');
            $table->string('country_code', 2)->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_visits', function (Blueprint $table) {
            $table->dropColumn(['os', 'country_code']);
        });
    }
};
