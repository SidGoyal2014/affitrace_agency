<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class TenantSwitchingMiddleware
{
    public function handle(Request $request, Closure $next)
    {


        $tenantDb = $this->get_tenant_db_details_dynamically($request);

        // Store tenant DB info in session for future requests
        session([
            'tenant_db_details' => [
                'database_host' => $tenantDb['database_host'],
                'database_port' => $tenantDb['database_port'],
                'database_name' => $tenantDb['database_name'],
                'database_user' => $tenantDb['database_user'],
                'database_password' => $tenantDb['database_password']
            ]
        ]);

        //\Log::debug('TenantSwitchingMiddleware - Stored tenant info in session:', session('tenant_db_details'));

        // 3. Configure dynamic connection
        config([
            'database.connections.tenant_db_con_runtime' => [
                'driver' => 'pgsql',
                'host' => $tenantDb['database_host'],
                'port' => $tenantDb['database_port'],
                'database' => $tenantDb['database_name'],
                'username' => $tenantDb['database_user'],
                'password' => $tenantDb['database_password'],
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'disable',
            ],

            'database.default' => 'tenant_db_con_runtime',
            'auth.providers.users.connection' => 'tenant_db_con_runtime'

        ]);


        // 5. Set the default connection
        DB::setDefaultConnection('tenant_db_con_runtime');

        return $next($request);

    }//handle


    function get_tenant_db_details_dynamically($request)
    {

        $tenantDb = array();

        // Check user credentials from MAIN_db
        $user = DB::connection('pgsql_main')
            ->table('users')
            ->where('user_type', $request->user_type)
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(401, 'Invalid credentials');
        }

        // Optimization: Agency Name (username) IS the Tenant DB Name.
        // We skip the tenants table lookup.
        /*
        // Fetch tenant DB details from MAIN_db
        $tenant = DB::connection('pgsql_main')
            ->table('tenants')
            ->where('agency_id', $user->id)
            ->first();

        if (!$tenant) {
            abort(500, 'Tenant DB info not found');
        }
        */

        $tenantDb['database_host'] = env('DB_HOST');
        $tenantDb['database_port'] = env('DB_PORT');
        // Use username directly as database name
        $tenantDb['database_name'] = $user->username;
        $tenantDb['database_user'] = env('DB_USERNAME');
        $tenantDb['database_password'] = env('DB_PASSWORD');

        return $tenantDb;

    }//get_tenant_db_details_dynamically


    /*
    function get_tenant_db_details_dynamically($request){

        $tenantDb = array();


        // 2. Call App A to get DB details

        $response = Http::post(env('MAIN_WEBSITE').'/api/get_tenant', [
            'user_type' => $request->user_type,
            'email' => $request->email,
            'password' => $request->password
        ]);


        if (!$response->ok()) {
            return response()->json(['message' => 'Tenant DB info not found'], 500);
        }

        $tenantDb = $response->json()['tenant'];

        $tenantDb['database_host'] = env('DB_HOST');//'82.29.160.75';
        $tenantDb['database_port'] = env('DB_PORT');//'3306';
        $tenantDb['database_name'] = $tenantDb['database_name'];
        $tenantDb['database_user'] = env('DB_USERNAME');//'root3';
        $tenantDb['database_password'] = env('DB_PASSWORD');//'Test#123??';



        return $tenantDb;

    }//get_tenant_db_details_dynamically

    */


}
?>