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

            // 1. Create Short-lived Access Token (1 Hour)
            $accessToken = $user->createToken('access_token', ['access-api'], now()->addMinutes(60))->plainTextToken;

            // 2. Create Long-lived Refresh Token (2 Weeks)
            $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], now()->addWeeks(2))->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    /**
     * Refresh Access Token
     * Request must be authenticated with the Refresh Token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        // Ensure the current token has the 'issue-access-token' ability
        if (!$user->currentAccessToken()->can('issue-access-token')) {
            return response()->json(['message' => 'Invalid token ability. Access tokens cannot be used to refresh.'], 403);
        }

        // Token Rotation: Revoke the old refresh token
        $user->currentAccessToken()->delete();

        // Issue new pair
        $accessToken = $user->createToken('access_token', ['access-api'], now()->addMinutes(60))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], now()->addWeeks(2))->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer'
        ]);
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
