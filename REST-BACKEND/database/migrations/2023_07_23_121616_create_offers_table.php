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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_name');
            $table->string('advertiser_id');
            $table->integer('category_id');
            $table->string('app_id');
            $table->string('external_offer_id');
            $table->string('preview_url');
            $table->text('offer_url');
            $table->string('ad_model');
            $table->string('ad_pricing');
            $table->string('currency');
            $table->string('aff_model');
            $table->string('aff_pricing');
			$table->enum('hide_payout', ['N', 'Y']);
            $table->timestamp('campaign_start_date');
            $table->timestamp('campaign_end_date');
            $table->enum('daily_start_stop', ['N', 'Y']);
            $table->string('daily_start_time');
            $table->string('daily_stop_time');
            $table->string('offer_visibility');
			$table->string('offer_status');
            $table->enum('alert_to_affiliates', ['N', 'Y']);
			$table->enum('deep_links', ['N', 'Y']);
            $table->text('offer_desc');
            $table->text('private_note');
            $table->text('offer_terms');
            $table->string('target_country');
            $table->string('target_state');
            $table->string('target_city');
            $table->string('target_device');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
