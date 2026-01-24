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
        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->enum('is_transferred', ['yes', 'no'])->default('no')->after('deep_link');
            $table->timestamp('transferred_at')->nullable()->after('is_transferred');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_trackings', function (Blueprint $table) {
            //
        });
    }
};
