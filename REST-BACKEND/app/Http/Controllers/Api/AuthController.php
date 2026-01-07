<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @group Authentication
 *
 * Endpoints for user authentication, token management, and profile retrieval.
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * Authenticate a user and obtain access and refresh tokens.
     * This endpoint does not require authentication.
     *
     * @unauthenticated
     *
     * @bodyParam eai string required The agency/tenant identifier. Example: agency123
     * @bodyParam email string required The user's email address. Example: admin@example.com
     * @bodyParam password string required The user's password. Example: password123
     * @bodyParam user_type string required The type of user (admin, affiliate, advertiser). Example: admin
     *
     * @response 200 {
     *   "message": "Login successful",
     *   "access_token": "1|abc123...",
     *   "refresh_token": "2|def456...",
     *   "token_type": "Bearer",
     *   "user": {"id": 1, "name": "Admin User", "email": "admin@example.com"}
     * }
     * @response 401 {
     *   "error": "Invalid credentials"
     * }
     * @response 422 {
     *   "errors": {"email": ["The email field is required."]}
     * }
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

            // Check if user is active (handle legacy empty status as 'active')
            $rawStatus = $user->getRawOriginal('status');
            $userStatus = \App\Enums\UserStatus::tryFrom($rawStatus) ?? \App\Enums\UserStatus::Active;

            if ($userStatus !== \App\Enums\UserStatus::Active) {
                Auth::logout(); // Invalidate session/token immediately if any
                return response()->json(['error' => 'Account is not active'], 403);
            }

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
     * Refresh Token
     *
     * Exchange a valid refresh token for a new access/refresh token pair.
     * Must be authenticated with the refresh token (not access token).
     *
     * @response 200 {
     *   "message": "Token refreshed successfully",
     *   "access_token": "3|ghi789...",
     *   "refresh_token": "4|jkl012...",
     *   "token_type": "Bearer"
     * }
     * @response 403 {
     *   "message": "Invalid token ability. Access tokens cannot be used to refresh."
     * }
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
     * Logout
     *
     * Revoke the current access token, logging the user out.
     *
     * @response 200 {
     *   "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get Current User
     *
     * Retrieve the authenticated user's profile information.
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Admin User",
     *   "email": "admin@example.com",
     *   "user_type": "admin",
     *   "created_at": "2024-01-01T00:00:00.000000Z"
     * }
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}

