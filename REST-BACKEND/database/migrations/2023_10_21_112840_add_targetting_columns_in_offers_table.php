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
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('country_permission', ['allow', 'block'])->nullable()->after('offer_terms');
            $table->enum('state_permission', ['allow', 'block'])->nullable()->after('target_country');
            $table->enum('city_permission', ['allow', 'block'])->nullable()->after('target_state');
            $table->enum('device_permission', ['allow', 'block'])->nullable()->after('target_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            //
        });
    }
};
