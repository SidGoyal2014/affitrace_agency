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
        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->string('browser')->nullable()->after('os_ver');
            $table->string('brand')->nullable()->after('browser');
            $table->string('isp')->nullable()->after('brand');
            $table->string('language')->nullable()->after('isp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_trackings', function (Blueprint $table) {
            //
        });
    }
};
