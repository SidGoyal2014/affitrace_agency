<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;


class EmployeeController extends Controller {


	public function profile(Request $request){

		if(Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			return view('employee.profile')
				->with('user', Auth::user())
				->with('title', ucfirst($logged_in_user_type).' Profile')
				->with('module_name', 'Profile')
				->with('logged_in_user_type', $logged_in_user_type);

		}

		return redirect('login');

	}//profile


	function update_profile(Request $request){

        try {

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
				'email' => 'unique:users,email,'.Auth::id(),
                'password' => 'nullable|string|min:5'
            ]);

            if ($validator->fails()) {
				return redirect('employee/profile')->withErrors($validator->messages())->withInput();

            }

            $user = Auth::user();

            $user->name = $request->name;
            $user->email = $request->email;
			$user->phone = $request->phone;
			$user->gender = $request->gender;

			if(strlen($request->password) > 0){
				$user->password = Hash::make($request->password);

			}

            $user->save();

			return redirect('employee/profile')->with('success', 'Profile Updated');


        }catch( \Exception $e ){

			return redirect('employee/profile')->withErrors($e->getMessage());
        }


	}//update_profile






}//End of class AdvertiserController
