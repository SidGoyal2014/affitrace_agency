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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('current_plan_id');
            $table->string('current_plan_name');
            $table->decimal('current_plan_price', 10, 2);
            $table->integer('num_offers')->nullable();
            $table->integer('num_affiliates')->nullable();
            $table->integer('num_clicks')->nullable();
            $table->integer('num_conversions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
