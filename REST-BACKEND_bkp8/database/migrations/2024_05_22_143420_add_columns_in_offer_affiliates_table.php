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
        Schema::table('offer_affiliates', function (Blueprint $table) {

            $table->enum('request_made', ['Y', 'N'])->nullable()->after('tracking_url');
            $table->enum('request_status', ['Accepted', 'Pending', 'Rejected'])->nullable()->after('request_made');

        });
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
