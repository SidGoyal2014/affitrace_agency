<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE offer_affiliates MODIFY COLUMN request_status ENUM('Accepted', 'Pending', 'Rejected', 'Unassigned') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE offer_affiliates MODIFY COLUMN request_status ENUM('Accepted', 'Pending', 'Rejected') NULL");
    }
};
