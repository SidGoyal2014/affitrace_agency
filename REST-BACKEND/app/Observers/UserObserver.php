<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    protected function log(string $action, User $user)
    {
        AuditLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'details' => [
                'target_id' => $user->id,
                'target_name' => $user->name,
                'user_type' => $user->user_type,
                'email' => $user->email,
                'status' => $user->status ? $user->status->value : null,
            ],
            'ip_address' => request()->ip(),
        ]);
    }

    public function created(User $user): void
    {
        $this->log('user_created', $user);
    }

    public function updated(User $user): void
    {
        $dirty = [];
        if ($user->isDirty('role_id')) {
            $dirty['old_role_id'] = $user->getOriginal('role_id');
            $dirty['new_role_id'] = $user->role_id;
        }
        if ($user->isDirty('status')) {
            $dirty['old_status'] = $user->getOriginal('status') ? $user->getOriginal('status')->value : null;
            $dirty['new_status'] = $user->status ? $user->status->value : null;
        }

        if (!empty($dirty)) {
            AuditLog::create([
                'actor_id' => Auth::id(),
                'action' => 'user_updated_security',
                'details' => array_merge($dirty, [
                    'target_id' => $user->id,
                    'target_name' => $user->name,
                ]),
                'ip_address' => request()->ip(),
            ]);
        } else {
            $this->log('user_updated', $user);
        }
    }

    public function deleted(User $user): void
    {
        $this->log('user_deleted', $user);
    }
}
