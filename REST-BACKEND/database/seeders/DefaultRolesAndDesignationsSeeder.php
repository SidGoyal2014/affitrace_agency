<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultRolesAndDesignationsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Designations
        $designations = [
            ['name' => 'Affiliate Manager', 'description' => 'Manages assigned affiliates and their campaigns.'],
            ['name' => 'Advertiser Manager', 'description' => 'Manages assigned advertisers and offers.'],
            ['name' => 'Finance Manager', 'description' => 'Handles invoices, billing, and weekly/monthly payouts.'],
            ['name' => 'Observer', 'description' => 'Read-only access with hidden/masked financial values.'],
        ];

        foreach ($designations as $d) {
            DB::table('designations')->updateOrInsert(
                ['name' => $d['name']],
                array_merge($d, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Get module IDs
        $modules = DB::table('modules')->pluck('id', 'slug')->toArray();

        // Helper to insert role module pivot
        $setPermission = function ($roleId, $moduleSlug, $view, $create, $edit, $delete) use ($modules) {
            if (isset($modules[$moduleSlug])) {
                DB::table('role_module')->updateOrInsert(
                    ['role_id' => $roleId, 'module_id' => $modules[$moduleSlug]],
                    [
                        'can_view' => $view,
                        'can_create' => $create,
                        'can_edit' => $edit,
                        'can_delete' => $delete,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        };

        // 2. Seed Affiliate Manager Role
        $amRoleId = DB::table('roles')->insertGetId([
            'name' => 'Affiliate Manager Role',
            'user_type' => 'employee',
            'description' => 'Access to assigned affiliates, dashboard, and reports.',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $setPermission($amRoleId, 'dashboard', true, false, false, false);
        $setPermission($amRoleId, 'offers', true, false, false, false);
        $setPermission($amRoleId, 'affiliates', true, true, true, false);
        $setPermission($amRoleId, 'reports', true, false, false, false);
        $setPermission($amRoleId, 'invoices', true, true, true, false);

        // 3. Seed Advertiser Manager Role
        $advRoleId = DB::table('roles')->insertGetId([
            'name' => 'Advertiser Manager Role',
            'user_type' => 'employee',
            'description' => 'Access to assigned advertisers, offers, and dashboard.',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $setPermission($advRoleId, 'dashboard', true, false, false, false);
        $setPermission($advRoleId, 'offers', true, true, true, false);
        $setPermission($advRoleId, 'advertisers', true, true, true, false);
        $setPermission($advRoleId, 'reports', true, false, false, false);
        $setPermission($advRoleId, 'invoices', true, true, true, false);

        // 4. Seed Finance Manager Role
        $finRoleId = DB::table('roles')->insertGetId([
            'name' => 'Finance Manager Role',
            'user_type' => 'employee',
            'description' => 'Full access to billing, invoices, payments, and reports.',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $setPermission($finRoleId, 'dashboard', true, false, false, false);
        $setPermission($finRoleId, 'reports', true, false, false, false);
        $setPermission($finRoleId, 'invoices', true, true, true, true);
        $setPermission($finRoleId, 'payments', true, true, true, false);

        // 5. Seed Observer Role
        $obsRoleId = DB::table('roles')->insertGetId([
            'name' => 'Observer Role',
            'user_type' => 'employee',
            'description' => 'Read-only access with hidden financial values.',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $setPermission($obsRoleId, 'dashboard', true, false, false, false);
        $setPermission($obsRoleId, 'offers', true, false, false, false);
        $setPermission($obsRoleId, 'affiliates', true, false, false, false);
        $setPermission($obsRoleId, 'advertisers', true, false, false, false);
        $setPermission($obsRoleId, 'reports', true, false, false, false);
    }
}
