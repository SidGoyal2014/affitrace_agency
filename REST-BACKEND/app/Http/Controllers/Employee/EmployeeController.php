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


	public function profile(Request $request)
	{

		$user = $request->user();
		$logged_in_user_type = $user->user_type;

		return response()->json([
			'module_name' => 'Profile',
			'user' => $user,
			'title' => ucfirst($logged_in_user_type) . ' Profile',
			'logged_in_user_type' => $logged_in_user_type
		]);

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

			$user = Auth::user();

			$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;
			$user->gender = $request->gender;

			if (strlen($request->password) > 0) {
				$user->password = Hash::make($request->password);

			}

			$user->save();

			return response()->json(['status' => 1, 'msg' => 'Profile Updated']);


		} catch (\Exception $e) {

			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
		}


	}//update_profile






}//End of class AdvertiserController
