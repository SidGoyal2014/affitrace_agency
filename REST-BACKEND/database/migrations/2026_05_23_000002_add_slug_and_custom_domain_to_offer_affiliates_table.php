<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_affiliates', function (Blueprint $table) {
            $table->string('slug', 16)->unique()->nullable()->after('tracking_url');
            $table->unsignedBigInteger('custom_domain_id')->nullable()->after('slug');
            $table->foreign('custom_domain_id')
                  ->references('id')->on('custom_domains')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('offer_affiliates', function (Blueprint $table) {
            $table->dropForeign(['custom_domain_id']);
            $table->dropColumn(['slug', 'custom_domain_id']);
        });
    }
};