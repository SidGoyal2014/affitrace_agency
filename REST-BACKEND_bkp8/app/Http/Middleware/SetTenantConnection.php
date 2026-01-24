<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetTenantConnection
{
    /**
     * Handle an incoming request.
     * This middleware applies tenant database connection on every request
     * if tenant info exists in session.
     */
    public function handle(Request $request, Closure $next){

        /*
        //\Log::debug('SetTenantConnection - MIDDLEWARE STARTED for URL: ' . $request->url());


        // Skip tenant connection for public routes
        // Check if route is public by checking if it doesn't require auth
        $route = $request->route();
        
        if ($route) {
            $middleware = $route->middleware();
            
            // Skip if route doesn't have 'auth' middleware (meaning it's public)
            if (!in_array('auth', $middleware)) {
                //\Log::debug('SetTenantConnection - Skipping for public route (no auth middleware): ' . $request->path());
                return $next($request);
            }
        }
        */
        
        $response = $this->skip_tenant_connection_for_public_routes($request, $next);
        if ($response) return $response;
        

        try {
            // Check if tenant DB info exists in session
            if (session()->has('tenant_db_details')) {
                $tenantDb = session('tenant_db_details');
                
                //\Log::debug('SetTenantConnection - Found tenant DB info:', $tenantDb);
                
                // Configure dynamic connection using session data
                config([
                    'database.connections.tenant_db_con_runtime' => [
                        'driver' => 'mysql',
                        'host' => $tenantDb['database_host'],
                        'port' => $tenantDb['database_port'],
                        'database' => $tenantDb['database_name'],
                        'username' => $tenantDb['database_user'],
                        'password' => $tenantDb['database_password'],
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => '',
                        'strict' => true,
                    ],

                    'database.default' => 'tenant_db_con_runtime',
                    'auth.providers.users.connection' => 'tenant_db_con_runtime'
                ]);
                
                // Set the default connection
                DB::setDefaultConnection('tenant_db_con_runtime');
                
                \Log::debug('SetTenantConnection - DB connection set to: ' . DB::getDefaultConnection());
            } else {
                //\Log::debug('SetTenantConnection - No tenant DB info found in session');
            }
        } catch (\Exception $e) {
            // Log the error but don't break the request
            //\Log::error('SetTenantConnection middleware error: ' . $e->getMessage());
        }
        
        return $next($request);

    }//handle


    public function skip_tenant_connection_for_public_routes($request, $next){


        //\Log::debug('SetTenantConnection - MIDDLEWARE STARTED for URL: ' . $request->url());


        // Skip tenant connection for public routes
        // Check if route is public by checking if it doesn't require auth
        $route = $request->route();
        
        if ($route) {
            $middleware = $route->middleware();
            
            // Skip if route doesn't have 'auth' middleware (meaning it's public)
            if (!in_array('auth', $middleware)) {
                //\Log::debug('SetTenantConnection - Skipping for public route (no auth middleware): ' . $request->path());
                return $next($request);
            }
        }
        
        // If we reach here, it's an authenticated route, continue processing
        return null; // Don't return $next($request) here, let the main handle method continue


    }//skip_tenant_connection_for_public_routes


}