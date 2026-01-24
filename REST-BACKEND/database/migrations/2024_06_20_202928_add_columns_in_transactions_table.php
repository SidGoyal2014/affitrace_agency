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
            $table->decimal('transaction_amount', 10, 2)->nullable()->after('stripe_subscription_id');
            $table->text('transaction_details')->nullable()->after('transaction_amount');
            $table->datetime('activity_date')->nullable()->after('activity');
            $table->integer('subscription_id')->nullable()->after('activity_date');


            $table->dropColumn('stripe_plan_id');
            $table->dropColumn('plan_name');
            $table->dropColumn('plan_type');
            $table->dropColumn('price');

            $table->dropColumn('subscription_amount');
            $table->dropColumn('subscription_start_date');
            $table->dropColumn('subscription_end_date');
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
