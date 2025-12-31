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
}
