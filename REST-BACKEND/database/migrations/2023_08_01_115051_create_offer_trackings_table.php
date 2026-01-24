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
        Schema::create('offer_trackings', function (Blueprint $table) {
            $table->id();
			$table->integer('offer_id');
			$table->integer('aff_id');
			$table->string('aff_click_id');
			$table->string('tid')->nullable();
			$table->string('aff_sub1')->nullable();
			$table->string('sub_aff_id')->nullable();
			$table->string('aff_sub2')->nullable();
			$table->string('aff_sub3')->nullable();
			$table->string('aff_sub5')->nullable();
			$table->string('aff_sub4')->nullable();
			$table->string('source')->nullable();
			$table->string('android_id')->nullable();
			$table->string('google_aid')->nullable();
			$table->integer('device_id')->nullable();
			$table->string('ip')->nullable();
			$table->string('iosidfa')->nullable();
			$table->string('offer_name');
			$table->string('country');
			$table->string('os')->nullable();
			$table->string('raw_useragent')->nullable();
			$table->string('os_ver')->nullable();
			$table->integer('adv_id');
			$table->string('useragent')->nullable();
			$table->string('timestamp')->nullable();
			$table->string('deep_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_trackings');
    }
};
