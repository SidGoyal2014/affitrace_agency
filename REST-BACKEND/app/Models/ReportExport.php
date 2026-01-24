<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY = 'ready';
    const STATUS_EXPIRED = 'expired';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'report_type',
        'format',
        'filters',
        'columns',
        'status',
        'expires_at',
        'error',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if export is ready for download.
     */
    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    /**
     * Check if export has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED
            || ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Check if export is still processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING
            || $this->status === self::STATUS_QUEUED;
    }
}
