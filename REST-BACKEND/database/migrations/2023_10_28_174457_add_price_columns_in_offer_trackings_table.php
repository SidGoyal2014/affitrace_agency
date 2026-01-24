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
            
            $table->string('revenue')->nullable()->after('event');
            $table->string('payout')->nullable()->after('revenue');

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
