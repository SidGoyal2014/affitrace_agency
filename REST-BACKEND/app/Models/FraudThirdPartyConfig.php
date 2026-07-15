<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudThirdPartyConfig extends Model
{
    use HasFactory;

    protected $table = 'fraud_third_party_configs';

    protected $fillable = [
        'provider', 'api_key', 'api_url', 'settings', 'is_active',
    ];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = ['api_key']; // Never expose key in list responses
}