<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyNotificationRecipient extends Model
{
    protected $fillable = [
        'notification_id',
        'recipient_id',
        'is_read',
        'read_at',
        'is_dismissed',
        'dismissed_at',
        'delivery_status',
        'delivered_at',
    ];

    protected $casts = [
        'is_read'      => 'boolean',
        'read_at'      => 'datetime',
        'is_dismissed' => 'boolean',
        'dismissed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function notification(): BelongsTo
    {
        return $this->belongsTo(AgencyNotification::class, 'notification_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
