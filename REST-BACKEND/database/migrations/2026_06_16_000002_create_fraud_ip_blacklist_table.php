<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_ip_blacklist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('reason')->nullable();
            $table->string('imported_from')->nullable()->comment('manual / csv / ipqs / anura');
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_ip_blacklist');
    }
};