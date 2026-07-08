<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role_id',
        'designation_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => \App\Enums\UserStatus::class,
    ];

    // ─── Relationships ────────────────────────────────────

    /**
     * The role assigned to this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The designation assigned to this user (employees only).
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    // ─── RBAC Helpers ─────────────────────────────────────

    /**
     * Check if this user has access to a specific module action.
     *
     * Admin users always have full access.
     *
     * @param  string  $moduleSlug  e.g., 'offers', 'reports'
     * @param  string  $action      e.g., 'can_view', 'can_create', 'can_edit', 'can_delete'
     * @return bool
     */
    public function hasModuleAccess(string $moduleSlug, string $action = 'can_view'): bool
    {
        // Admin always has full access
        if (strtolower($this->user_type) === 'admin') {
            return true;
        }

        $roleId = $this->role_id;
        $role = $this->role;

        // Fallback to the default role of this user type if no role is explicitly assigned
        if (!$roleId) {
            $defaultRole = Role::where('user_type', $this->user_type)
                ->where('is_default', true)
                ->first();
            if ($defaultRole) {
                $roleId = $defaultRole->id;
                $role = $defaultRole;
            }
        }

        // No role assigned = no access (Uses the fallback $roleId local variable)
        if (!$roleId) {
            return false;
        }

        // Avoid N+1 queries by accessing lazy loaded modules relation collection
        if ($role && !$role->relationLoaded('modules')) {
            $role->loadMissing('modules');
        }

        if (!$role) {
            return false;
        }

        $module = $role->modules->firstWhere('slug', $moduleSlug);

        if (!$module) {
            return false;
        }

        return (bool) $module->pivot->{$action};
    }


    /**
     * Check if this user can impersonate other users.
     *
     * Admin always can. Employees can if their role includes the 'impersonation' module.
     *
     * @return bool
     */
    public function canImpersonate(): bool
    {
        if (strtolower($this->user_type) === 'admin') {
            return true;
        }

        return $this->hasModuleAccess('impersonation', 'can_view');
    }

    /**
     * Get all module permissions for this user's role.
     *
     * Returns an array keyed by module slug with permission flags.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        // Admin gets all permissions
        if (strtolower($this->user_type) === 'admin') {
            return Module::all()->mapWithKeys(function ($module) {
                return [
                    $module->slug => [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                ];
            })->toArray();
        }

        $role = $this->role;
        $roleId = $this->role_id;

        // Fallback to the default role of this user type if no role is explicitly assigned
        if (!$roleId) {
            $defaultRole = Role::where('user_type', $this->user_type)
                ->where('is_default', true)
                ->first();
            if ($defaultRole) {
                $role = $defaultRole;
                $roleId = $defaultRole->id;
            }
        }

        if (!$roleId || !$role) {
            return [];
        }

        if (!$role->relationLoaded('modules')) {
            $role->loadMissing('modules');
        }

        return $role->modules->mapWithKeys(function ($module) {
            return [
                $module->slug => [
                    'can_view' => (bool) $module->pivot->can_view,
                    'can_create' => (bool) $module->pivot->can_create,
                    'can_edit' => (bool) $module->pivot->can_edit,
                    'can_delete' => (bool) $module->pivot->can_delete,
                ]
            ];
        })->toArray();
    }


    public function receivedNotifications(): HasMany
    {
        return $this->hasMany(AgencyNotificationRecipient::class, 'recipient_id');
    }
}
