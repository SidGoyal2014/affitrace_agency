<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirect_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('input_url');
            $table->string('proxy', 500)->nullable();
            $table->string('device', 100)->nullable()->default('desktop_windows');
            $table->text('final_url')->nullable();
            $table->json('hops')->nullable();
            $table->unsignedSmallInteger('hop_count')->default(0);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirect_checks');
    }
};