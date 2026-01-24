<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Adds indexes to offer_trackings table for improved query performance.
     * These indexes optimize:
     * - Postback lookups (tid)
     * - Report queries (offer_id + aff_id, adv_id, country, created_at)
     */
    public function up(): void
    {
        Schema::table('offer_trackings', function (Blueprint $table) {
            // Index for postback/conversion lookups by transaction ID
            $table->index('tid', 'idx_offer_trackings_tid');

            // Composite index for offer-affiliate report queries
            $table->index(['offer_id', 'aff_id'], 'idx_offer_trackings_offer_aff');

            // Index for advertiser report queries
            $table->index('adv_id', 'idx_offer_trackings_adv');

            // Index for geo-based reports
            $table->index('country', 'idx_offer_trackings_country');

            // Index for time-based queries (already has created_at from timestamps)
            $table->index('created_at', 'idx_offer_trackings_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->dropIndex('idx_offer_trackings_tid');
            $table->dropIndex('idx_offer_trackings_offer_aff');
            $table->dropIndex('idx_offer_trackings_adv');
            $table->dropIndex('idx_offer_trackings_country');
            $table->dropIndex('idx_offer_trackings_created');
        });
    }
};
