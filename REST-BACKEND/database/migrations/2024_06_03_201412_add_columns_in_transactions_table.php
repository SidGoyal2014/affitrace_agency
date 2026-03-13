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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'invoice_id')) {
                $table->string('invoice_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('transactions', 'billing')) {
                $table->string('billing')->nullable()->after('invoice_id');
            }
            if (!Schema::hasColumn('transactions', 'billing_reason')) {
                $table->string('billing_reason')->nullable()->after('billing');
            }
            if (!Schema::hasColumn('transactions', 'billing_charge')) {
                $table->string('billing_charge')->nullable()->after('billing_reason');
            }
            if (!Schema::hasColumn('transactions', 'stripe_plan_id')) {
                $table->string('stripe_plan_id')->nullable()->after('billing_charge');
            }
            if (!Schema::hasColumn('transactions', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable()->after('pg_customer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
