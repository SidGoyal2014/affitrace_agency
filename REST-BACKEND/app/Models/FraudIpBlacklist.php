<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudIpBlacklist extends Model
{
    use HasFactory;

    protected $table = 'fraud_ip_blacklist';

    protected $fillable = [
        'ip_address', 'reason', 'imported_from', 'added_by',
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}