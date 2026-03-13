<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('user_type')->constrained('roles')->onDelete('set null');
            $table->foreignId('designation_id')->nullable()->after('role_id')->constrained('designations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['designation_id']);
            $table->dropColumn(['role_id', 'designation_id']);
        });
    }
};
