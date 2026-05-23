<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('affiliate_weekly_payouts')) {
            Schema::create('affiliate_weekly_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id')->index();
                $table->date('week_start')->index();
                $table->date('week_end');
                $table->string('month_key', 7)->index();
                $table->unsignedSmallInteger('year')->index();
                $table->unsignedSmallInteger('week_number');
                $table->enum('entry_type', ['Generated', 'Manual'])->default('Generated')->index();
                $table->string('generated_key')->nullable()->unique();
                $table->string('work_basis', 30)->default('Mixed');
                $table->decimal('work_units', 20, 4)->nullable();
                $table->decimal('total_amount', 20, 2)->default(0);
                $table->string('currency', 10)->default('USD');
                $table->string('status', 20)->default('PENDING')->index();
                $table->string('invoice_code', 100)->nullable();
                $table->text('notes')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->json('breakdown')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->unsignedBigInteger('paid_by')->nullable();
                $table->unsignedBigInteger('rejected_by')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['affiliate_id', 'status']);
                $table->index(['affiliate_id', 'week_start', 'entry_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_weekly_payouts');
    }
};