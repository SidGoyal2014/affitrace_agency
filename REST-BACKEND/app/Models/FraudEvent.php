<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudEvent extends Model
{
    use HasFactory;

    protected $table = 'fraud_events';
    public $timestamps = false;

    protected $fillable = [
        'event_type', 'entity_type', 'entity_id',
        'rule_id', 'offer_id', 'affiliate_id', 'details', 'created_at',
    ];

    protected $casts = [
        'details'    => 'array',
        'created_at' => 'datetime',
    ];

    public function rule()
    {
        return $this->belongsTo(FraudRule::class, 'rule_id');
    }
}