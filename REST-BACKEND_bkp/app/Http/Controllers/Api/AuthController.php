<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login User and return Token
     */

    public function login(Request $request)
    {
        // 1. Validate Input
        $validator = Validator::make($request->all(), [
            'eai' => 'required', // Agency ID / Username
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Authenticate (DB connection is already set by api.tenant middleware)
        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
                'user_type' => $request->user_type
            ])
        ) {

            $user = Auth::user();

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    /**
     * Logout User
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get User Profile
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
