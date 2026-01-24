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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->datetime('trial_end_date')->nullable()->after('subscription_end_date');

            $table->text('subscription_details')->after('trial_end_date')->nullable();
            $table->enum('is_active',['N','Y'])->nullable()->after('subscription_details');
            $table->timestamp('cancelled_at')->after('status')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
};
