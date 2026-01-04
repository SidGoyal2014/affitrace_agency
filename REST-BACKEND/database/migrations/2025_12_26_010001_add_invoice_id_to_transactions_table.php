<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Check if column exists, if not add it
            if (!Schema::hasColumn('transactions', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->index()->after('id');
                // Note: User constraint "Transactions act as invoice line items" implies FK logic
                // usually managed by app, but we can add constraint if needed.
                // Keeping it loose for now as per previous instructions unless strict FK requested.
                // Re-reading JSON: "foreign_keys enabled: false". So no FK constraint.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'invoice_id')) {
                $table->dropColumn('invoice_id');
            }
        });
    }
};
