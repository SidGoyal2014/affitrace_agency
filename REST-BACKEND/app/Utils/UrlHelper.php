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
}
