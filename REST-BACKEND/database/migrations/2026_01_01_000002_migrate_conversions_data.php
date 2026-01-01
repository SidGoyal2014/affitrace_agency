<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Migrates existing conversion records from offer_trackings to conversions table.
     * Condition: tid = aff_click_id AND aff_click_id IS NOT NULL
     */
    public function up(): void
    {
        // Only run if conversions table exists and is empty
        if (!DB::getSchemaBuilder()->hasTable('conversions')) {
            return;
        }

        $count = DB::table('conversions')->count();
        if ($count > 0) {
            // Skip if already has data
            return;
        }

        DB::statement("
            INSERT INTO conversions (
                tid,
                aff_click_id,
                offer_id,
                offer_name,
                affiliate_id,
                advertiser_id,
                revenue,
                payout,
                currency,
                revenue_usd,
                payout_usd,
                event_id,
                event,
                ip,
                country,
                device_id,
                os,
                os_ver,
                browser,
                useragent,
                source,
                aff_sub1,
                aff_sub2,
                ios_idfa,
                google_aid,
                android_id,
                date,
                created_at,
                updated_at
            )
            SELECT 
                tid,
                aff_click_id,
                offer_id,
                offer_name,
                aff_id,
                adv_id,
                revenue,
                payout,
                currency,
                revenue_usd,
                payout_usd,
                event_id,
                event,
                ip,
                country,
                device_id,
                os,
                os_ver,
                browser,
                useragent,
                source,
                sub1,
                sub2,
                iosidfa,
                google_aid,
                android_id,
                DATE(created_at),
                created_at,
                updated_at
            FROM offer_trackings
            WHERE tid = aff_click_id 
              AND aff_click_id IS NOT NULL 
              AND aff_click_id != ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear migrated data
        // DB::table('conversions')->truncate();
    }
};
