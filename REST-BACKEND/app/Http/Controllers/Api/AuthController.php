<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $settings = \App\Models\Setting::find(1);
        $isRecaptchaEnabled = (bool) ($settings->is_recaptcha_enabled ?? false);
        $secret = $settings->recaptcha_secret_key ?? env('RECAPTCHA_SECRET_KEY');
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type' => 'required|string'
        ];

        if ($isRecaptchaEnabled && !empty($secret)) {
            $rules['recaptcha_token'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate Google reCAPTCHA
        if ($isRecaptchaEnabled && !empty($secret) && !$this->verifyRecaptcha($request->recaptcha_token)) {
            return response()->json(['error' => 'reCAPTCHA verification failed. Please try again.'], 422);
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

            // 3. Load role & permissions for the UI
            $user->load(['role.modules', 'designation']);
            $permissions = $user->getPermissions();

            // Audit Log: User login
            \App\Models\AuditLog::create([
                'actor_id' => $user->id,
                'action' => $user->user_type . '_login',
                'details' => [
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                ],
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'user' => $user,
                'permissions' => $permissions,
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }


    /**
     * Get Platform Branding
     *
     * Retrieves the branding configurations (name, logo, favicon, colors) for the current tenant.
     * This endpoint does not require authentication.
     *
     * @unauthenticated
     */
    public function get_branding(Request $request)
    {
        $settings = \App\Models\Setting::find(1);

        $branding = [
            'platform_name' => $settings->platform_name ?? 'AffiTrace Agency',
            'platform_logo' => $settings->platform_logo ?? null,
            'platform_favicon' => $settings->platform_favicon ?? null,
            'primary_color' => $settings->primary_color ?? '#2563EB',
            'secondary_color' => $settings->secondary_color ?? '#1E293B',
            'accent_color' => $settings->accent_color ?? '#10B981',
            'support_email' => $settings->support_email ?? null,
            'custom_copyright' => $settings->custom_copyright ?? null,
            'recaptcha_site_key' => $settings->recaptcha_site_key ?? env('RECAPTCHA_SITE_KEY'),
            'is_recaptcha_enabled' => (bool) ($settings->is_recaptcha_enabled ?? false),
        ];
        return response()->json([
            'status' => 1,
            'branding' => $branding
        ]);
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

    /**
     * Update Timezone
     *
     * Modifies the timezone for the currently authenticated user.
     *
     * @bodyParam timezone string required The timezone to set (e.g. America/New_York)
     *
     * @response 200 {
     *   "message": "Timezone updated successfully",
     *   "timezone": "America/New_York"
     * }
     */
    public function updateTimezone(Request $request)
    {
        $user = $request->user();

        Log::info('Timezone update requested', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'email' => $user->email,
            'requested_timezone' => $request->timezone,
            'ip' => $request->ip(),
        ]);

        $validator = Validator::make($request->all(), [
            'timezone' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Timezone update validation failed', [
                'user_id' => $user->id,
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldTimezone = $user->timezone;
        $user->timezone = $request->timezone;

        try {
            $user->save();

            Log::info('Timezone updated successfully', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email' => $user->email,
                'old_timezone' => $oldTimezone,
                'new_timezone' => $user->timezone,
            ]);
        } catch (\Exception $e) {
            Log::error('Timezone update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to update timezone',
            ], 500);
        }

        return response()->json([
            'message' => 'Timezone updated successfully',
            'timezone' => $user->timezone
        ]);
    }

    /**
     * Get list of countries.
     */
    public function countries()
    {
        $countries = \App\Utils\IpLocationUtils::getCountry();
        unset($countries['ALL']);
        return response()->json($countries);
    }

    /**
     * Public registration endpoint.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:30',
            'country' => 'required|string|max:100',
            'traffic_source' => 'required|string|max:255',
            'terms' => 'accepted',
            'recaptcha_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate Google reCAPTCHA
        if (!$this->verifyRecaptcha($request->recaptcha_token)) {
            return response()->json(['error' => 'reCAPTCHA verification failed. Please try again.'], 422);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $email = $request->email;

        // Cache OTP and registration data for 10 minutes
        \Illuminate\Support\Facades\Cache::put('email_otp_' . $email, $otp, now()->addMinutes(10));
        \Illuminate\Support\Facades\Cache::put('pending_registration_' . $email, $request->except(['password', 'password_confirmation', 'recaptcha_token']) + ['password' => $request->password], now()->addMinutes(10));

        try {
            Mail::raw("Your AffiTrace email verification code is: $otp", function ($message) use ($email) {
                $message->to($email)->subject("Verify Your Email Address - AffiTrace");
            });
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send verification email. Please check your SMTP configuration.'], 500);
        }

        return response()->json([
            'status' => 'otp_sent',
            'email' => $email,
            'message' => 'A verification code has been sent to your email address.'
        ]);
    }

    /**
     * Verify email OTP and complete signup.
     */
    public function verify_email_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('email_otp_' . $email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['error' => 'Invalid or expired verification code.'], 422);
        }

        $regData = \Illuminate\Support\Facades\Cache::get('pending_registration_' . $email);
        if (!$regData) {
            return response()->json(['error' => 'Registration session expired. Please sign up again.'], 422);
        }

        DB::beginTransaction();
        try {
            $userCode = strtoupper(Str::random(10));

            $user = new User();
            $user->name = $regData['name'];
            $user->email = $regData['email'];
            $user->password = Hash::make($regData['password']);
            $user->phone = $regData['phone'];
            $user->country = $regData['country'];
            $user->user_type = 'affiliate';
            $user->status = \App\Enums\UserStatus::Pending->value;
            $user->code = $userCode;
            $user->timezone = 'UTC';
            $user->save();

            $details = new \App\Models\AffiliateUserDetail();
            $details->user_id = $user->id;
            $details->traffic_source = $regData['traffic_source'];
            $details->payout_threshold = 50.00;
            $details->save();

            \App\Models\AuditLog::create([
                'actor_id' => $user->id,
                'action' => 'affiliate_registration',
                'details' => [
                    'email' => $user->email,
                    'user_type' => 'affiliate',
                ],
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            \Illuminate\Support\Facades\Cache::forget('email_otp_' . $email);
            \Illuminate\Support\Facades\Cache::forget('pending_registration_' . $email);

            return response()->json([
                'message' => 'Registration completed successfully! Your account is pending administrator approval.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during account creation. Please try again.'], 500);
        }
    }

    /**
     * Send password reset link email.
     */
    public function forgot_password(Request $request, \App\Services\ForgotPasswordService $fpService)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $token = $fpService->generateResetToken($request->email);
            $fpService->sendResetLinkEmail($request->email, $token);

            return response()->json(['message' => 'We have e-mailed your password reset link!']);
        } catch (\Exception $e) {
            Log::error('Forgot password failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send password reset email.'], 500);
        }
    }

    /**
     * Reset password using token.
     */
    public function reset_password(Request $request, \App\Services\ForgotPasswordService $fpService)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $verifyToken = $fpService->verifyToken($request->email, $request->token);

        if (!$verifyToken) {
            return response()->json(['error' => 'Invalid or expired password reset token.'], 422);
        }

        try {
            $fpService->resetPassword($request->email, $request->password);
            return response()->json(['message' => 'Your password has been changed!']);
        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to reset password.'], 500);
        }
    }

    /**
     * Helper to verify reCAPTCHA.
     */
    protected function verifyRecaptcha($token)
    {
        $settings = \App\Models\Setting::find(1);
        $secret = $settings->recaptcha_secret_key ?? env('RECAPTCHA_SECRET_KEY');

        if (empty($secret)) {
            return true;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
            ]);
            return $response->json()['success'] ?? false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
