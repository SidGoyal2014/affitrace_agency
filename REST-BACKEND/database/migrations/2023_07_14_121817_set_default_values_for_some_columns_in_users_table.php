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
        Schema::table('users', function (Blueprint $table) {

			$table->string('job_title')->nullable()->change();
			$table->string('instant_messenger_name')->nullable()->change();
			$table->string('instant_messenger_value')->nullable()->change();

			DB::statement("ALTER TABLE `users` CHANGE `status` `status` ENUM('pending', 'active', 'rejected', 'suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
