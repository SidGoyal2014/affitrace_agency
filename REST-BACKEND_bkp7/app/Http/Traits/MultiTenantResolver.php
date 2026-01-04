<?php
namespace App\Http\Traits;

use Illuminate\Http\Request;

trait MultiTenantResolver
{
    /**
     * Resolve the Agency Username using the 3-Strategy Resolver.
     * 1. Hostname (Dashboard/Clicks)
     * 2. Click ID Prefix (Postbacks)
     * 3. 'eai' Parameter (Fallback/Legacy)
     *
     * @param \Illuminate\Http\Request|null $request
     * @return string|null
     */
    public function resolve_agency_username($request = null)
    {
        if (!$request) {
            $request = request();
        }

        $agency_username = null;

        // STRATEGY 0: Header (For Single-Domain Dashboard)
        // Frontend sends this after login
        if ($header_id = $request->header('X-Agency-ID')) {
            return $header_id;
        }

        // STRATEGY 1: Hostname (Optional fallback)
        // e.g. "agency-cool.affitrace.com"
        $host = $_SERVER['HTTP_HOST'] ?? '';

        // Filter out main domains and localhost dev environments
        if (
            $host &&
            $host !== 'api.affitrace.com' &&
            $host !== 'app.affitrace.com' &&
            $host !== 'localhost' &&
            !str_contains($host, '127.0.0.1')
        ) {
            $parts = explode('.', $host);
            // Expecting at least [subdomain, domain, com]
            if (count($parts) >= 2) {
                // Taking the first part as agency username
                $agency_username = $parts[0];
                return $agency_username;
            }
        }

        // STRATEGY 2: Click ID Prefix (For Postbacks)
        // e.g. ?aff_click_id=agency-cool-9f8a7d...
        if ($request->has('aff_click_id')) {
            $click_id = $request->input('aff_click_id');
            // Check for the separator
            if (str_contains($click_id, '-')) {
                $parts = explode('-', $click_id);
                $agency_username = $parts[0];
                return $agency_username;
            }
        }

        // STRATEGY 3: Explicit Parameter (Fallback / Testing / Legacy)
        // e.g. ?eai=agency-cool
        if ($request->has('eai')) {
            $agency_username = $request->input('eai');
            return $agency_username;
        }

        return null;
    }
}
