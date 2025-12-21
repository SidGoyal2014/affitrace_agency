<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, DB, Mail, Hash;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;


class ForgotPasswordController extends Controller
{

    protected $fpService;

    public function __construct(\App\Services\ForgotPasswordService $fpService)
    {
        $this->fpService = $fpService;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showForgetPasswordForm()
    {
        return view('gen_forms.fp.forgetPassword')
            ->with('page_name', 'Reset Password');

    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitForgetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users'
        ]);

        $token = $this->fpService->generateResetToken($request->email);
        $this->fpService->sendResetLinkEmail($request->email, $token);

        return back()->with('message', 'We have e-mailed your password reset link!');

    }//submitForgetPasswordForm

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showResetPasswordForm($token)
    {
        return view('gen_forms.fp.forgetPasswordLink', ['token' => $token])->with('page_name', 'Reset Password');

    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $verifyToken = $this->fpService->verifyToken($request->email, $request->token);

        if (!$verifyToken) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $this->fpService->resetPassword($request->email, $request->password);

        return redirect('/user/login')->with('message', 'Your password has been changed!');
    }


}//ForgotPasswordController