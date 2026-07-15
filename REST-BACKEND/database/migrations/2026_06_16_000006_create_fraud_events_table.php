<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_events', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', [
                'click_flagged', 'click_blocked',
                'conversion_rejected', 'ip_blacklisted',
                'device_blacklisted', 'rule_triggered'
            ]);
            $table->string('entity_type')->nullable()->comment('click / conversion / ip / device');
            $table->string('entity_id')->nullable()->comment('tid or conversion id or ip/device_id');
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('rule_id')->references('id')->on('fraud_rules')->nullOnDelete();
            $table->index(['event_type', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_events');
    }
};