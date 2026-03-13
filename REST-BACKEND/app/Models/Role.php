<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'user_type', 'description', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Modules accessible by this role.
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'role_module')
                    ->withPivot('can_view', 'can_create', 'can_edit', 'can_delete')
                    ->withTimestamps();
    }

    /**
     * Users assigned to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
