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
            $table->enum('browser_permission', ['allow', 'block'])->nullable()->after('target_device');
            $table->string('browser')->nullable()->after('browser_permission');

            $table->enum('os_permission', ['allow', 'block'])->nullable()->after('browser');
            $table->string('os')->nullable()->after('os_permission');

            $table->enum('os_ver_permission', ['allow', 'block'])->nullable()->after('os');
            $table->string('os_ver')->nullable()->after('os_ver_permission');

            $table->enum('brand_permission', ['allow', 'block'])->nullable()->after('os_ver');
            $table->string('brand')->nullable()->after('brand_permission');

            $table->enum('isp_permission', ['allow', 'block'])->nullable()->after('brand');
            $table->string('isp')->nullable()->after('isp_permission');

            $table->enum('language_permission', ['allow', 'block'])->nullable()->after('isp');
            $table->string('language')->nullable()->after('language_permission');
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
