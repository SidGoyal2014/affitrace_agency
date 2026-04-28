<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_notifications', function (Blueprint $table) {
            $table->id();

            // TODO: agency_id is stored as a string identifier (the agency DB name/username).
            // This project uses database-per-tenant multi-tenancy with no shared tenants table,
            // so a FK constraint to a tenants/agencies table is not applicable here.
            // If a centralised agencies table is introduced in future, add the FK constraint then.
            $table->string('agency_id')->index();

            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('body');

            $table->enum('type', ['notification', 'news', 'alert', 'broadcast'])
                ->default('notification');

            $table->enum('category', ['offer', 'payment', 'transaction', 'subscription', 'general'])
                ->default('general');

            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                ->default('normal');

            $table->enum('target_role', ['affiliate', 'advertiser', 'employee', 'all'])
                ->default('all');

            $table->enum('channel', ['in-app', 'email'])
                ->default('in-app');

            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('is_dispatched')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_notifications');
    }
};
