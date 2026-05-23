<?php

namespace App\Models;

use App\Enums\WeeklyPayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateWeeklyPayout extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'affiliate_weekly_payouts';

    protected $fillable = [
        'affiliate_id',
        'week_start',
        'week_end',
        'month_key',
        'year',
        'week_number',
        'entry_type',
        'generated_key',
        'work_basis',
        'work_units',
        'total_amount',
        'currency',
        'status',
        'invoice_code',
        'notes',
        'rejected_reason',
        'breakdown',
        'paid_at',
        'rejected_at',
        'paid_by',
        'rejected_by',
        'created_by',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'work_units' => 'decimal:4',
        'total_amount' => 'decimal:2',
        'breakdown' => 'array',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
        'status' => WeeklyPayoutStatus::class,
    ];

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'weekly_payout_id');
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === WeeklyPayoutStatus::Pending;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === WeeklyPayoutStatus::Paid;
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === WeeklyPayoutStatus::Rejected;
    }
}