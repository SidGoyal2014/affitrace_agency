<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impersonation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impersonator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('impersonated_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('ip_address', 45);
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_logs');
    }
};
