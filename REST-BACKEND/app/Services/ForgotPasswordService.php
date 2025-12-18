<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordService
{
    public function generateResetToken($email)
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        return $token;
    }

    public function sendResetLinkEmail($email, $token)
    {
        Mail::send(
            'gen_forms.fp.emails.forgetPassword',
            ['token' => $token],
            function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password');
            }
        );
    }

    public function verifyToken($email, $token)
    {
        return DB::table('password_reset_tokens')
            ->where([
                'email' => $email,
                'token' => $token
            ])
            ->first();
    }

    public function resetPassword($email, $password)
    {
        $user = User::where('email', $email)
            ->update(['password' => Hash::make($password)]);

        // Note: The original code had a likely typo: 'gen_forms.fp.password_reset_tokens'
        // Assuming standard table 'password_reset_tokens'
        DB::table('password_reset_tokens')->where(['email' => $email])->delete();

        return $user;
    }
}
