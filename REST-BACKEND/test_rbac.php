<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Module;
use App\Models\Designation;
use App\Enums\UserType;
use Illuminate\Support\Facades\DB;

try {
    echo "Starting RBAC logic verification...\n";

    // 1. Check if modules are seeded
    $modulesCount = Module::count();
    echo "- Modules count: {$modulesCount} (Expected > 0)\n";
    if ($modulesCount === 0) throw new Exception("Modules not seeded.");

    $dashboardModule = Module::where('slug', 'dashboard')->first();

    // 2. Create a test Role
    $role = Role::create([
        'name' => 'Test Affiliate Role',
        'user_type' => 'affiliate',
        'description' => 'A role for testing permissions',
        'is_default' => false,
    ]);
    echo "- Created Test Role (ID: {$role->id})\n";

    // 3. Attach permissions
    $role->modules()->attach($dashboardModule->id, [
        'can_view' => true,
        'can_create' => false,
        'can_edit' => false,
        'can_delete' => false,
    ]);
    echo "- Attached 'dashboard' module view permission to the role.\n";

    // 4. Create a test Designation
    $designation = Designation::create([
        'name' => 'Test Staff',
        'description' => 'Staff designation',
    ]);
    echo "- Created Test Designation (ID: {$designation->id})\n";

    // 5. Create a test User and assign Role/Designation
    $user = new User();
    $user->code = 'EMP-' . uniqid();
    $user->name = 'Test Employee User';
    $user->email = 'testemployee@example.com';
    $user->password = bcrypt('password');
    $user->enc_pwd = base64_encode('password');
    $user->sec_key = 'sec_key';
    $user->user_type = 'employee';
    $user->role_id = $role->id;
    $user->designation_id = $designation->id;
    $user->status = 'active';
    $user->save();
    echo "- Created Test Employee User (ID: {$user->id})\n";

    // 6. Test logic helpers
    $hasDashView = $user->hasModuleAccess('dashboard', 'can_view');
    $hasDashEdit = $user->hasModuleAccess('dashboard', 'can_edit');
    $hasOffersView = $user->hasModuleAccess('offers', 'can_view');

    echo "- [Assert] User has dashboard view access: " . ($hasDashView ? 'PASS' : 'FAIL') . "\n";
    echo "- [Assert] User DOES NOT have dashboard edit access: " . (!$hasDashEdit ? 'PASS' : 'FAIL') . "\n";
    echo "- [Assert] User DOES NOT have offers view access: " . (!$hasOffersView ? 'PASS' : 'FAIL') . "\n";

    if (!$hasDashView || $hasDashEdit || $hasOffersView) {
        throw new Exception("Role helper tests failed.");
    }

    echo "Cleaning up test data...\n";
    $user->delete();
    $role->modules()->detach();
    $role->delete();
    $designation->delete();

    echo "✅ SUCCESS: Core RBAC logics verified successfully.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

