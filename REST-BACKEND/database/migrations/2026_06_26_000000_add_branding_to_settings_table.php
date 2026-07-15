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
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'platform_name')) {
                    $table->string('platform_name', 100)->default('AffiTrace Agency');
                }
                if (!Schema::hasColumn('settings', 'platform_logo')) {
                    $table->string('platform_logo', 255)->nullable();
                }
                if (!Schema::hasColumn('settings', 'platform_favicon')) {
                    $table->string('platform_favicon', 255)->nullable();
                }
                if (!Schema::hasColumn('settings', 'primary_color')) {
                    $table->string('primary_color', 7)->default('#2563EB');
                }
                if (!Schema::hasColumn('settings', 'secondary_color')) {
                    $table->string('secondary_color', 7)->default('#1E293B');
                }
                if (!Schema::hasColumn('settings', 'accent_color')) {
                    $table->string('accent_color', 7)->default('#10B981');
                }
                if (!Schema::hasColumn('settings', 'support_email')) {
                    $table->string('support_email', 100)->nullable();
                }
                if (!Schema::hasColumn('settings', 'custom_copyright')) {
                    $table->string('custom_copyright', 150)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                $columns = [
                    'platform_name',
                    'platform_logo',
                    'platform_favicon',
                    'primary_color',
                    'secondary_color',
                    'accent_color',
                    'support_email',
                    'custom_copyright'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
