<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Http\Traits\MultiTenantResolver;

class ApiSetTenantConnection
{
    use MultiTenantResolver;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Get Agency ID using the 3-Strategy Resolver
        $agencyId = $this->resolve_agency_username($request);

        if (!$agencyId) {
            return response()->json(['error' => 'Agency identity not found. Check hostname or aff_click_id.'], 400);
        }

        // 2. Find the Tenant in Main DB (with caching for performance)
        // Cache tenant lookup for 1 hour to reduce DB queries
        try {
            $tenantData = Cache::remember("tenant:{$agencyId}", 3600, function () use ($agencyId) {
                // Find the Agency User first (to get their ID)
                $agencyUser = DB::connection('mysql_main')
                    ->table('users')
                    ->where('username', $agencyId)
                    ->where('user_type', \App\Enums\UserType::Admin->value)
                    ->first();

                if (!$agencyUser) {
                    return null;
                }

                // Find the Tenant Record
                $tenant = DB::connection('mysql_main')
                    ->table('tenants')
                    ->where('agency_id', $agencyUser->id)
                    ->first();

                return $tenant;
            });

            if (!$tenantData) {
                return response()->json(['error' => 'Agency not found: ' . $agencyId], 404);
            }

            // 3. Configure the Tenant Connection
            // We use the env variables for DB user/pass as they seem shared in this setup
            // or we could use specific ones if stored in tenant record.
            // Based on TenantSwitchingMiddleware logic:

            $dbName = $tenantData->database_name;
            $dbHost = env('DB_HOST');
            $dbPort = env('DB_PORT');
            $dbUser = env('DB_USERNAME');
            $dbPassword = env('DB_PASSWORD');

            Config::set('database.connections.tenant_db_con_runtime', [
                'driver' => 'mysql',
                'host' => $dbHost,
                'port' => $dbPort,
                'database' => $dbName,
                'username' => $dbUser,
                'password' => $dbPassword,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ]);

            Config::set('database.default', 'tenant_db_con_runtime');
            Config::set('auth.providers.users.connection', 'tenant_db_con_runtime');

            DB::purge('tenant_db_con_runtime');
            DB::reconnect('tenant_db_con_runtime');
            DB::setDefaultConnection('tenant_db_con_runtime');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect to tenant database: ' . $e->getMessage()], 500);
        }

        return $next($request);
    }
}
