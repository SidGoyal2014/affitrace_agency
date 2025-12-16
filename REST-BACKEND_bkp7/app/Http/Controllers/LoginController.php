<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB, Auth, Http, Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;

use App\Http\Traits\SystemSettings;

class LoginController extends Controller {

	use SystemSettings;



    public function loginform(Request $request){

		//Session::flush(); // removes all session data
		if (Auth::check()){

			return redirect('dashboard');

		}

		return view('gen_forms/loginform');



	}//loginform


    public function doLogin(Request $request){


    	/*
	    This function is to login into the system
	    for for website internal purpose

	    Similar function in UserController is used for remote access for apis
	    */

	    $loginPageUrl = loginPageUrl($request->eai);

		try{

            $validator = Validator::make($request->all(), [
                'eai' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }


			$password = $request->input('password'); // password is form field
			$hashed = Hash::make($password);


			$user_type = $request->input('user_type');

			//DB::enableQueryLog();

			$user = User::where('email', $request->email)->where('user_type', $user_type)->first();

			


			if($user->username != $request->eai){
				return redirect(loginPageUrl())->with('error','Credentials are not correct, please try again');

			}



            if (!$user || !Hash::check($request->password, $user->password)) {
				return redirect(loginPageUrl())->with('error','Passwords not correct, please try again');

            }


            if (!$user) {
                return redirect(loginPageUrl())->with('error','The email or password is incorrect, please try again 222');

            }else{


				if (Auth::attempt(
						array(
							'email' => $request->email,
							'password' => $request->password,
							'user_type' => $user_type  // ← FIXED: Remove ucfirst()
						)
				)){

					$token = Auth::user()->createToken('authToken')->plainTextToken;

					$request->session()->put('user_type', $user_type);
					$request->session()->put('email', $request->email);
					$request->session()->put('username', $request->eai);
					$request->session()->put('agency_id', $user->agency_id);


					// Auth token below will be used while accessing Apis from the logged in admin/affiliate panel
					$request->session()->put('logged_in_user_token', $token); 


					\Log::debug('LoginController - Session after login:', session()->all());
					\Log::debug('LoginController - Auth user:', Auth::user() ? Auth::user()->toArray() : 'No user');

					return redirect($user_type.'/dashboard');
					//return redirect()->intended($user_type.'/dashboard');
					

				}else{

					return redirect(loginPageUrl())->with('error','The email or password is incorrect, please try again 333');

				}

			}

		}catch(\Exception $e){

			return redirect(loginPageUrl())->with('error', $e->getMessage().'The email or password is incorrect, please try again 4444');

		}

	}//doLogin


	public function logout(Request $req){

		$username = session('username');

		Auth::logout();

        $req->session()->invalidate(); // Invalidate the current session
        $req->session()->regenerateToken(); // Regenerate the CSRF token


		return redirect(loginPageUrl($username));


	}//logout







}//End of class LoginController
