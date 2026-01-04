<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        if (!Schema::hasTable('mmplatforms')) {
            Schema::create('mmplatforms', function (Blueprint $table) {

                // Engine & charset
                // Engine & charset
                // $table->engine = 'InnoDB';
                // $table->charset = 'utf8mb4';
                // $table->collation = 'utf8mb4_unicode_ci';

                // Columns
                $table->bigIncrements('id'); // bigint unsigned
                $table->string('title', 255); // NOT NULL by default
                $table->longText('description')->nullable();
                $table->enum('is_active', ['Y', 'N']);
                $table->timestamp('created_at')->nullable()->default(null);
                $table->timestamp('updated_at')->nullable()->default(null);
            });

            // Seed data
            DB::table('mmplatforms')->insert([
                ['title' => 'AppsFlyer', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Adjust', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Branch', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Singular', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Kochava', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mmplatforms');
    }
};
