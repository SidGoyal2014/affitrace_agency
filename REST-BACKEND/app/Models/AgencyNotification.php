<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyNotification extends Model
{
    protected $fillable = [
        'agency_id',
        'sender_id',
        'type',
        'category',
        'priority',
        'target_role',
        'channel',
        'title',
        'body',
        'data',
        'is_active',
        'is_dispatched',
        'scheduled_at',
        'expires_at',
    ];

    protected $casts = [
        'type'          => 'string',
        'category'      => 'string',
        'priority'      => 'string',
        'target_role'   => 'string',
        'channel'       => 'string',
        'is_active'     => 'boolean',
        'is_dispatched' => 'boolean',
        'scheduled_at'  => 'datetime',
        'expires_at'    => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AgencyNotificationRecipient::class, 'notification_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    public function scopePendingDispatch($query)
    {
        return $query->where('scheduled_at', '<=', now())
                     ->where('is_dispatched', false)
                     ->where('is_active', true);
    }
}
