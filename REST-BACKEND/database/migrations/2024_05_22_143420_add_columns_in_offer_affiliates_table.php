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
        if (Schema::hasTable('offer_affiliates')) {
            Schema::table('offer_affiliates', function (Blueprint $table) {
                if (!Schema::hasColumn('offer_affiliates', 'request_made')) {
                    $table->enum('request_made', ['Y', 'N'])->nullable()->after('tracking_url');
                }
                if (!Schema::hasColumn('offer_affiliates', 'request_status')) {
                    $table->enum('request_status', ['Accepted', 'Pending', 'Rejected'])->nullable()->after('request_made');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_affiliates', function (Blueprint $table) {
            //
        });
    }
};
