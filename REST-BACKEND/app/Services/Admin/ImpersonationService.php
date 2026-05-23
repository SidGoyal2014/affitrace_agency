<?php

namespace App\Services\Admin;

use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class ImpersonationService
{
    /**
     * Start impersonating a user (affiliate or advertiser).
     */
    public function startImpersonation(User $admin, $targetUserId, $ipAddress)
    {
        if (!$admin->canImpersonate()) {
            return ['status' => false, 'message' => 'You do not have permission to impersonate users.', 'code' => 403];
        }

        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return ['status' => false, 'message' => 'Target user not found.', 'code' => 404];
        }

        $allowedTypes = ['affiliate', 'advertiser'];
        if (!in_array(strtolower($targetUser->user_type), $allowedTypes)) {
            return ['status' => false, 'message' => 'You can only impersonate affiliates and advertisers.', 'code' => 403];
        }

        // Create a 60-minute scoped impersonation token
        $impersonationToken = $targetUser->createToken(
            'impersonation_token',
            ['access-api', 'impersonated'],
            now()->addMinutes(60)
        )->plainTextToken;

        // Log the impersonation
        $log = ImpersonationLog::create([
            'impersonator_id' => $admin->id,
            'impersonated_id' => $targetUser->id,
            'started_at'      => now(),
            'ip_address'      => $ipAddress,
            'notified'        => false,
        ]);

        // Notify the impersonated user via email
        try {
            Mail::raw(
                'Your account on Affitrace was accessed by an administrator (' . $admin->name . ') at ' . now()->format('d M Y, h:i A') . '. If you did not request this, please contact support.',
                function ($message) use ($targetUser) {
                    $message->to($targetUser->email)
                            ->subject('Affitrace: Your account was accessed by an administrator');
                }
            );
            $log->update(['notified' => true]);
        } catch (\Exception $e) {
            Log::warning('Failed to notify impersonated user: ' . $e->getMessage());
        }

        return [
            'status'        => true,
            'message'       => 'Impersonation started',
            'token'         => $impersonationToken,
            'token_type'    => 'Bearer',
            'user'          => $targetUser,
            'expires_in'    => 60, // minutes
            'log_id'        => $log->id,
            'impersonator'  => [
                'id'   => $admin->id,
                'name' => $admin->name,
            ],
            'code' => 200
        ];
    }

    /**
     * Stop impersonating a user.
     */
    public function stopImpersonation(User $user, PersonalAccessToken $token = null)
    {
        if ($token && in_array('impersonated', $token->abilities ?? [])) {
            // Find the active impersonation log
            $log = ImpersonationLog::where('impersonated_id', $user->id)
                ->whereNull('ended_at')
                ->latest('started_at')
                ->first();

            if ($log) {
                $log->update(['ended_at' => now()]);
            }

            // Revoke the impersonation token
            $token->delete();

            return ['status' => true, 'message' => 'Impersonation ended successfully', 'code' => 200];
        }

        return ['status' => false, 'message' => 'No active impersonation session found.', 'code' => 400];
    }

    /**
     * Get paginated logs.
     */
    public function getLogs(array $filters = [], $perPage = 25)
    {
        $query = ImpersonationLog::with(['impersonator:id,name,email', 'impersonated:id,name,email,user_type'])
            ->orderByDesc('started_at');

        if (!empty($filters['impersonator_id'])) {
            $query->where('impersonator_id', $filters['impersonator_id']);
        }

        if (!empty($filters['impersonated_id'])) {
            $query->where('impersonated_id', $filters['impersonated_id']);
        }

        return $query->paginate($perPage);
    }
}
