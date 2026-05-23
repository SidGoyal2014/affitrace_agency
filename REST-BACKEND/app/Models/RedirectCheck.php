<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedirectCheck extends Model
{
    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';

    const DEVICES = [
        'desktop_windows' => 'Windows Desktop',
        'desktop_mac'     => 'macOS Desktop',
        'mobile_ios'      => 'iPhone / iOS',
        'mobile_android'  => 'Android Phone',
        'tablet_ios'      => 'iPad / iOS Tablet',
        'tablet_android'  => 'Android Tablet',
        'bot_googlebot'   => 'Googlebot',
    ];

    protected $fillable = [
        'user_id',
        'input_url',
        'proxy',
        'device',
        'final_url',
        'hops',
        'hop_count',
        'status',
        'error_message',
    ];

    protected $casts = [
        'hops'       => 'array',
        'hop_count'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDeviceLabelAttribute(): string
    {
        return self::DEVICES[$this->device] ?? $this->device ?? 'Unknown';
    }
}