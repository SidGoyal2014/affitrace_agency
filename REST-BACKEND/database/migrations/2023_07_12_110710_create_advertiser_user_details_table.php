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
        Schema::create('advertiser_user_details', function (Blueprint $table) {
            $table->id();
			$table->string('timezone')->nullable();
			$table->text('postback_ip_whitelist')->nullable();
			$table->string('affiliate_block')->nullable();
			$table->string('affiliate_whitelist')->nullable();
			$table->string('private_note')->nullable();
			$table->string('additional_info')->nullable();
			$table->string('tax_details')->nullable();
			$table->string('currency')->nullable();
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
        Schema::dropIfExists('advertiser_user_details');
    }
};
