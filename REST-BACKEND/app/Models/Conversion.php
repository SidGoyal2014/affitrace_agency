<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    use HasFactory;

    protected $table = 'conversions';

    protected $fillable = [
        'tid',
        'i_id',
        'aff_click_id',
        'offer_id',
        'offer_name',
        's_offer',
        'affiliate_id',
        'affiliate_name',
        's_affiliate',
        'aff_manager',
        'advertiser_id',
        'advertiser_name',
        'adv_manager',
        'clicks',
        'conversion',
        'impressions',
        'unique_clicks',
        'affiliate_price',
        'advertiser_price',
        'affiliate_model',
        'advertiser_model',
        'sale',
        'profit',
        'currency',
        'revenue',
        'payout',
        'revenue_usd',
        'payout_usd',
        'event_id',
        'event',
        'status',
        'status_type',
        'filter_log',
        'date',
        'time',
        'hour',
        'conversion_time',
        'ip',
        'country',
        'location',
        'isp',
        'conn_speed',
        'device',
        'device_brand',
        'device_model',
        'device_id',
        'os',
        'os_ver',
        'browser',
        'useragent',
        'referral_url',
        'referral_domain',
        'source',
        'aff_sub1',
        'aff_sub2',
        'aff_sub3',
        'aff_sub4',
        'aff_sub5',
        'adv_sub1',
        'adv_sub2',
        'adv_sub3',
        'adv_sub4',
        'adv_sub5',
        'ios_idfa',
        'google_aid',
        'android_id',
        'raw_data',
        
        // New reporting fields
        'session_id',
        'rejection_reason',
        'status_changed_by',
        'status_changed_at',
        'is_flagged',
        'flag_reason',
        'click_time',
        'click_to_conv_time',
        'click_to_conv_display',
        'postback_fired_at',
        'postback_received_at',
        'landing_page_id',
        'landing_page_url',
        'affiliate_manager_id',
        'aff_sub6',
        'aff_sub7',
        'aff_sub8',
        'aff_sub9',
        'aff_sub10',
        'adv_sub6',
        'adv_sub7',
        'adv_sub8',
        'adv_sub9',
        'adv_sub10',
        'click_ip',
        'conversion_ip',
        'ip_match',
        'tracking_url',
        'redirect_url',
        'tracking_domain',
        'region',
        'city',
        'carrier',
        'connection_type',
        'device_type',
        'device_id_raw',
        'os_version',
        'browser_version',
        'screen_resolution',
        'fraud_score',
        'fraud_reason',
        'is_bot',
        'is_vpn',
        'is_datacenter',
        'is_duplicate_ip',
        'is_duplicate_device',
        'geo_mismatch',
        'ctit_flag',
        'attribution_type',
        'impression_time',
        'view_to_click_time',
        'attribution_window',
        'postback_status',
        'postback_http_code',
        'postback_url_fired',
        'postback_attempts',
        'adv_postback_url',
    ];

    protected $casts = [
        'date' => 'date',
        'conversion_time' => 'datetime',
        'raw_data' => 'array',
        'affiliate_price' => 'decimal:2',
        'advertiser_price' => 'decimal:2',
        'sale' => 'decimal:2',
        'profit' => 'decimal:2',
        'revenue' => 'decimal:2',
        'payout' => 'decimal:2',
        'revenue_usd' => 'decimal:2',
        'payout_usd' => 'decimal:2',
        
        // New casts
        'status_changed_at' => 'datetime',
        'click_time' => 'datetime',
        'postback_fired_at' => 'datetime',
        'postback_received_at' => 'datetime',
        'impression_time' => 'datetime',
        'is_flagged' => 'boolean',
        'ip_match' => 'boolean',
        'is_bot' => 'boolean',
        'is_vpn' => 'boolean',
        'is_datacenter' => 'boolean',
        'is_duplicate_ip' => 'boolean',
        'is_duplicate_device' => 'boolean',
        'geo_mismatch' => 'boolean',
        'click_to_conv_time' => 'integer',
        'landing_page_id' => 'integer',
        'affiliate_manager_id' => 'integer',
        'fraud_score' => 'integer',
        'view_to_click_time' => 'integer',
        'postback_http_code' => 'integer',
        'postback_attempts' => 'integer',
    ];

    /**
     * Get the original click (from offer_trackings)
     */
    public function click()
    {
        return $this->belongsTo(OfferTracking::class, 'tid', 'tid');
    }

    /**
     * Get the offer
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    /**
     * Accessor for click_to_conv_time with dynamic fallback.
     */
    public function getClickToConvTimeAttribute($value)
    {
        if ($value !== null) {
            return (int) $value;
        }

        $convTime = $this->conversion_time ?: $this->created_at;
        $clickTime = $this->click_time;

        if (!$clickTime) {
            $click = $this->click;
            if ($click) {
                $clickTime = $click->created_at;
            }
        }

        if ($convTime && $clickTime) {
            return max(0, strtotime($convTime) - strtotime($clickTime));
        }

        return null;
    }

    /**
     * Accessor for click_to_conv_display with dynamic fallback.
     */
    public function getClickToConvDisplayAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        $seconds = $this->click_to_conv_time;
        if ($seconds === null) {
            return '-';
        }

        if ($seconds < 60) {
            return $seconds . 's';
        }
        $minutes = floor($seconds / 60);
        $remSeconds = $seconds % 60;
        if ($minutes < 60) {
            return $minutes . 'm ' . $remSeconds . 's';
        }
        $hours = floor($minutes / 60);
        $remMinutes = $minutes % 60;
        if ($hours < 24) {
            return $hours . 'h ' . $remMinutes . 'm';
        }
        $days = floor($hours / 24);
        $remHours = $hours % 24;
        return $days . 'd ' . $remHours . 'h';
    }

    /**
     * Accessor for ip_match with dynamic fallback.
     */
    public function getIpMatchAttribute($value)
    {
        if (array_key_exists('ip_match', $this->attributes) && $this->attributes['ip_match'] !== null) {
            return (bool) $this->attributes['ip_match'];
        }

        $clickIp = $this->click_ip;
        $convIp = $this->conversion_ip;

        if ($clickIp && $convIp) {
            return strtolower(trim($clickIp)) === strtolower(trim($convIp));
        }

        return false;
    }

    /**
     * Accessor for click_ip with dynamic fallback.
     */
    public function getClickIpAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }
        $click = $this->click;
        return $click ? $click->ip : null;
    }

    /**
     * Accessor for conversion_ip with dynamic fallback.
     */
    public function getConversionIpAttribute($value)
    {
        return $value ?: $this->ip;
    }
}
