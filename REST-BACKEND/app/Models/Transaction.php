<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        // Payment receipt fields (from 2026_04_10 migration)
        'payment_type',
        'payment_mode',
        'logged_in_user',
        'payment_from',
        'payment_to',
        'advertiser_id',
        'affiliate_id',
        'transaction_code',
        'manual_invoice_no',
        // Fields from 2024_06_20 migration
        'transaction_amount',
        'transaction_details',
        'activity_date',
        'subscription_id',
        // Invoice linkage (2025_12_26 migration)
        'invoice_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($transaction) {
            if ($transaction->invoice_id && $transaction->isDirty(['billing', 'billing_reason', 'billing_charge'])) {
                // Ensure the related invoice is loaded
                if (!$transaction->relationLoaded('invoice')) {
                    $transaction->load('invoice');
                }

                if ($transaction->invoice && $transaction->invoice->status !== Invoice::STATUS_DRAFT) {
                    throw new \Exception("Cannot edit billing fields for a finalized invoice.");
                }
            }
        });
    }
}
