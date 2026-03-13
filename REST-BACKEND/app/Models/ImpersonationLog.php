<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpersonationLog extends Model
{
    protected $fillable = [
        'impersonator_id',
        'impersonated_id',
        'started_at',
        'ended_at',
        'ip_address',
        'notified',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'notified'   => 'boolean',
    ];

    /**
     * The admin/employee who initiated impersonation.
     */
    public function impersonator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'impersonator_id');
    }

    /**
     * The affiliate/advertiser being impersonated.
     */
    public function impersonated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'impersonated_id');
    }
}
