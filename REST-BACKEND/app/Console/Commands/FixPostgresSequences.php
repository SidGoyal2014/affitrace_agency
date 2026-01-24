<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class FixPostgresSequences extends Command
{
    protected $signature = 'db:fix-sequences {--db-name= : The database name to connect to} {--connection= : The connection name to use}';
    protected $description = 'Fix PostgreSQL sequences for a specific database';

    public function handle()
    {
        $dbName = $this->option('db-name');
        $connectionName = $this->option('connection');

        if ($dbName) {
            // Create a temporary connection based on 'pgsql' template
            $config = config('database.connections.pgsql');
            $config['database'] = $dbName;
            Config::set('database.connections.temp_pgsql_fix', $config);
            $connectionName = 'temp_pgsql_fix';
        } elseif (!$connectionName) {
            $connectionName = config('database.default');
        }

        $this->info("Fixing sequences for connection: $connectionName" . ($dbName ? " (DB: $dbName)" : ""));

        try {
            // Get all public tables
            $tables = DB::connection($connectionName)->select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");

            foreach ($tables as $table) {
                $tableName = $table->tablename;

                // We assume 'id' is the primary key. If not, this might skip, which is fine for now.
                // Checking sequence existence is better.

                // Construct sequence name based on standard naming: table_id_seq
                // Or utilize pg_get_serial_sequence

                try {
                    $seqResult = DB::connection($connectionName)->select("SELECT pg_get_serial_sequence(?, 'id') as seq", [$tableName]);

                    if (!empty($seqResult[0]->seq)) {
                        $seqName = $seqResult[0]->seq;
                        $this->line("Fixing sequence '$seqName' for table '$tableName'...");

                        // Set the sequence to the MAX(id) + 1
                        // If MAX(id) is null (empty table), it defaults to 1 (via coalesce(..., 0) + 1)
                        DB::connection($connectionName)->statement("
                            SELECT setval(
                                '$seqName', 
                                COALESCE((SELECT MAX(id) FROM \"$tableName\"), 0) + 1, 
                                false
                            )
                        ");
                    }
                } catch (\Exception $ex) {
                    // Ignore tables without 'id' column or issues
                    // $this->warn("Skipped $tableName: " . $ex->getMessage());
                }
            }

            $this->info("Sequences fixed successfully.");

        } catch (\Exception $e) {
            $this->error("Critical Error: " . $e->getMessage());
        }
    }
}
