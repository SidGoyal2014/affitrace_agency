<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->string('session_id')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('status_changed_by')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->string('flag_reason')->nullable();
            $table->timestamp('click_time')->nullable();
            $table->integer('click_to_conv_time')->nullable();
            $table->string('click_to_conv_display', 50)->nullable();
            $table->timestamp('postback_fired_at')->nullable();
            $table->timestamp('postback_received_at')->nullable();
            $table->integer('landing_page_id')->nullable();
            $table->text('landing_page_url')->nullable();
            $table->integer('affiliate_manager_id')->nullable();
            
            // Sub parameters 6-10
            $table->string('aff_sub6')->nullable();
            $table->string('aff_sub7')->nullable();
            $table->string('aff_sub8')->nullable();
            $table->string('aff_sub9')->nullable();
            $table->string('aff_sub10')->nullable();
            
            $table->string('adv_sub6')->nullable();
            $table->string('adv_sub7')->nullable();
            $table->string('adv_sub8')->nullable();
            $table->string('adv_sub9')->nullable();
            $table->string('adv_sub10')->nullable();
            
            $table->string('click_ip', 45)->nullable();
            $table->string('conversion_ip', 45)->nullable();
            $table->boolean('ip_match')->default(false);
            
            $table->text('tracking_url')->nullable();
            $table->text('redirect_url')->nullable();
            $table->string('tracking_domain')->nullable();
            
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('carrier')->nullable();
            $table->string('connection_type', 50)->nullable();
            
            $table->string('device_type', 50)->nullable();
            $table->string('device_id_raw')->nullable();
            $table->string('os_version', 50)->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->string('screen_resolution', 50)->nullable();
            
            $table->integer('fraud_score')->default(0);
            $table->string('fraud_reason')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_vpn')->default(false);
            $table->boolean('is_datacenter')->default(false);
            $table->boolean('is_duplicate_ip')->default(false);
            $table->boolean('is_duplicate_device')->default(false);
            $table->boolean('geo_mismatch')->default(false);
            $table->string('ctit_flag', 50)->nullable();
            
            $table->string('attribution_type', 50)->nullable();
            $table->timestamp('impression_time')->nullable();
            $table->integer('view_to_click_time')->nullable();
            $table->string('attribution_window', 50)->nullable();
            
            $table->string('postback_status', 50)->nullable();
            $table->integer('postback_http_code')->nullable();
            $table->text('postback_url_fired')->nullable();
            $table->integer('postback_attempts')->default(0);
            $table->text('adv_postback_url')->nullable();
        });

        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->string('session_id')->nullable();
            $table->string('sub6')->nullable();
            $table->string('sub7')->nullable();
            $table->string('sub8')->nullable();
            $table->string('sub9')->nullable();
            $table->string('sub10')->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->string('screen_resolution', 50)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_vpn')->default(false);
            $table->boolean('is_datacenter')->default(false);
            $table->string('carrier')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn([
                'session_id', 'rejection_reason', 'status_changed_by', 'status_changed_at',
                'is_flagged', 'flag_reason', 'click_time', 'click_to_conv_time', 'click_to_conv_display',
                'postback_fired_at', 'postback_received_at', 'landing_page_id', 'landing_page_url',
                'affiliate_manager_id', 'aff_sub6', 'aff_sub7', 'aff_sub8', 'aff_sub9', 'aff_sub10',
                'adv_sub6', 'adv_sub7', 'adv_sub8', 'adv_sub9', 'adv_sub10', 'click_ip', 'conversion_ip',
                'ip_match', 'tracking_url', 'redirect_url', 'tracking_domain', 'region', 'city',
                'carrier', 'connection_type', 'device_type', 'device_id_raw', 'os_version',
                'browser_version', 'screen_resolution', 'fraud_score', 'fraud_reason', 'is_bot',
                'is_vpn', 'is_datacenter', 'is_duplicate_ip', 'is_duplicate_device', 'geo_mismatch',
                'ctit_flag', 'attribution_type', 'impression_time', 'view_to_click_time',
                'attribution_window', 'postback_status', 'postback_http_code', 'postback_url_fired',
                'postback_attempts', 'adv_postback_url'
            ]);
        });

        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->dropColumn([
                'session_id', 'sub6', 'sub7', 'sub8', 'sub9', 'sub10', 'browser_version',
                'screen_resolution', 'is_bot', 'is_vpn', 'is_datacenter', 'carrier', 'region', 'city'
            ]);
        });
    }
};
