<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_click_scores', function (Blueprint $table) {
            $table->id();
            $table->string('tid')->index()->comment('Tracking ID from offer_trackings');
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->tinyInteger('score')->default(100)->comment('Quality score 0-100');
            $table->json('flags')->nullable()->comment('Array of triggered rule types');
            $table->enum('status', ['clean', 'flagged', 'blocked'])->default('clean');
            $table->json('third_party_data')->nullable()->comment('Raw response from IPQS/Anura/24metrics');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['affiliate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_click_scores');
    }
};