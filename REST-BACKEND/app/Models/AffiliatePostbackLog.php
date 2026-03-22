<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePostbackLog extends Model
{
    use HasFactory;

    protected $table = 'affiliate_postback_logs';

    protected $fillable = [
        'conversion_id',
        'affiliate_id',
        'url_fired',
        'http_status',
        'response_body',
    ];

    public function conversion()
    {
        return $this->belongsTo(Conversion::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
}
