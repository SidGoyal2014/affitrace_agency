<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferTracking extends Model
{
    use HasFactory;

    protected $table = 'offer_trackings';

    protected $fillable = [
        'offer_id',
        'aff_id',
        'aff_click_id',
        'tid',
        'aff_sub1',
        'sub_aff_id',
        'aff_sub2',
        'aff_sub3',
        'aff_sub5',
        'aff_sub4',
        'source',
        'android_id',
        'google_aid',
        'device_id',
        'ip',
        'iosidfa',
        'offer_name',
        'country',
        'os',
        'raw_useragent',
        'os_ver',
        'adv_id',
        'useragent',
        'timestamp',
        'deep_link',
        'event_id',
        'event',
        'revenue',
        'payout',
        'currency',
        'is_transferred',
        'transferred_at',
        'revenue_usd',
        'payout_usd',
        'sub1',
        'browser',
        'brand',
        'isp',
        'language',
        'sub2',
        'event_ref_id',

        // New fields
        'session_id',
        'sub6',
        'sub7',
        'sub8',
        'sub9',
        'sub10',
        'browser_version',
        'screen_resolution',
        'is_bot',
        'is_vpn',
        'is_datacenter',
        'carrier',
        'region',
        'city',
    ];

    protected $casts = [
        'offer_id' => 'integer',
        'aff_id' => 'integer',
        'device_id' => 'integer',
        'adv_id' => 'integer',
        'transferred_at' => 'datetime',
        'revenue_usd' => 'decimal:2',
        'payout_usd' => 'decimal:2',
        'is_bot' => 'boolean',
        'is_vpn' => 'boolean',
        'is_datacenter' => 'boolean',
    ];
}
