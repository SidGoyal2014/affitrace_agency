<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferAffiliate extends Model
{
    use HasFactory;

    protected $casts = [
        'request_status' => \App\Enums\OfferRequest::class,
    ];
}
