<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_device_blacklist', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->string('device_type')->nullable()->comment('android_id / ios_idfa / google_aid');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_device_blacklist');
    }
};