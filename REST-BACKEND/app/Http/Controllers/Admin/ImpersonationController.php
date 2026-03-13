<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user (affiliate or advertiser).
     *
     * Creates a 60-minute scoped token, logs the session, and notifies the impersonated user.
     */
    public function start(Request $request, $userId)
    {
        $admin = Auth::user();

        // Only admin or employees with impersonation permission can do this
        if (!$admin->canImpersonate()) {
            return response()->json(['message' => 'You do not have permission to impersonate users.'], 403);
        }

        $targetUser = User::findOrFail($userId);

        // Can only impersonate affiliates and advertisers
        $allowedTypes = ['affiliate', 'advertiser'];
        if (!in_array(strtolower($targetUser->user_type), $allowedTypes)) {
            return response()->json(['message' => 'You can only impersonate affiliates and advertisers.'], 403);
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
            'ip_address'      => $request->ip(),
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
            // Log email failure but don't block impersonation
            \Illuminate\Support\Facades\Log::warning('Failed to notify impersonated user: ' . $e->getMessage());
        }

        return response()->json([
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
        ]);
    }

    /**
     * Stop impersonating a user.
     *
     * Revokes the impersonation token and logs the end time.
     */
    public function stop(Request $request)
    {
        $user = Auth::user();
        $token = $user->currentAccessToken();

        // Check if this is an impersonation token
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

            return response()->json(['message' => 'Impersonation ended successfully']);
        }

        return response()->json(['message' => 'No active impersonation session found.'], 400);
    }

    /**
     * List impersonation audit logs.
     */
    public function logs(Request $request)
    {
        $query = ImpersonationLog::with(['impersonator:id,name,email', 'impersonated:id,name,email,user_type'])
            ->orderByDesc('started_at');

        if ($request->has('impersonator_id')) {
            $query->where('impersonator_id', $request->impersonator_id);
        }

        if ($request->has('impersonated_id')) {
            $query->where('impersonated_id', $request->impersonated_id);
        }

        $logs = $query->paginate(25);

        return response()->json($logs);
    }
}
