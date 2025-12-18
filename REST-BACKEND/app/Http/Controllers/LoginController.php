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

class LoginController extends Controller
{

	use SystemSettings;

	protected $loginService;

	public function __construct(\App\Services\LoginService $loginService)
	{
		$this->loginService = $loginService;
	}

	public function loginform(Request $request)
	{
		if (Auth::check()) {
			return redirect('dashboard');
		}

		return view('gen_forms/loginform');

	}//loginform


	public function doLogin(Request $request)
	{

		/*
		This function is to login into the system
		for for website internal purpose

		Similar function in UserController is used for remote access for apis
		*/

		$loginPageUrl = loginPageUrl($request->eai);

		try {

			$validator = Validator::make($request->all(), [
				'eai' => 'required',
				'email' => 'required|email',
				'password' => 'required|string|min:1'
			]);

			if ($validator->fails()) {
				return response()->json($validator->messages(), 400);
			}

			$user_type = $request->input('user_type');

			$result = $this->loginService->authenticateUser($request, $user_type);

			if ($result['status']) {
				$user_type = $result['user_type'];
				return redirect($user_type . '/dashboard');
			} else {
				return redirect(loginPageUrl())->with('error', $result['message']);
			}

		} catch (\Exception $e) {

			return redirect(loginPageUrl())->with('error', $e->getMessage() . 'The email or password is incorrect, please try again 4444');

		}

	}//doLogin


	public function logout(Request $req)
	{

		$username = $this->loginService->logout($req);
		return redirect(loginPageUrl($username));


	}//logout







}//End of class LoginController
