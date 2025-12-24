<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferMmpDetail extends Model
{
    use HasFactory;

    protected $table = 'offer_mmp_details';

    protected $fillable = [
        'offer_id',
        'mmp_id',
        'attribution_url'
    ];
}
