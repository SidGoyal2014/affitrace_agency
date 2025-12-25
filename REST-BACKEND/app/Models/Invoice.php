<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'advertiser_id',
        'status',
        'invoice_date',
        'due_date',
        'sent_at',
        'paid_at',
        'subtotal_amount',
        'tax_amount',
        'total_amount',
        'pdf_path',
        'created_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SENT = 'SENT';
    const STATUS_PAID = 'PAID';
    const STATUS_DELETED = 'DELETED';

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeItemsAvailableForInvoice($query, $advertiserId)
    {
        return $query->where('advertiser_id', $advertiserId)
            ->where('billing', 'Y')
            ->whereNull('invoice_id');
    }

    public function getIsDraftAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getIsSentAttribute()
    {
        return $this->status === self::STATUS_SENT;
    }

    public function getIsPaidAttribute()
    {
        return $this->status === self::STATUS_PAID;
    }
}
