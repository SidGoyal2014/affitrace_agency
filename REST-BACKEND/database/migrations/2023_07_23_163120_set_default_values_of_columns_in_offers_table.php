<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {

            $table->integer('category_id')->nullable()->change();
            $table->string('app_id')->nullable()->change();
            $table->string('external_offer_id')->nullable()->change();
            $table->string('preview_url')->nullable()->change();
            $table->string('ad_pricing')->nullable()->change();
            $table->string('aff_pricing')->nullable()->change();
            $table->string('campaign_start_date')->nullable()->change();
            $table->string('campaign_end_date')->nullable()->change();
            $table->string('daily_start_time')->nullable()->change();
            $table->string('daily_stop_time')->nullable()->change();
            $table->string('offer_visibility')->nullable()->change();
			$table->string('offer_status')->nullable()->change();
            $table->text('offer_desc')->nullable()->change();
            $table->text('private_note')->nullable()->change();
            $table->text('offer_terms')->nullable()->change();
            $table->string('target_country')->nullable()->change();
            $table->string('target_state')->nullable()->change();
            $table->string('target_city')->nullable()->change();
            $table->string('target_device')->nullable()->change();

            if (DB::connection()->getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE offers ALTER COLUMN hide_payout DROP NOT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN hide_payout SET DEFAULT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN daily_start_stop DROP NOT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN daily_start_stop SET DEFAULT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN alert_to_affiliates DROP NOT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN alert_to_affiliates SET DEFAULT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN deep_links DROP NOT NULL");
                DB::statement("ALTER TABLE offers ALTER COLUMN deep_links SET DEFAULT NULL");
            } else {
                DB::statement("ALTER TABLE `offers` CHANGE `hide_payout` `hide_payout` ENUM('N', 'Y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
                DB::statement("ALTER TABLE `offers` CHANGE `daily_start_stop` `daily_start_stop` ENUM('N', 'Y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
                DB::statement("ALTER TABLE `offers` CHANGE `alert_to_affiliates` `alert_to_affiliates` ENUM('N', 'Y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
                DB::statement("ALTER TABLE `offers` CHANGE `deep_links` `deep_links` ENUM('N', 'Y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
            }

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
