<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Creates report_exports table for async export metadata tracking.
     * NO file_path, NO blob columns - reports are ephemeral.
     */
    public function up(): void
    {
        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Owner of the export');
            $table->string('report_type')->comment('Identifier: clicks, conversions, etc.');
            $table->enum('format', ['csv', 'xlsx']);
            $table->json('filters')->comment('Applied filters');
            $table->json('columns')->nullable()->comment('Selected columns');
            $table->enum('status', ['queued', 'processing', 'ready', 'expired', 'failed'])
                ->default('queued');
            $table->datetime('expires_at')->comment('TTL for the export');
            $table->text('error')->nullable()->comment('Failure reason');
            $table->timestamps();

            // Composite index for listing user exports
            $table->index(['user_id', 'status']);
            // Index for fast cleanup queries
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_exports');
    }
};
