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
        Schema::table('offer_trackings', function (Blueprint $table) {
            // Transitional FK-ready column. Existing string column `event` stays unchanged in this phase.
            if (!Schema::hasColumn('offer_trackings', 'event_ref_id')) {
                $table->unsignedBigInteger('event_ref_id')->nullable()->after('event');
            }
        });

        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->foreign('event_ref_id', 'fk_offer_trackings_event_ref_id_offer_events_id')
                ->references('id')
                ->on('offer_events')
                ->nullOnDelete();

            $table->unique(['tid', 'event_id'], 'uq_offer_trackings_tid_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_trackings', function (Blueprint $table) {
            $table->dropUnique('uq_offer_trackings_tid_event_id');
            $table->dropForeign('fk_offer_trackings_event_ref_id_offer_events_id');

            if (Schema::hasColumn('offer_trackings', 'event_ref_id')) {
                $table->dropColumn('event_ref_id');
            }
        });
    }
};
