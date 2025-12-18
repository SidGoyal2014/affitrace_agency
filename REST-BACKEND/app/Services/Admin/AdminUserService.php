<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Auth;

class AdminUserService
{
    public function login($request)
    {
        if (
            Auth::attempt(
                array(
                    'email' => $request->email,
                    'password' => $request->password,
                    'user_type' => 'admin'
                )
            )
        ) {
            $token = Auth::user()->createToken('authToken')->plainTextToken;
            return ['token' => $token, 'code' => 200];
        } else {
            return ['error' => 'Unauthorized', 'code' => 401];
        }
    }
}
