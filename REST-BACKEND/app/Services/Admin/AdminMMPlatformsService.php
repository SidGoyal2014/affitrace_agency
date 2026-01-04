<?php

namespace App\Services\Admin;

use App\Models\MmPlatform;

class AdminMMPlatformsService
{
    public function listPlatforms()
    {
        $platforms = MmPlatform::all();

        // Transform for UI: map title -> name, is_active -> active
        $transformed = $platforms->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->title,
                'active' => $p->is_active === 'Y',
                'description' => $p->description,
            ];
        });

        return [
            'module_name' => "List mobile marketing platforms",
            'platforms' => $transformed
        ];
    }

    public function setupPlatform($id)
    {
        $platform = MmPlatform::find($id);

        return [
            'module_name' => "Setup platform",
            'platform' => $platform
        ];
    }
}
