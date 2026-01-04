<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ReportFilterDTO
{
    public ?string $from = null;
    public ?string $to = null;
    public ?int $offer_id = null;
    public ?int $affiliate_id = null;
    public ?int $advertiser_id = null;
    public ?string $country = null;
    public ?string $os = null;
    public ?string $browser = null;
    public ?int $device = null;
    public ?int $category = null;
    public ?string $conversion_status = null;
    public int $limit = 50;

    /**
     * Create a ReportFilterDTO from a Request object
     */
    public static function fromRequest(Request $request): self
    {
        $dto = new self();

        $dto->from = $request->input('from');
        $dto->to = $request->input('to');
        $dto->offer_id = $request->input('offer_id') ? (int) $request->input('offer_id') : null;
        $dto->affiliate_id = $request->input('affiliate_id') ? (int) $request->input('affiliate_id') : null;
        $dto->advertiser_id = $request->input('advertiser_id') ? (int) $request->input('advertiser_id') : null;
        $dto->country = $request->input('country');
        $dto->os = $request->input('os');
        $dto->browser = $request->input('browser');
        $dto->device = $request->input('device') ? (int) $request->input('device') : null;
        $dto->category = $request->input('category') ? (int) $request->input('category') : null;
        $dto->conversion_status = $request->input('conversion_status');
        $dto->limit = $request->input('limit', 50);

        return $dto;
    }

    /**
     * Check if any filters are applied
     */
    public function hasFilters(): bool
    {
        return $this->from !== null
            || $this->to !== null
            || $this->offer_id !== null
            || $this->affiliate_id !== null
            || $this->advertiser_id !== null
            || $this->country !== null
            || $this->os !== null
            || $this->browser !== null
            || $this->device !== null
            || $this->category !== null
            || $this->conversion_status !== null;
    }

    /**
     * Get all filters as an array
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'offer_id' => $this->offer_id,
            'affiliate_id' => $this->affiliate_id,
            'advertiser_id' => $this->advertiser_id,
            'country' => $this->country,
            'os' => $this->os,
            'browser' => $this->browser,
            'device' => $this->device,
            'category' => $this->category,
            'conversion_status' => $this->conversion_status,
            'limit' => $this->limit,
        ];
    }
}
