<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudClickScore extends Model
{
    use HasFactory;

    protected $table = 'fraud_click_scores';

    protected $fillable = [
        'tid', 'offer_id', 'affiliate_id', 'ip_address',
        'score', 'flags', 'status', 'third_party_data',
    ];

    protected $casts = [
        'flags'            => 'array',
        'third_party_data' => 'array',
        'score'            => 'integer',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
}