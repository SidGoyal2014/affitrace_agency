<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_postback_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversion_id')->index();
            $table->unsignedBigInteger('affiliate_id')->index();
            $table->text('url_fired');
            $table->string('http_status', 10)->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
            
            // Optional DB Foreign Keys if required, but performance-wise indexes are often enough for logs
            // $table->foreign('conversion_id')->references('id')->on('conversions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_postback_logs');
    }
};
