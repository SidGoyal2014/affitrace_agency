<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        if (Schema::hasTable('offer_affiliates')) {
            $driver = DB::getDriverName();

            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE offer_affiliates DROP CONSTRAINT IF EXISTS offer_affiliates_request_status_check");
                DB::statement("ALTER TABLE offer_affiliates ADD CONSTRAINT offer_affiliates_request_status_check CHECK (request_status IN ('Accepted', 'Pending', 'Rejected', 'Unassigned'))");
                DB::statement("ALTER TABLE offer_affiliates ALTER COLUMN request_status DROP NOT NULL");
            } else {
                Schema::table('offer_affiliates', function (Blueprint $table) {
                    $table->enum('request_status', ['Accepted', 'Pending', 'Rejected', 'Unassigned'])->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offer_affiliates')) {
            $driver = DB::getDriverName();

            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE offer_affiliates DROP CONSTRAINT IF EXISTS offer_affiliates_request_status_check");
                DB::statement("ALTER TABLE offer_affiliates ADD CONSTRAINT offer_affiliates_request_status_check CHECK (request_status IN ('Accepted', 'Pending', 'Rejected'))");
                DB::statement("ALTER TABLE offer_affiliates ALTER COLUMN request_status SET NOT NULL");
            } else {
                Schema::table('offer_affiliates', function (Blueprint $table) {
                    $table->enum('request_status', ['Accepted', 'Pending', 'Rejected'])->nullable(false)->change();
                });
            }
        }
    }
};