<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferAffiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'affiliate_id',
        'tracking_url',
        'slug',
        'custom_domain_id',
        'request_made',
        'request_status',
    ];

    public function customDomain()
    {
        return $this->belongsTo(CustomDomain::class, 'custom_domain_id');
    }

    /**
     * Returns the short tracking URL based on slug + custom domain.
     * Falls back to legacy gourl format if no slug exists.
     */
    public function getShortTrackingUrl(): string
    {
        if ($this->slug) {
            if ($this->custom_domain_id && $this->customDomain && $this->customDomain->is_verified && $this->customDomain->is_active) {
                return 'https://' . rtrim($this->customDomain->domain, '/') . '/' . $this->slug;
            }
            return \App\Utils\UrlHelper::getBaseUrl() . '/' . $this->slug;
        }

        // Legacy fallback
        return \App\Utils\UrlHelper::getBaseUrl() . '/offer/gourl/' . $this->offer_id . '/' . $this->affiliate_id . '/?&tid={tid}';
    }
}