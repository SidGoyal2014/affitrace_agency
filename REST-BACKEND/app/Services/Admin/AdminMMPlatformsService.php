<?php

namespace App\Services\Admin;

class AdminMMPlatformsService
{
    public function listPlatforms()
    {
        return [
            'module_name' => "List mobile marketing platforms",
            'platforms' => []
        ];
    }

    public function setupPlatform($id)
    {
        return [
            'module_name' => "Setup platform",
            'platform_id' => $id
        ];
    }
}
