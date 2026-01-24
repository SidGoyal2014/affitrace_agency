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
            $table->string('invoice_id')->nullable()->after('id');
            $table->string('billing')->nullable()->after('invoice_id');
            $table->string('billing_reason')->nullable()->after('billing');
            $table->string('billing_charge')->nullable()->after('billing_reason');
            $table->string('stripe_plan_id')->nullable()->after('billing_charge');

            $table->string('stripe_subscription_id')->nullable()->after('pg_customer_id');

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
