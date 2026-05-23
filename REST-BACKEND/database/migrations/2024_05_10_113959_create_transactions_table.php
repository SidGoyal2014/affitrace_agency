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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string("plan_name")->nullable();
                $table->string("plan_type")->nullable();
                $table->decimal("price", 10, 2)->nullable();
                $table->string("pg_customer_id")->nullable();
                $table->decimal("subscription_amount", 10, 2)->nullable();
                $table->datetime("subscription_start_date")->nullable();
                $table->datetime("subscription_end_date")->nullable();
                $table->datetime("transaction_date")->nullable();
                $table->enum('activity',['Created','Paused', 'Resumed', 'Cancelled', 'Deleted'])->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
