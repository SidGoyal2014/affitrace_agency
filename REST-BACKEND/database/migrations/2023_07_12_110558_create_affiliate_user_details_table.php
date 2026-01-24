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
        Schema::create('affiliate_user_details', function (Blueprint $table) {
            $table->id();
			$table->string('additional_tracking_url_tokens')->nullable();
			$table->string('time_zone')->nullable();
			$table->string('payment_method')->nullable();
			$table->text('payment_details')->nullable();
			$table->string('allow_profile_update')->nullable();
			$table->string('advertisers_block')->nullable();
			$table->string('advertisers_allow')->nullable();
			$table->string('affiliate_fallback')->nullable();
			$table->text('private_note')->nullable();
			$table->text('billing_terms')->nullable();
			$table->string('add_tax_basic_name')->nullable();
			$table->string('add_tax_type')->nullable();
			$table->string('add_tax_number')->nullable();
			$table->string('add_tax')->nullable();
			$table->integer('user_id')->nullable();
			$table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_user_details');
    }
};
