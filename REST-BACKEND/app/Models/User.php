<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

        // No role assigned = no access
        if (!$this->role_id || !$this->role) {
            return false;
        }

        $module = $this->role->modules()->where('slug', $moduleSlug)->first();

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
                return [$module->slug => [
                    'can_view'   => true,
                    'can_create' => true,
                    'can_edit'   => true,
                    'can_delete' => true,
                ]];
            })->toArray();
        }

        if (!$this->role_id || !$this->role) {
            return [];
        }

        return $this->role->modules->mapWithKeys(function ($module) {
            return [$module->slug => [
                'can_view'   => (bool) $module->pivot->can_view,
                'can_create' => (bool) $module->pivot->can_create,
                'can_edit'   => (bool) $module->pivot->can_edit,
                'can_delete' => (bool) $module->pivot->can_delete,
            ]];
        })->toArray();
    }
}
