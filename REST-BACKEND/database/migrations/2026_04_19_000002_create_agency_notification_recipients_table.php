<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_notification_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('notification_id')
                ->constrained('agency_notifications')
                ->cascadeOnDelete();

            $table->foreignId('recipient_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Composite indexes for common query patterns
            $table->index('recipient_id');
            $table->index(['notification_id', 'recipient_id']);
            $table->index(['recipient_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_notification_recipients');
    }
};
