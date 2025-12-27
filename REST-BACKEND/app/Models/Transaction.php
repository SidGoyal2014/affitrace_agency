<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

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
