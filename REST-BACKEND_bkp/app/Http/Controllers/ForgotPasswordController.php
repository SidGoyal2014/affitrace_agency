<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, DB, Mail, Hash; 
use Carbon\Carbon; 
use App\Models\User; 
use Illuminate\Support\Str;


class ForgotPasswordController extends Controller{


      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showForgetPasswordForm(){
         return view('gen_forms.fp.forgetPassword')
			 ->with('page_name', 'Reset Password');

      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitForgetPasswordForm(Request $request){

          $request->validate([
              'email' => 'required|email|exists:users'
          ]);
  
          $token = Str::random(64);
  
          /*
          DB::table('password_reset_tokens')->insert([
              'email' => $request->email, 
              'token' => $token, 
              'created_at' => Carbon::now()
            ]);
          */

          DB::table('password_reset_tokens')->updateOrInsert(
              ['email' => $request->email],  // condition: match by email
              [
                  'token' => $token,
                  'created_at' => Carbon::now()
              ]
          );

  
          Mail::send(

              'gen_forms.fp.emails.forgetPassword', 
              ['token' => $token], 
              function($message) use($request){

                $message->to($request->email);
                $message->subject('Reset Password');

              }

         );
  
          return back()->with('message', 'We have e-mailed your password reset link!');

      }//submitForgetPasswordForm

      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showResetPasswordForm($token) { 
         return view('gen_forms.fp.forgetPasswordLink', ['token' => $token])->with('page_name', 'Reset Password');

      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitResetPasswordForm(Request $request){

          $request->validate([
              'email' => 'required|email|exists:users',
              'password' => 'required|string|min:6|confirmed',
              'password_confirmation' => 'required'
          ]);
  
          $verifyToken = DB::table('password_reset_tokens')
                              ->where([
                                'email' => $request->email, 
                                'token' => $request->token
                              ])
                              ->first();
  
          if(!$verifyToken){
              return back()->withInput()->with('error', 'Invalid token!');
          }
  
          $user = User::where('email', $request->email)
                      ->update(['password' => Hash::make($request->password)]);
 
          DB::table('gen_forms.fp.password_reset_tokens')->where(['email'=> $request->email])->delete();
  

          return redirect('/user/login')->with('message', 'Your password has been changed!');
      }


}//ForgotPasswordController