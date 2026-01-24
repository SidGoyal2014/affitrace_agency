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
        Schema::create('subscription_status_details', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('status',['Created','Paused', 'Resumed', 'Cancelled', 'Deleted'])->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_status_details');
    }
};
