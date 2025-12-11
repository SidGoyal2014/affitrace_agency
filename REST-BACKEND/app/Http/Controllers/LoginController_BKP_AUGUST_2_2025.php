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

    public function registerform(Request $request){

		if(Auth::check()){
			return redirect('dashboard');
		}

		return view('gen_forms/registerform')->with('title', 'Signup');

	}//registerform

	public function doRegister(Request $request){

        try {

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
				'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|string|min:5',
                'company' => 'required|string|min:5',
				'phone' => 'required|digits_between:10,15',
            ]);

            if ($validator->fails()) {
            	if($request->user_type == 'advertiser'){
					return redirect('admin/list_advertisers')->withErrors($validator->messages())->withInput();
					
				}

				return redirect('register')->withErrors($validator->messages())->withInput();

            }//if

            if($request->user_type == 'affiliate'){

				if($this->get_total_affiliates() >= $this->get_settings("num_affiliates")){
					return redirect('register')->with('error', "We are not accepting new affiliates as of now.");

				}

            }//if


// echo "DB Connection: ".DB::getDefaultConnection();
// echo "<br>";
// echo "DB Name: ".DB::connection()->getDatabaseName();
// echo "<br>";
// print_r(session()->all());
// die;
// print_r(request()->all());



			$code = bin2hex(random_bytes(5));
            $user = new User();
            $user->code = $code;
			$user->name = $request->name;
			$user->username = str_replace(' ','', strtolower($request->name));
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->user_type = $request->user_type;

			$user->company = $request->company;
			$user->country = $request->country;
			$user->phone = $request->phone;

            $user->save();

// print_r($user);
// die;


			if($request->user_type == 'affiliate'){

				$aud = new AffiliateUserDetail();
				$aud->user_id = $user->id;
				$aud->save();

	            Auth::attempt(['email' => $user->email, 'password' => $user->password, 'user_type' => $user->user_type]);
				return redirect('dashboard');


			}elseif($request->user_type == 'advertiser'){
			
				$adud = new AdvertiserUserDetail();
				$adud->user_id = $user->id;
				$adud->save();

	            return redirect('admin/list_advertisers');

			}


        }catch( \Exception $e ){

        	if($request->user_type == 'affiliate'){
				return redirect('register')->withErrors($e->getMessage());
				
			}elseif($request->user_type == 'advertiser'){
				return redirect('admin/create_advertiser')->withErrors($e->getMessage());

			}
        }

    }//doRegister


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
