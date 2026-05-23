<?php

namespace App\Services\Admin;

use App\Models\CustomDomain;
use App\Models\OfferAffiliate;
use App\Utils\UrlHelper;
use Illuminate\Support\Str;

class AdminCustomDomainService
{
    /**
     * Register a new custom domain.
     * Returns ['status'=>1, 'domain'=>CustomDomain] or ['status'=>0, 'error'=>string]
     */
    public function addDomain(string $rawDomain): array
    {
        // Strip scheme if user pastes full URL
        $domain = preg_replace('#^https?://#i', '', rtrim(trim($rawDomain), '/'));

        if (!$this->isValidDomainFormat($domain)) {
            return ['status' => 0, 'error' => 'Invalid domain format. Use e.g. mybrand.com or track.mybrand.com', 'code' => 422];
        }

        if (CustomDomain::where('domain', $domain)->exists()) {
            return ['status' => 0, 'error' => 'Domain already registered.', 'code' => 409];
        }

        $customDomain = CustomDomain::create([
            'domain'             => $domain,
            'verification_token' => Str::uuid()->toString(),
            'is_verified'        => false,
            'is_active'          => true,
        ]);

        return ['status' => 1, 'domain' => $customDomain, 'code' => 201];
    }

    /**
     * Attempt DNS TXT record verification.
     * The user must add a TXT record:
     *   Name:  _affitrace-verify.{domain}
     *   Value: {verification_token}
     */

    public function verifyDomain(int $id): array
    {
        $customDomain = CustomDomain::find($id);

        if (!$customDomain) {
            return ['status' => 0, 'error' => 'Domain not found.', 'code' => 404];
        }

        if ($customDomain->is_verified) {
            return ['status' => 1, 'msg' => 'Domain is already verified.', 'code' => 200];
        }

        $host    = '_affitrace-verify.' . $customDomain->domain;
        $records = $this->fetchTxtRecords($host);

        if (empty($records)) {
            return [
                'status' => 0,
                'error'  => 'No TXT record found for ' . $host . '. Please add the record and try again.',
                'code'   => 422,
            ];
        }

        $tokenFound = false;
        foreach ($records as $record) {
            $txtValue = trim($record['txt'] ?? '');
            if ($txtValue === $customDomain->verification_token) {
                $tokenFound = true;
                break;
            }
        }

        if (!$tokenFound) {
            return [
                'status' => 0,
                'error'  => 'TXT record found but token does not match. Expected: ' . $customDomain->verification_token,
                'code'   => 422,
            ];
        }

        $customDomain->is_verified = true;
        $customDomain->verified_at = now();
        $customDomain->save();

        return ['status' => 1, 'msg' => 'Domain verified successfully.', 'code' => 200];
    }

    /**
     * Resolve TXT records via Google DoH (works on Windows/XAMPP),
     * with fallback to native dns_get_record().
     */
    private function fetchTxtRecords(string $host): array
    {
        $url = 'https://dns.google/resolve?' . http_build_query(['name' => $host, 'type' => 'TXT']);
        $ctx = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true]]);
        $json = @file_get_contents($url, false, $ctx);

        if ($json !== false) {
            $data = json_decode($json, true);
            if (($data['Status'] ?? -1) === 0 && !empty($data['Answer'])) {
                $txts = [];
                foreach ($data['Answer'] as $answer) {
                    if (($answer['type'] ?? 0) === 16) { // 16 = TXT
                        $txts[] = ['txt' => trim($answer['data'] ?? '', '"')];
                    }
                }
                return $txts;
            }
            return [];
        }

        // Fallback: native PHP (unreliable on Windows)
        $records = @dns_get_record($host, DNS_TXT);
        return $records ?: [];
    }


    /**
     * Toggle a domain's active status.
     * When deactivating, all links using this domain revert to base-URL short links.
     */
    public function toggleActive(int $id): array
    {
        $customDomain = CustomDomain::find($id);

        if (!$customDomain) {
            return ['status' => 0, 'error' => 'Domain not found.', 'code' => 404];
        }

        $customDomain->is_active = !$customDomain->is_active;
        $customDomain->save();

        if (!$customDomain->is_active) {
            $this->resetLinksForDomain($id);
        }

        $state = $customDomain->is_active ? 'activated' : 'deactivated';
        return ['status' => 1, 'msg' => "Domain {$state} successfully.", 'code' => 200];
    }

    /**
     * Delete a domain. Blocked if any offer-affiliate links still reference it.
     */
    public function deleteDomain(int $id): array
    {
        $customDomain = CustomDomain::find($id);

        if (!$customDomain) {
            return ['status' => 0, 'error' => 'Domain not found.', 'code' => 404];
        }

        $activeLinks = OfferAffiliate::where('custom_domain_id', $id)->count();

        if ($activeLinks > 0) {
            return [
                'status' => 0,
                'error'  => "Cannot delete: {$activeLinks} link(s) are still using this domain. Remove the domain from those links first.",
                'code'   => 409,
            ];
        }

        $customDomain->delete();
        return ['status' => 1, 'msg' => 'Domain deleted.', 'code' => 200];
    }

    /**
     * List all custom domains.
     */
    public function listDomains(): array
    {
        $domains = CustomDomain::withCount('offerAffiliates')->orderBy('id', 'desc')->get();
        return ['status' => 1, 'domains' => $domains, 'code' => 200];
    }

    /**
     * Regenerate tracking_url for all offer_affiliates that use this domain,
     * reverting them to base-URL format.
     */
    public function resetLinksForDomain(int $domainId): void
    {
        $affiliates = OfferAffiliate::where('custom_domain_id', $domainId)->get();

        foreach ($affiliates as $oa) {
            $oa->custom_domain_id = null;
            $oa->tracking_url     = UrlHelper::buildShortLinkUrl(null, $oa->slug);
            $oa->save();
        }
    }

    // -------------------------------------------------------------------------

    private function isValidDomainFormat(string $domain): bool
    {
        return (bool) preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $domain);
    }
}