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
        Schema::table('affiliate_user_details', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliate_user_details', 'payout_threshold')) {
                $table->decimal('payout_threshold', 10, 2)->default(50.00);
            }
            if (!Schema::hasColumn('affiliate_user_details', 'telegram_bot_token')) {
                $table->string('telegram_bot_token')->nullable();
            }
            if (!Schema::hasColumn('affiliate_user_details', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable();
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'recaptcha_site_key')) {
                $table->string('recaptcha_site_key')->nullable();
            }
            if (!Schema::hasColumn('settings', 'recaptcha_secret_key')) {
                $table->string('recaptcha_secret_key')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_user_details', function (Blueprint $table) {
            $table->dropColumn(['payout_threshold', 'telegram_bot_token', 'telegram_chat_id']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['recaptcha_site_key', 'recaptcha_secret_key']);
        });
    }
};
