<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Invoice From
            $table->string('from_company')->nullable()->after('title');
            $table->text('from_address')->nullable()->after('from_company');
            $table->string('from_email')->nullable()->after('from_address');
            $table->string('from_vat_id')->nullable()->after('from_email');

            // Invoice To - type determines target
            $table->enum('company_type', ['advertiser', 'affiliate', 'custom'])->default('advertiser')->after('from_vat_id');
            $table->string('company_name')->nullable()->after('company_type');
            $table->text('to_address')->nullable()->after('company_name');
            $table->string('to_email')->nullable()->after('to_address');
            $table->string('to_vat_id')->nullable()->after('to_email');

            // Make advertiser_id nullable (invoices can now be for affiliates or custom)
            $table->unsignedBigInteger('advertiser_id')->nullable()->change();

            // Optional offer link
            $table->unsignedBigInteger('offer_id')->nullable()->after('to_vat_id');

            // Tax, discount, net total
            $table->decimal('tax_percent', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('tax_percent');
            $table->decimal('net_total', 12, 2)->default(0)->after('discount_amount');

            // Payment method
            $table->string('payment_method')->nullable()->after('net_total');

            // Notes and terms
            $table->text('notes')->nullable()->after('payment_method');
            $table->text('terms')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'from_company', 'from_address', 'from_email', 'from_vat_id',
                'company_type', 'company_name', 'to_address', 'to_email', 'to_vat_id',
                'offer_id', 'tax_percent', 'discount_amount', 'net_total',
                'payment_method', 'notes', 'terms',
            ]);
        });
    }
};
