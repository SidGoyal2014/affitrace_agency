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
			$table->string('company')->after('user_type')->nullable()->change();
			$table->string('address')->after('company')->nullable()->change();
			$table->string('city')->after('address')->nullable()->change();
			$table->string('state')->after('city')->nullable()->change();
			$table->string('state_code')->after('state')->nullable()->change();
			$table->string('country')->after('state_code')->nullable()->change();
			$table->string('zipcode')->after('country')->nullable()->change();
			$table->string('employee_id')->after('zipcode')->nullable()->change();
			$table->string('phone')->after('status')->nullable()->change();
			$table->string('gender')->after('phone')->nullable()->change();
			$table->string('dob')->after('gender')->nullable()->change();
			$table->string('social_name')->after('dob')->nullable()->change();
			$table->string('socal_profile_link')->after('social_name')->nullable()->change();
			$table->string('private_note')->after('socal_profile_link')->nullable()->change();
			$table->string('additional_info')->after('private_note')->nullable()->change();
			$table->string('tax_details')->after('additional_info')->nullable()->change();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('users', function (Blueprint $table) {
			$table->string('company')->after('user_type')->nullable(false)->change();
			$table->string('address')->after('company')->nullable(false)->change();
			$table->string('city')->after('address')->nullable(false)->change();
			$table->string('state')->after('city')->nullable(false)->change();
			$table->string('state_code')->after('state')->nullable(false)->change();
			$table->string('country')->after('state_code')->nullable(false)->change();
			$table->string('zipcode')->after('country')->nullable(false)->change();
			$table->string('employee_id')->after('zipcode')->nullable(false)->change();
			$table->string('phone')->after('status')->nullable(false)->change();
			$table->string('gender')->after('phone')->nullable(false)->change();
			$table->string('dob')->after('gender')->nullable(false)->change();
			$table->string('social_name')->after('dob')->nullable(false)->change();
			$table->string('socal_profile_link')->after('social_name')->nullable(false)->change();
			$table->string('private_note')->after('socal_profile_link')->nullable(false)->change();
			$table->string('additional_info')->after('private_note')->nullable(false)->change();
			$table->string('tax_details')->after('additional_info')->nullable(false)->change();
		});
    }
};
