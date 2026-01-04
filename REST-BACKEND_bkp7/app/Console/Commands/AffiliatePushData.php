<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliatePushData extends Command{
    
    protected $signature = 'affiliate:push-data';
    protected $description = 'Push data to affiliate endpoints for all tenants and update statuses';

    public function handle(){

        $this->info('Fetching tenants from App1...');
        Log::info('Fetching tenants from App1....');
        $tenants = DB::connection('mysql_main')->table('tenants')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');
            return Command::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->info("Processing tenant: {$tenant->database_name}");

            // Switch DB to tenant
            config([
                'database.connections.process_tenant_con' => [
                    'driver' => 'mysql',
                    'host' => env('MAIN_DB_HOST'),//$tenant->database_host,
                    'port' => 3306,//$tenant->database_port,
                    'database' => $tenant->database_name,
                    'username' => env('MAIN_DB_USERNAME'),//$tenant->database_user,
                    'password' => env('MAIN_DB_PASSWORD'),//$tenant->database_password ?: env('MAIN_DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ]);
            DB::purge('process_tenant_con');
            DB::reconnect('process_tenant_con');

            try {
                // === Place your process logic here ===
                $this->runProcessLogic($tenant);
                $this->info("Completed: {$tenant->database_name}");

            } catch (\Throwable $e) {
                $this->error("Failed for {$tenant->database_name}: " . $e->getMessage());

            }
        }

        $this->info('All tenants processed successfully.');
        return Command::SUCCESS;

    }//handle

    /**
     * Move your process.php logic into this method
     */
    protected function runProcessLogic($tenant){

        $sql = "select 
                OT.id, OT.offer_id, OT.aff_id, OT.aff_click_id, OT.sub2, APC.url 
            FROM
                offer_trackings OT 
                    INNER JOIN 
                affiliate_postback_columns APC
                    ON
                OT.aff_id = APC.uid
            WHERE
                APC.url != '' 
                    AND
                OT.aff_click_id != ''
                    AND
                OT.is_transferred = 'no'
            ";

        // Example: use DB::connection('tenant') for queries
        // Replace this with your process.php logic
        $results = DB::connection('process_tenant_con')->select($sql);

        if (count($results) > 0) {
            $str = '';
            foreach ($results as $rec) {
                $url = str_replace('{sub2}', $rec->sub2, $rec->url);

                $this->send_curl_get_request($url, $rec->offer_id, $rec->aff_id);

                $str .= $rec->id . ',';

            }

            $str = rtrim($str, ',');
            $updateSql = "UPDATE offer_trackings SET is_transferred = 'yes', transferred_at = NOW() WHERE id IN ($str)";

            DB::connection('process_tenant_con')->statement($updateSql);

            $this->removeExtraLogs();

            Log::info("Updated {$ids} records as transferred.");

        } else {
            $this->saveLogs("No records available for sending data to affiliate", null, null);
            Log::info("No records found for processing.");

        }



    }//runProcessLogic


    protected function sendCurlGetRequest($url, $offer_id, $aff_id){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $timeStr = "Sent At: ------".date('j M, Y H:i:s')."---------------";

        if (curl_exec($ch) === false) {
            $str = "\n".$timeStr.'Curl error: ' . curl_error($ch). " - URL: " . $url. "\n";
        } else {
            $info = curl_getinfo($ch);
            $str = "\n".$timeStr.'Took '. $info['total_time']. ' seconds to send a request to '. $url. "\n";
        }

        echo $str;

        curl_close($ch);

        $this->saveLogs($str, $offer_id, $aff_id);

    }//sendCurlGetRequest

    /**
     * Save logs into the tenant's logs table.
     */
    protected function saveLogs($str, $offer_id, $aff_id){

        DB::connection('process_tenant_con')->table('logs')->insert([
            'offer_id'    => $offer_id,
            'aff_id'      => $aff_id,
            'module'      => 'CRON',
            'description' => $str,
            'created_at'  => now(),
        ]);

    }//saveLogs

    /**
     * Remove logs older than 2 days.
     */
    protected function removeExtraLogs(){

        DB::connection('process_tenant_con')->statement("DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 DAY)");

        Log::info("Old logs older than 2 days removed.");

    }//removeExtraLogs



}//End Of Command ProcessAllTenants
