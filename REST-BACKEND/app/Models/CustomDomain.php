<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'verification_token',
        'is_verified',
        'verified_at',
        'is_active',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active'   => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function offerAffiliates()
    {
        return $this->hasMany(OfferAffiliate::class, 'custom_domain_id');
    }
}