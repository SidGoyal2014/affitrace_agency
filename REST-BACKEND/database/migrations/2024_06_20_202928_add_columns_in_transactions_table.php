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
            if (!Schema::hasColumn('transactions', 'transaction_amount')) {
                $table->decimal('transaction_amount', 10, 2)->nullable()->after('stripe_subscription_id');
            }
            if (!Schema::hasColumn('transactions', 'transaction_details')) {
                $table->text('transaction_details')->nullable()->after('transaction_amount');
            }
            if (!Schema::hasColumn('transactions', 'activity_date')) {
                $table->datetime('activity_date')->nullable()->after('activity');
            }
            if (!Schema::hasColumn('transactions', 'subscription_id')) {
                $table->integer('subscription_id')->nullable()->after('activity_date');
            }

            if (Schema::hasColumn('transactions', 'stripe_plan_id')) {
                $table->dropColumn('stripe_plan_id');
            }
            if (Schema::hasColumn('transactions', 'plan_name')) {
                $table->dropColumn('plan_name');
            }
            if (Schema::hasColumn('transactions', 'plan_type')) {
                $table->dropColumn('plan_type');
            }
            if (Schema::hasColumn('transactions', 'price')) {
                $table->dropColumn('price');
            }

            if (Schema::hasColumn('transactions', 'subscription_amount')) {
                $table->dropColumn('subscription_amount');
            }
            if (Schema::hasColumn('transactions', 'subscription_start_date')) {
                $table->dropColumn('subscription_start_date');
            }
            if (Schema::hasColumn('transactions', 'subscription_end_date')) {
                $table->dropColumn('subscription_end_date');
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
