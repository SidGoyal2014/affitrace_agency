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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_plan_id')->nullable();
            $table->string("plan_name")->nullable();
            $table->string("plan_type")->nullable();
            $table->decimal("price", 10, 2)->nullable();
            $table->string("pg_customer_id")->nullable();          
            $table->string('stripe_subscription_id')->nullable();
            $table->decimal("subscription_amount", 10, 2)->nullable();
            $table->datetime("subscription_start_date")->nullable();
            $table->datetime("subscription_end_date")->nullable();
            $table->enum("status", ['Created', 'Paused', 'Resumed', 'Cancelled', 'Deleted'])->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
