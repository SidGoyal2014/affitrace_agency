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


			$table->enum('user_type', ['admin', 'advertiser', 'affiliate', 'employee'])->after('name');
			$table->string('company')->after('user_type');
			$table->string('address')->after('company');
			$table->string('city')->after('address');
			$table->string('state')->after('city');
			$table->string('state_code')->after('state');
			$table->string('country')->after('state_code');
			$table->string('zipcode')->after('country');
			$table->string('employee_id')->after('zipcode');
			$table->enum('status', ['active', 'pending', 'rejected', 'suspended'])->after('employee_id');
			$table->string('phone')->after('status');
			$table->string('gender')->after('phone');
			$table->string('dob')->after('gender');
			$table->string('social_name')->after('dob');
			$table->string('socal_profile_link')->after('social_name');
			$table->string('private_note')->after('socal_profile_link');
			$table->string('additional_info')->after('private_note');
			$table->string('tax_details')->after('additional_info');
			
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
