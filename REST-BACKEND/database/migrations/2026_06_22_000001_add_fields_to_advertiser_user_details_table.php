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
        Schema::table('advertiser_user_details', function (Blueprint $table) {
            $table->string('tags')->nullable();
            $table->text('status_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertiser_user_details', function (Blueprint $table) {
            $table->dropColumn(['tags', 'status_reason']);
        });
    }
};
