<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['slug' => 'dashboard',       'name' => 'Dashboard',       'description' => 'Main dashboard overview'],
            ['slug' => 'offers',          'name' => 'Offers',          'description' => 'Offer management'],
            ['slug' => 'affiliates',      'name' => 'Affiliates',      'description' => 'Affiliate management'],
            ['slug' => 'advertisers',     'name' => 'Advertisers',     'description' => 'Advertiser management'],
            ['slug' => 'reports',         'name' => 'Reports',         'description' => 'Analytics and reports'],
            ['slug' => 'mmp',             'name' => 'MMP',             'description' => 'Mobile Measurement Partners'],
            ['slug' => 'payments',        'name' => 'Payments',        'description' => 'Payment plans and subscriptions'],
            ['slug' => 'integrations',    'name' => 'Integrations',    'description' => 'Third-party integrations'],
            ['slug' => 'invoices',        'name' => 'Invoices',        'description' => 'Invoice management'],
            ['slug' => 'role_management', 'name' => 'Role Management', 'description' => 'Roles and permissions management'],
            ['slug' => 'impersonation',   'name' => 'Impersonation',   'description' => 'Login-as affiliate/advertiser'],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $module['slug']],
                array_merge($module, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
