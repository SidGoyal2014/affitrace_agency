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
        Schema::create('payout_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('offer_id');
            $table->string('affiliate_id');
            $table->integer('event_id');
            $table->string('rule_name')->nullable();
            $table->string('revenue_model')->nullable();
            $table->string('revenue_pricing')->nullable();
            $table->string('currency')->nullable();
            $table->string('payout_model')->nullable();
            $table->string('payout_pricing')->nullable();
            $table->text('target_country')->nullable();
            $table->string('target_device')->nullable();
			$table->string('browser')->nullable();
			$table->string('os')->nullable();
            $table->string('aff_sub1')->nullable();
            $table->string('aff_sub2')->nullable();
            $table->string('aff_sub3')->nullable();
            $table->string('aff_sub4')->nullable();
            $table->string('aff_sub5')->nullable();
            $table->string('adv_sub1')->nullable();
            $table->string('adv_sub2')->nullable();
            $table->string('adv_sub3')->nullable();
            $table->string('adv_sub4')->nullable();
            $table->string('adv_sub5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_rules');
    }
};
