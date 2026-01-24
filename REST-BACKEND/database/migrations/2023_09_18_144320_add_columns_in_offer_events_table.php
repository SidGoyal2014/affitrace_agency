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
        Schema::table('offer_events', function (Blueprint $table) {
            $table->string('multi_conv')->after('event_token');
			$table->string('multi_conv_ip')->after('multi_conv');
			$table->string('approval')->after('multi_conv_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_events', function (Blueprint $table) {
            //
        });
    }
};
