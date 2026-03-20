<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate resolving the agency ID as requested from the frontend
$request = Illuminate\Http\Request::create('/api/admin/invoices', 'POST');
$request->headers->set('X-Agency-ID', 'ebizeoOfficial');

$resolver = new class {
    use App\Http\Traits\MultiTenantResolver;
};
$agencyId = $resolver->resolve_agency_username($request);
echo "Agency ID: " . $agencyId . PHP_EOL;

$tenantData = Cache::remember("tenant:{$agencyId}", 3600, function () use ($agencyId) {
    return (object) ['database_name' => $agencyId];
});
echo "Tenant DB Name: " . $tenantData->database_name . PHP_EOL;

Config::set('database.connections.tenant_db_con_runtime', [
    'driver' => 'pgsql',
    'host' => config('database.connections.pgsql.host'),
    'port' => config('database.connections.pgsql.port'),
    'database' => $tenantData->database_name,
    'username' => config('database.connections.pgsql.username'),
    'password' => config('database.connections.pgsql.password'),
    'charset' => 'utf8',
    'prefix' => '',
    'search_path' => 'public',
]);
Config::set('database.default', 'tenant_db_con_runtime');
DB::purge('tenant_db_con_runtime');
DB::reconnect('tenant_db_con_runtime');

try {
    $dbConnected = DB::connection('tenant_db_con_runtime')->getDatabaseName();
    echo "Successfully connected to: " . $dbConnected . PHP_EOL;

    $cols = DB::connection('tenant_db_con_runtime')->select("SELECT column_name FROM information_schema.columns WHERE table_name = 'invoices'");
    echo "Columns in invoices table: ";
    $colNames = [];
    foreach ($cols as $col) {
        $colNames[] = $col->column_name;
    }
    echo implode(", ", $colNames) . PHP_EOL;

} catch (\Exception $e) {
    echo "Error connecting: " . $e->getMessage() . PHP_EOL;
}
