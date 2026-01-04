<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Creates a dedicated conversions table matching Offer18 schema structure.
     */
    public function up(): void
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();

            // Identifiers
            $table->string('tid')->index();
            $table->string('i_id')->nullable();
            $table->string('aff_click_id')->nullable();

            // Offer Data
            $table->integer('offer_id')->index();
            $table->string('offer_name')->nullable();
            $table->string('s_offer')->nullable();

            // Affiliate Data
            $table->integer('affiliate_id')->index();
            $table->string('affiliate_name')->nullable();
            $table->string('s_affiliate')->nullable();
            $table->string('aff_manager')->nullable();

            // Advertiser Data
            $table->integer('advertiser_id')->index();
            $table->string('advertiser_name')->nullable();
            $table->string('adv_manager')->nullable();

            // Metrics
            $table->integer('clicks')->default(0);
            $table->integer('conversion')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('unique_clicks')->default(0);

            // Pricing
            $table->decimal('affiliate_price', 10, 2)->default(0);
            $table->decimal('advertiser_price', 10, 2)->default(0);
            $table->string('affiliate_model', 50)->nullable();
            $table->string('advertiser_model', 50)->nullable();
            $table->decimal('sale', 10, 2)->default(0);
            $table->decimal('profit', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');

            // Revenue (backward compatibility)
            $table->decimal('revenue', 10, 2)->default(0);
            $table->decimal('payout', 10, 2)->default(0);
            $table->decimal('revenue_usd', 10, 2)->default(0);
            $table->decimal('payout_usd', 10, 2)->default(0);

            // Event Data
            $table->integer('event_id')->nullable();
            $table->string('event', 100)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('status_type', 50)->nullable();
            $table->text('filter_log')->nullable();

            // Time Data
            $table->date('date')->nullable()->index();
            $table->time('time')->nullable();
            $table->string('hour', 5)->nullable();
            $table->dateTime('conversion_time')->nullable();

            // Geo Data
            $table->string('ip', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('location')->nullable();
            $table->string('isp')->nullable();
            $table->string('conn_speed', 50)->nullable();

            // Device Data
            $table->string('device', 100)->nullable();
            $table->string('device_brand', 100)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->integer('device_id')->nullable();
            $table->string('os', 50)->nullable();
            $table->string('os_ver', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->text('useragent')->nullable();

            // Tracking Data
            $table->text('referral_url')->nullable();
            $table->string('referral_domain')->nullable();
            $table->string('source')->nullable();

            // Affiliate Sub Parameters
            $table->string('aff_sub1')->nullable();
            $table->string('aff_sub2')->nullable();
            $table->string('aff_sub3')->nullable();
            $table->string('aff_sub4')->nullable();
            $table->string('aff_sub5')->nullable();

            // Advertiser Sub Parameters
            $table->string('adv_sub1')->nullable();
            $table->string('adv_sub2')->nullable();
            $table->string('adv_sub3')->nullable();
            $table->string('adv_sub4')->nullable();
            $table->string('adv_sub5')->nullable();

            // MMP Data
            $table->string('ios_idfa')->nullable();
            $table->string('google_aid')->nullable();
            $table->string('android_id')->nullable();

            // Raw Data
            $table->json('raw_data')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
