<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Insert fraud_detection module if not already present
        $exists = DB::table('modules')->where('slug', 'fraud_detection')->exists();
        if (!$exists) {
            DB::table('modules')->insert([
                'name'       => 'Fraud Detection',
                'slug'       => 'fraud_detection',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('modules')->where('slug', 'fraud_detection')->delete();
    }
};