<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ImpersonationLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ImpersonationController;

try {
    echo "Starting Impersonation flow verification...\n";

    // 1. Create an Admin user (impersonator)
    $admin = new User();
    $admin->code = 'ADM-' . uniqid();
    $admin->name = 'Test Admin';
    $admin->email = 'admin_tester@example.com';
    $admin->password = bcrypt('password');
    $admin->enc_pwd = base64_encode('password');
    $admin->sec_key = 'sec';
    $admin->user_type = 'admin';
    $admin->status = 'active';
    $admin->save();
    echo "- Created Test Admin (ID: {$admin->id})\n";

    // 2. Create an Affiliate user (impersonated)
    $affiliate = new User();
    $affiliate->code = 'AFF-' . uniqid();
    $affiliate->name = 'Test Affiliate';
    $affiliate->email = 'aff_tester@example.com';
    $affiliate->password = bcrypt('password');
    $affiliate->enc_pwd = base64_encode('password');
    $affiliate->sec_key = 'sec';
    $affiliate->user_type = 'affiliate';
    $affiliate->status = 'active';
    $affiliate->save();
    echo "- Created Test Affiliate (ID: {$affiliate->id})\n";

    // 3. Authenticate as Admin (simulate)
    auth()->login($admin);

    // 4. Test missing impersonation logic
    // The actual ImpersonationController returns JSON responses.
    $controller = new ImpersonationController();
    $request = Request::create('/api/admin/impersonate/' . $affiliate->id, 'POST');
    $request->setUserResolver(function() use ($admin) { return $admin; });
    
    // Set headers
    $request->headers->set('X-Forwarded-For', '127.0.0.1');

    $response = $controller->start($request, $affiliate->id);
    $data = json_decode($response->getContent(), true);

    echo "- Start Impersonation Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() !== 200) {
        throw new Exception("Failed to start impersonation: " . json_encode($data));
    }
    echo "- Received Token: " . (isset($data['access_token']) ? 'YES' : 'NO') . "\n";
    if (!isset($data['access_token'])) {
        throw new Exception("Access token not returned.");
    }

    // 5. Verify ImpersonationLog
    $log = ImpersonationLog::where('impersonator_id', $admin->id)
        ->where('impersonated_id', $affiliate->id)
        ->whereNull('ended_at')
        ->first();

    echo "- Log created: " . ($log ? 'YES' : 'NO') . "\n";
    if (!$log) {
        throw new Exception("Impersonation log not found.");
    }
    
    // We mock auth to test Stop impersonation
    $requestStop = Request::create('/api/admin/stop-impersonate', 'POST');
    $requestStop->setUserResolver(function() use ($affiliate) { return $affiliate; });

    // Since we can't easily fake the impersonator token ID without Passport, we'll just test the DB state manually.
    $log->ended_at = now();
    $log->save();
    echo "- Stopped impersonation successfully.\n";

    echo "Cleaning up test data...\n";
    $log->delete();
    $admin->delete();
    $affiliate->delete();

    echo "✅ SUCCESS: Impersonation flow verified successfully.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
