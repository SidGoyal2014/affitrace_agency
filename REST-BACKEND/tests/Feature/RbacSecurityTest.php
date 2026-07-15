<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Module;
use App\Enums\UserType;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RbacSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed modules
        $this->seed(\Database\Seeders\ModuleSeeder::class);
    }

    /**
     * Verify that an affiliate cannot hijack the administrator account.
     */
    public function test_affiliate_cannot_update_admin_profile()
    {
        $admin = User::factory()->create([
            'email' => 'admin@affitrace.com',
            'user_type' => 'admin',
        ]);

        $affiliate = User::factory()->create([
            'email' => 'affiliate@affitrace.com',
            'user_type' => 'affiliate',
        ]);

        Sanctum::actingAs($affiliate, ['access-api']);

        $response = $this->postJson('/api/affiliate/profile', [
            'uid' => $admin->id,
            'name' => 'Hacked Admin',
            'email' => 'hacked@affitrace.com',
        ]);

        $response->assertStatus(200);

        // Assert admin profile remains intact
        $admin->refresh();
        $this->assertEquals('admin@affitrace.com', $admin->email);

        // Assert affiliate profile was updated instead
        $affiliate->refresh();
        $this->assertEquals('hacked@affitrace.com', $affiliate->email);
    }

    /**
     * Verify that employees cannot elevate their own role.
     */
    public function test_employee_cannot_self_assign_privileges()
    {
        $employeeRole = Role::create([
            'name' => 'Clerk',
            'user_type' => 'employee',
            'is_default' => true,
        ]);

        $adminRole = Role::create([
            'name' => 'Admin Role',
            'user_type' => 'admin',
        ]);

        $employee = User::factory()->create([
            'user_type' => 'employee',
            'role_id' => $employeeRole->id,
        ]);

        Sanctum::actingAs($employee, ['access-api']);

        $response = $this->postJson('/api/employee/update_profile', [
            'uid' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'role_id' => $adminRole->id,
        ]);

        $response->assertStatus(403);
        $employee->refresh();
        $this->assertEquals($employeeRole->id, $employee->role_id);
    }

    /**
     * Verify affiliate API endpoint is guarded by module.access middleware.
     */
    public function test_affiliate_access_blocked_without_permission()
    {
        $roleWithoutBrowse = Role::create([
            'name' => 'Restricted Affiliate',
            'user_type' => 'affiliate',
        ]);

        $affiliate = User::factory()->create([
            'user_type' => 'affiliate',
            'role_id' => $roleWithoutBrowse->id,
        ]);

        Sanctum::actingAs($affiliate, ['access-api']);

        $response = $this->getJson('/api/affiliate/offers/browse');
        $response->assertStatus(403);
    }
}
