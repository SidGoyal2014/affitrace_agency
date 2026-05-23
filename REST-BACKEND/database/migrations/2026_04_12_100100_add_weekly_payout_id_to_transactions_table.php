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
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'weekly_payout_id')) {
                    $table->unsignedBigInteger('weekly_payout_id')
                        ->nullable()
                        ->after('invoice_id');

                    $table->index('weekly_payout_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'weekly_payout_id')) {
                    $table->dropIndex(['weekly_payout_id']);
                    $table->dropColumn('weekly_payout_id');
                }
            });
        }
    }
};