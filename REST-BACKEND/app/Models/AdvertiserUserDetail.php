<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiserUserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'postback_ip_whitelist',
        'time_zone',
        'affiliate_block',
        'affiliate_whitelist',
        'private_note',
        'additional_info',
        'tax_details',
    ];



    
}
