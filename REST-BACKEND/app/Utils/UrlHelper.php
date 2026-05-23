<?php
namespace App\Utils;

class UrlHelper
{
    public static function getBaseUrl()
    {
        // 1. Try specific env variable first (User Request)
        $externalUrl = env('EXTERNAL_SYSTEM_URL');

        if (!empty($externalUrl)) {
            return rtrim($externalUrl, '/');
        }

        // 2. Fallback to standard Laravel APP_URL
        return rtrim(config('app.url'), '/');
    }

    /**
     * Build a short link URL.
     * If a verified+active custom domain is supplied, use it; otherwise use the base URL.
     */
    public static function buildShortLinkUrl(?string $customDomain, string $slug): string
    {
        if ($customDomain) {
            return 'https://' . rtrim($customDomain, '/') . '/' . $slug;
        }

        return self::getBaseUrl() . '/' . $slug;
    }    

}
