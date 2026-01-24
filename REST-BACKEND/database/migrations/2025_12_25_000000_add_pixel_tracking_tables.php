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
        // Add conversion_tracking_method to offers table
        if (Schema::hasTable('offers') && !Schema::hasColumn('offers', 'conversion_tracking_method')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->enum('conversion_tracking_method', ['s2s', 'image_pixel', 'iframe_pixel', 'script_pixel'])
                    ->default('s2s')
                    ->after('offer_status'); // Placing it near status for visibility
            });
        }

        // Create affiliate_pixels table
        if (!Schema::hasTable('affiliate_pixels')) {
            Schema::create('affiliate_pixels', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('affiliate_id'); // Using integer to match users.id type if likely integer
                $table->integer('offer_id')->nullable();
                $table->integer('event_id')->nullable();
                $table->enum('type', ['image', 'iframe', 'script'])->default('iframe');
                $table->text('pixel_code');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                // Indexes for performance
                $table->index(['affiliate_id', 'offer_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('offers') && Schema::hasColumn('offers', 'conversion_tracking_method')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->dropColumn('conversion_tracking_method');
            });
        }

        Schema::dropIfExists('affiliate_pixels');
    }
};
