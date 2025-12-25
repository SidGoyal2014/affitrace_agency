<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferTracking;
use Exception;

class CsvImportService
{
    public function import($file, $type)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        // Normalize header needed? Assuming user follows template.
        // Convert header to lowercase/trimmed for safer mapping
        $header = array_map(function ($h) {
            return trim(strtolower($h));
        }, $header);

        $processed = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                // Assoc array
                if (count($row) !== count($header)) {
                    // mismatch columns
                    continue;
                }
                $data = array_combine($header, $row);

                try {
                    switch ($type) {
                        case 'advertiser':
                            $this->importAdvertiser($data);
                            break;
                        case 'affiliate':
                            $this->importAffiliate($data);
                            break;
                        case 'clicks':
                            $this->importClick($data);
                            break;
                        case 'conversions':
                            $this->importConversion($data);
                            break;
                        default:
                            throw new Exception("Invalid import type");
                    }
                    $processed++;
                } catch (Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($processed + $failed + 1) . ": " . $e->getMessage();
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            fclose($handle);
        }

        return [
            'total' => $processed + $failed,
            'processed' => $processed,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 5) // Return first 5 errors
        ];
    }

    protected function importAdvertiser($data)
    {
        $this->importUser($data, 'advertiser');
    }

    protected function importAffiliate($data)
    {
        $this->importUser($data, 'affiliate');
    }

    protected function importUser($data, $role)
    {
        $email = $this->getVal($data, ['email']);
        if (!$email)
            throw new Exception("Email required");

        $user = User::where('email', $email)->first();
        if (!$user && !empty($data['external_id'])) {
            $user = User::where('external_user_id', $data['external_id'])->first();
        }

        if (!$user) {
            $user = new User();
            $user->email = $email;
            $user->password = bcrypt('Password@123');
            $user->user_type = $role;
            $user->status = 'Active'; // Default status
        }

        // Basic Info
        $user->name = $this->getVal($data, ['name', 'company', 'username']) ?? 'Unknown';
        $user->company = $this->getVal($data, ['company']) ?? '';

        // Extended Fields
        $user->phone = $this->getVal($data, ['phone', 'mobile', 'cell', 'phone_number']);
        $user->skype = $this->getVal($data, ['skype', 'skype_id', 'im', 'messenger']);
        $user->address = $this->getVal($data, ['address', 'address1', 'street']);
        $user->city = $this->getVal($data, ['city', 'town']);
        $user->state = $this->getVal($data, ['state', 'region', 'province']);
        $user->country = $this->getVal($data, ['country', 'country_code']);
        $user->zipcode = $this->getVal($data, ['zip', 'zipcode', 'postal_code']);

        // Status Mapping
        $statusInput = strtolower($this->getVal($data, ['status', 'state']) ?? '');
        if (in_array($statusInput, ['active', 'approved', 'enabled'])) {
            $user->status = 'Active';
        } elseif (in_array($statusInput, ['inactive', 'blocked', 'banned', 'pending'])) {
            $user->status = ($statusInput == 'pending') ? 'Pending' : 'Blocked';
        }

        if (!empty($data['external_id'])) {
            $user->external_user_id = $data['external_id'];
        }
        $user->save();
    }

    protected function importClick($data)
    {
        // Require Offer and Affiliate
        $offerId = $this->getVal($data, ['offer_id', 'external_offer_id', 'campaign_id']);
        $affId = $this->getVal($data, ['affiliate_id', 'external_affiliate_id', 'aff_id']);
        $tid = $this->getVal($data, ['transaction_id', 'tid', 'click_id']);

        if (!$offerId || !$affId || !$tid) {
            throw new Exception("Missing required fields (Offer ID, Affiliate ID, or Transaction ID)");
        }

        // Resolve Offer
        $offer = Offer::find($offerId);
        if (!$offer) {
            $offer = Offer::where('external_offer_id', $offerId)->first();
        }
        if (!$offer)
            throw new Exception("Offer not found: $offerId");

        // Resolve Affiliate
        $aff = User::find($affId);
        if (!$aff) {
            $aff = User::where('external_user_id', $affId)->first();
        }
        if (!$aff)
            throw new Exception("Affiliate not found: $affId");

        // Create Tracking
        $tracking = new OfferTracking();
        $tracking->oid = $offer->id;
        $tracking->aid = $aff->id;
        $tracking->tid = $tid;
        $tracking->aff_click_id = $this->getVal($data, ['aff_click_id', 'aff_click', 'sub_id']) ?? ''; // Critical for postbacks

        // Basic Info
        $tracking->ip_address = $this->getVal($data, ['ip', 'ip_address']) ?? '0.0.0.0';
        $tracking->user_agent = $this->getVal($data, ['ua', 'user_agent']) ?? '';
        $tracking->offer_name = $offer->offer_name; // Snapshot of name
        $tracking->country = $this->getVal($data, ['country', 'country_code']) ?? 'XX';

        // Device / OS / Browser
        $tracking->os = $this->getVal($data, ['os', 'operating_system']);
        $tracking->os_ver = $this->getVal($data, ['os_version', 'os_ver']);
        $tracking->browser = $this->getVal($data, ['browser', 'browser_name']);
        $tracking->brand = $this->getVal($data, ['brand', 'device_brand', 'vendor']);
        $tracking->device_id = $this->getVal($data, ['device_id', 'device_type']) ?? 1; // 1=Mobile, 2=Desktop etc (Mapping needed if strictly int)
        $tracking->isp = $this->getVal($data, ['isp', 'carrier']);
        $tracking->language = $this->getVal($data, ['language', 'lang']);

        // Mobile Identifiers
        $tracking->android_id = $this->getVal($data, ['android_id', 'gaid', 'google_aid']); // gaid often maps here
        $tracking->google_aid = $this->getVal($data, ['google_aid', 'gaid']);
        $tracking->iosidfa = $this->getVal($data, ['idfa', 'ios_idfa', 'ifa']);

        // Sub Parameters (Affiliate Source params)
        $tracking->aff_sub1 = $this->getVal($data, ['aff_sub1', 'sub1']);
        $tracking->aff_sub2 = $this->getVal($data, ['aff_sub2', 'sub2']);
        $tracking->aff_sub3 = $this->getVal($data, ['aff_sub3', 'sub3']);
        $tracking->aff_sub4 = $this->getVal($data, ['aff_sub4', 'sub4']);
        $tracking->aff_sub5 = $this->getVal($data, ['aff_sub5', 'sub5']);

        // Source / Sub IDs
        $tracking->source = $this->getVal($data, ['source', 'sub_id', 'pub_id']);
        $tracking->sub_aff_id = $this->getVal($data, ['sub_aff_id']);

        // Internal Tracking Subs (if separate from aff_sub)
        $tracking->sub1 = $this->getVal($data, ['internal_sub1', 's1']);
        $tracking->sub2 = $this->getVal($data, ['internal_sub2', 's2']);

        $tracking->adv_id = $offer->advertiser_id ?? 0;

        // Use provided timestamp or now
        $ts = $this->getVal($data, ['timestamp', 'created_at', 'date', 'click_time']);
        $tracking->created_at = $ts ? date('Y-m-d H:i:s', strtotime($ts)) : now();
        $tracking->updated_at = $tracking->created_at;

        $tracking->save();
    }

    protected function importConversion($data)
    {
        $tid = $this->getVal($data, ['transaction_id', 'tid', 'click_id']);
        if (!$tid)
            throw new Exception("Transaction ID required");

        $tracking = OfferTracking::where('tid', $tid)->first();
        if (!$tracking)
            throw new Exception("Click not found for TID: $tid");

        $tracking->aff_pricing = $this->getVal($data, ['payout', 'affiliate_payout']) ?? 0;
        $tracking->ad_pricing = $this->getVal($data, ['revenue', 'income']) ?? 0;
        $tracking->event = $this->getVal($data, ['event', 'event_name']) ?? 'default'; // 'default' or actual event

        // Status updates? Assuming valid conversion import means approved/converted
        // Actual status field depends on your schema (status? or just presence of pricing?)
        // Let's assume we don't have a status column on typical click tables, but maybe 'is_conversion'?
        // Checking TrackingService, it updates pricing.

        $ts = $this->getVal($data, ['timestamp', 'conversion_time', 'date']);
        if ($ts) {
            $tracking->updated_at = date('Y-m-d H:i:s', strtotime($ts));
        } else {
            $tracking->updated_at = now();
        }

        $tracking->save();
    }

    private function getVal($data, $keys)
    {
        foreach ($keys as $k) {
            if (isset($data[$k]) && $data[$k] !== '')
                return $data[$k];
        }
        return null;
    }
}
