<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        if (Schema::hasTable('offer_affiliates')) {
            Schema::table('offer_affiliates', function (Blueprint $table) {
                $table->enum('request_status', ['Accepted', 'Pending', 'Rejected', 'Unassigned'])->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('offer_affiliates', function (Blueprint $table) {
            $table->enum('request_status', ['Accepted', 'Pending', 'Rejected'])->nullable(false)->change();
        });
    }
};