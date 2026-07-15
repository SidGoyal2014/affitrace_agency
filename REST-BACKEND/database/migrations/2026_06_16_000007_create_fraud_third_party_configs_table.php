<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_third_party_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('provider', ['ipqs', 'anura', '24metrics'])->unique();
            $table->string('api_key')->nullable();
            $table->string('api_url')->nullable();
            $table->json('settings')->nullable()->comment('Provider-specific options: strictness, allow_proxies, etc.');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_third_party_configs');
    }
};