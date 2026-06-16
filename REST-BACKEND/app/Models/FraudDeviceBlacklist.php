<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudDeviceBlacklist extends Model
{
    use HasFactory;

    protected $table = 'fraud_device_blacklist';

    protected $fillable = [
        'device_id', 'device_type', 'reason', 'added_by',
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}