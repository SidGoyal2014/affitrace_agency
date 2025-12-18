<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;


class EmployeeController extends Controller
{

	protected $employeeService;

	public function __construct(\App\Services\Employee\EmployeeService $employeeService)
	{
		$this->employeeService = $employeeService;
	}

	public function profile(Request $request)
	{
		$data = $this->employeeService->getProfile($request->user());
		return response()->json($data);

	}//profile


	function update_profile(Request $request)
	{

		try {

			$validator = Validator::make(request()->all(), [
				'name' => 'required|string|min:5',
				'email' => 'unique:users,email,' . Auth::id(),
				'password' => 'nullable|string|min:5'
			]);

			if ($validator->fails()) {
				return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);

			}

			$result = $this->employeeService->updateProfile($request);
			return response()->json($result);


		} catch (\Exception $e) {

			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
		}


	}//update_profile






}//End of class AdvertiserController
