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

    public function sendResetLinkEmail($email, $token, $resetUrl, $platformName)
    {
        $templatePath = base_path('../UI/common/resources/templates/email/forgetPassword.html');
        if (!file_exists($templatePath)) {
            throw new \Exception("Password reset email template not found at: " . $templatePath);
        }

        $htmlContent = file_get_contents($templatePath);
        $htmlContent = str_replace('{resetUrl}', $resetUrl, $htmlContent);
        $htmlContent = str_replace('{platformName}', $platformName, $htmlContent);
        $htmlContent = str_replace('{currentYear}', date('Y'), $htmlContent);

        $apiUrl = env('EMAIL_API_URL');
        $apiKey = env('EMAIL_API_KEY');
        $fromAddress = env('EMAIL_API_FROM_ADDRESS', 'noreply@yourdomain.com');
        $fromName = env('EMAIL_API_FROM_NAME', 'AffiTrace Security');

        if (empty($apiUrl)) {
            // Log fallback to local SMTP/Mailtrap if API URL is not set
            \Illuminate\Support\Facades\Log::warning('EMAIL_API_URL not configured. Falling back to SMTP.');
            Mail::send(
                [],
                [],
                function ($message) use ($email, $htmlContent, $platformName) {
                    $message->to($email)
                        ->subject('Reset Password - ' . $platformName)
                        ->html($htmlContent);
                }
            );
            return;
        }

        $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        $tenantId = request()->header('X-Agency-ID') ?? request()->input('eai') ?? $dbName;

        // Call the HTTP API
        if (str_contains($apiUrl, 'mailgun.net')) {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth('api', $apiKey)
                ->asForm()
                ->post($apiUrl, [
                    'from'    => "{$fromName} <{$fromAddress}>",
                    'to'      => $email,
                    'subject' => 'Reset Password - ' . $platformName,
                    'html'    => $htmlContent
                ]);
        } else {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'X-Agency-ID'   => $tenantId
            ])->post($apiUrl, [
                'from'      => $fromAddress,
                'from_name' => $fromName,
                'to'        => $email,
                'subject'   => 'Reset Password - ' . $platformName,
                'html'      => $htmlContent
            ]);
        }

        if (!$response->successful()) {
            throw new \Exception("Failed to send password reset email via API. Status: " . $response->status() . " Response: " . $response->body());
        }
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
