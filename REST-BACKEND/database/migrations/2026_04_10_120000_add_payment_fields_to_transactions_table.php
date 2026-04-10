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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'payment_type')) {
                $table->enum('payment_type', ['Receipt', 'Payout'])
                    ->nullable()
                    ->default(null)
                    ->after('id');
            }

            if (!Schema::hasColumn('transactions', 'payment_mode')) {
                $table->enum('payment_mode', ['Manual', 'Online'])
                    ->default('Online')
                    ->after('payment_type');
            }

            if (!Schema::hasColumn('transactions', 'logged_in_user')) {
                $table->integer('logged_in_user')
                    ->default(null)
                    ->nullable()                    
                    ->after('payment_mode');
            }

            if (!Schema::hasColumn('transactions', 'payment_from')) {
                $table->enum('payment_from', ['Advertiser', 'Agency'])
                    ->default(null)
                    ->nullable()                    
                    ->after('logged_in_user');
            }

            if (!Schema::hasColumn('transactions', 'payment_to')) {
                $table->enum('payment_to', ['Agency', 'Affiliate'])
                    ->default(null)
                    ->nullable()                    
                    ->after('payment_from');
            }

            if (!Schema::hasColumn('transactions', 'advertiser_id')) {
                $table->integer('advertiser_id')
                    ->nullable()
                    ->default(null)
                    ->after('payment_to');
            }

            if (!Schema::hasColumn('transactions', 'affiliate_id')) {
                $table->integer('affiliate_id')
                    ->default(null)
                    ->nullable()                    
                    ->after('advertiser_id');
            }

            if (!Schema::hasColumn('transactions', 'transaction_code')) {
                $table->integer('transaction_code')
                    ->default(null)
                    ->nullable()                    
                    ->after('affiliate_id');
            }

            if (!Schema::hasColumn('transactions', 'manual_invoice_no')) {
                $table->integer('manual_invoice_no')
                    ->default(null)
                    ->nullable()                    
                    ->after('transaction_code');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            if (Schema::hasColumn('transactions', 'manual_invoice_no')) {
                $table->dropColumn('manual_invoice_no');
            }

            if (Schema::hasColumn('transactions', 'transaction_code')) {
                $table->dropColumn('transaction_code');
            }

            if (Schema::hasColumn('transactions', 'affiliate_id')) {
                $table->dropColumn('affiliate_id');
            }

            if (Schema::hasColumn('transactions', 'advertiser_id')) {
                $table->dropColumn('advertiser_id');
            }

            if (Schema::hasColumn('transactions', 'payment_to')) {
                $table->dropColumn('payment_to');
            }


            if (Schema::hasColumn('transactions', 'payment_from')) {
                $table->dropColumn('payment_from');
            }


            if (Schema::hasColumn('transactions', 'logged_in_user')) {
                $table->dropColumn('logged_in_user');
            }
            

            if (Schema::hasColumn('transactions', 'payment_mode')) {
                $table->dropColumn('payment_mode');
            }

            if (Schema::hasColumn('transactions', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });
    }
};
