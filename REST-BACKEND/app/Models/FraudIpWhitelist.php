<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudIpWhitelist extends Model
{
    use HasFactory;

    protected $table = 'fraud_ip_whitelist';

    protected $fillable = [
        'ip_address', 'reason', 'added_by',
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}