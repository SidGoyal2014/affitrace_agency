<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('scope', ['global', 'per_advertiser', 'per_affiliate', 'per_offer'])->default('global');
            $table->unsignedBigInteger('scope_id')->nullable()->comment('Advertiser/Affiliate/Offer ID depending on scope');
            $table->enum('rule_type', ['ctit', 'duplicate_ip', 'vpn_proxy', 'bot', 'geo_mismatch', 'datacenter']);
            $table->enum('rule_action', ['block', 'flag', 'auto_reject']);
            $table->json('conditions')->nullable()->comment('JSON: thresholds and parameters for the rule');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['rule_type', 'is_active']);
            $table->index(['scope', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_rules');
    }
};