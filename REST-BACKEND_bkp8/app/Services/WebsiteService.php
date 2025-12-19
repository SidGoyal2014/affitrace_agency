<?php

namespace App\Services;

class WebsiteService
{
    // Currently, WebsiteController only returns views. 
    // This service is created for consistency and future expansion.
    // Logic from invalid_country and verify methods is purely view-related 
    // but we can put any data preparation logic here if needed.

    public function getInvalidCountryViewData()
    {
        return [];
    }

    public function getVerifyViewData()
    {
        return [];
    }
}
