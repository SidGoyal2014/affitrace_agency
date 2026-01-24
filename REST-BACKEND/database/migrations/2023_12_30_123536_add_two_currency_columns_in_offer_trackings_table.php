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
            $table->decimal('revenue_usd', 25, 2)->after('currency');
            $table->decimal('payout_usd', 25, 2)->after('revenue_usd');
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
