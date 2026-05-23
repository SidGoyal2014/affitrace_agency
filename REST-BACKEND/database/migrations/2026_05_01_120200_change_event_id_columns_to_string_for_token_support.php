<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE offer_trackings MODIFY event_id VARCHAR(191) NULL");
            DB::statement("ALTER TABLE conversions MODIFY event_id VARCHAR(191) NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE offer_trackings ALTER COLUMN event_id TYPE VARCHAR(191) USING event_id::text");
            DB::statement("ALTER TABLE offer_trackings ALTER COLUMN event_id DROP NOT NULL");
            DB::statement("ALTER TABLE conversions ALTER COLUMN event_id TYPE VARCHAR(191) USING event_id::text");
            DB::statement("ALTER TABLE conversions ALTER COLUMN event_id DROP NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE offer_trackings MODIFY event_id INT NULL");
            DB::statement("ALTER TABLE conversions MODIFY event_id INT NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE offer_trackings ALTER COLUMN event_id TYPE INTEGER USING NULLIF(event_id, '')::integer");
            DB::statement("ALTER TABLE conversions ALTER COLUMN event_id TYPE INTEGER USING NULLIF(event_id, '')::integer");
        }
    }
};
