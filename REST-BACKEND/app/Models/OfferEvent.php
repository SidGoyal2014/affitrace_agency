<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'event_name',
        'event_token',
        'revenue',
        'payout',
        'multi_conv',
        'multi_conv_ip',
        'approval',
        'postback',
    ];

    protected $casts = [
        'revenue' => 'decimal:2',
        'payout'  => 'decimal:2',
    ];
}
