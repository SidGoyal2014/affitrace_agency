<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {

            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE offers ALTER COLUMN daily_start_time TYPE TIME(0) WITHOUT TIME ZONE USING daily_start_time::time without time zone');
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE offers ALTER COLUMN daily_stop_time TYPE TIME(0) WITHOUT TIME ZONE USING daily_stop_time::time without time zone');
            } else {
                $table->time('daily_start_time')->change();
                $table->time('daily_stop_time')->change();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            //
        });
    }
};
