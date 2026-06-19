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
        Schema::table('affiliate_user_details', function (Blueprint $table) {
            $table->string('website')->nullable();
            $table->string('traffic_source')->nullable();
            $table->text('docs')->nullable();
            $table->text('status_reason')->nullable();
            $table->integer('global_clicks_cap')->nullable();
            $table->integer('global_conversions_cap')->nullable();
            $table->string('tags')->nullable();
            $table->integer('referred_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_user_details', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'traffic_source',
                'docs',
                'status_reason',
                'global_clicks_cap',
                'global_conversions_cap',
                'tags',
                'referred_by'
            ]);
        });
    }
};
