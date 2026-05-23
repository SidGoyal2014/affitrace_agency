<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offer_events', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_events', 'revenue')) {
                $table->decimal('revenue', 25, 2)->nullable()->after('event_token');
            }

            if (!Schema::hasColumn('offer_events', 'payout')) {
                $table->decimal('payout', 25, 2)->nullable()->after('revenue');
            }
        });

        Schema::table('offer_events', function (Blueprint $table) {
            $table->unique(['offer_id', 'event_token'], 'uq_offer_events_offer_id_event_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_events', function (Blueprint $table) {
            $table->dropUnique('uq_offer_events_offer_id_event_token');

            if (Schema::hasColumn('offer_events', 'payout')) {
                $table->dropColumn('payout');
            }

            if (Schema::hasColumn('offer_events', 'revenue')) {
                $table->dropColumn('revenue');
            }
        });
    }
};
