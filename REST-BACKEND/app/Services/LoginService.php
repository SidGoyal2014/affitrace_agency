<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginService
{
    public function authenticateUser($request, $user_type)
    {
        $user = User::where('email', $request->email)->where('user_type', $user_type)->first();

        if (!$user) {
            return ['status' => false, 'message' => 'The email or password is incorrect, please try again'];
        }

        if ($user->username != $request->eai) {
            return ['status' => false, 'message' => 'Credentials are not correct, please try again'];
        }

        if (!Hash::check($request->password, $user->password)) {
            return ['status' => false, 'message' => 'Passwords not correct, please try again'];
        }

        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
                'user_type' => $user_type
            ])
        ) {
            $token = Auth::user()->createToken('authToken')->plainTextToken;

            // Session handling is typically controller responsibility, but moving logic here for clarity if needed.
            // However, modifying the request session is better done in controller or passing session object.
            // For now, we will return the success data and let controller handle session or handle session here helper style.

            $request->session()->put('user_type', $user_type);
            $request->session()->put('email', $request->email);
            $request->session()->put('username', $request->eai);
            $request->session()->put('agency_id', $user->agency_id);
            $request->session()->put('logged_in_user_token', $token);

            return ['status' => true, 'user_type' => $user_type];
        }

        return ['status' => false, 'message' => 'The email or password is incorrect, please try again'];
    }

    public function logout($request)
    {
        $username = session('username');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $username;
    }
}
