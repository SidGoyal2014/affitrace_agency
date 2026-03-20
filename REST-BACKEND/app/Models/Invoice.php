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
        'title',
        // Invoice From
        'from_company',
        'from_address',
        'from_email',
        'from_vat_id',
        // Invoice To
        'company_type',
        'company_name',
        'to_address',
        'to_email',
        'to_vat_id',
        // Optional offer
        'offer_id',
        // Status & dates
        'status',
        'invoice_date',
        'due_date',
        'sent_at',
        'paid_at',
        // Amounts
        'subtotal_amount',
        'tax_amount',
        'tax_percent',
        'discount_amount',
        'net_total',
        'total_amount',
        // Payment & extras
        'payment_method',
        'notes',
        'terms',
        'pdf_path',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SENT = 'SENT';
    const STATUS_PAID = 'PAID';
    const STATUS_DELETED = 'DELETED';

    /**
     * Get the advertiser (user with user_type = 'advertiser')
     */
    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    /**
     * Get line items
     */
    public function lineItems()
    {
        return $this->hasMany(InvoiceLineItem::class);
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
