<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use DB, Auth;

class UserController extends Controller
{

    function userlogin(Request $request){

    	/*
	    This function is to login into the system
	    for using apis from remote ... this is for remote access

	    Similar function in LoginController is used for website login purpose
	    */


		if (Auth::attempt(
				array(
					'email' => $request->email,
					'password' => $request->password,
					'user_type' => 'admin'
				)
		)){
	        $token = Auth::user()->createToken('authToken')->plainTextToken;
	        return response()->json(['token' => $token], 200);
	    } else {
	        return response()->json(['error' => 'Unauthorized'], 401);
	    }


    }//userlogin


}//UserController
