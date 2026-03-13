<?php

namespace App\Services\Admin;

use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleService
{
    /**
     * Get all roles, optionally filtered by user_type.
     */
    public function getAllRoles($userType = null)
    {
        $query = Role::with('modules');

        if ($userType) {
            $query->where('user_type', $userType);
        }

        return $query->orderBy('user_type')->orderBy('name')->get();
    }

    /**
     * Create a new role with module permissions.
     */
    public function createRole(array $data)
    {
        $validator = Validator::make($data, [
            'name'        => 'required|string|max:255',
            'user_type'   => 'required|in:admin,affiliate,advertiser,employee',
            'description' => 'nullable|string',
            'is_default'  => 'boolean',
            'modules'     => 'required|array',
            'modules.*.module_id'  => 'required|exists:modules,id',
            'modules.*.can_view'   => 'boolean',
            'modules.*.can_create' => 'boolean',
            'modules.*.can_edit'   => 'boolean',
            'modules.*.can_delete' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors(), 'code' => 422];
        }

        // Unset other defaults for the same user type
        if (!empty($data['is_default'])) {
            Role::where('user_type', $data['user_type'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $role = Role::create([
            'name' => $data['name'],
            'user_type' => $data['user_type'],
            'description' => $data['description'] ?? null,
            'is_default' => !empty($data['is_default']),
        ]);

        // Attach module permissions
        $moduleData = [];
        foreach ($data['modules'] as $mod) {
            $moduleData[$mod['module_id']] = [
                'can_view'   => $mod['can_view'] ?? true,
                'can_create' => $mod['can_create'] ?? false,
                'can_edit'   => $mod['can_edit'] ?? false,
                'can_delete' => $mod['can_delete'] ?? false,
            ];
        }
        $role->modules()->sync($moduleData);

        return ['status' => true, 'role' => $role->load('modules')];
    }

    /**
     * Get role details with module permissions.
     */
    public function getRole($id)
    {
        return Role::with('modules')->findOrFail($id);
    }

    /**
     * Update a role and its module permissions.
     */
    public function updateRole($id, array $data)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($data, [
            'name'        => 'sometimes|required|string|max:255',
            'user_type'   => 'sometimes|required|in:admin,affiliate,advertiser,employee',
            'description' => 'nullable|string',
            'is_default'  => 'boolean',
            'modules'     => 'sometimes|required|array',
            'modules.*.module_id'  => 'required_with:modules|exists:modules,id',
            'modules.*.can_view'   => 'boolean',
            'modules.*.can_create' => 'boolean',
            'modules.*.can_edit'   => 'boolean',
            'modules.*.can_delete' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors(), 'code' => 422];
        }

        // If setting as default, unset other defaults
        if (!empty($data['is_default'])) {
            $userType = $data['user_type'] ?? $role->user_type;
            Role::where('user_type', $userType)
                ->where('is_default', true)
                ->where('id', '!=', $role->id)
                ->update(['is_default' => false]);
        }

        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['user_type'])) $updateData['user_type'] = $data['user_type'];
        if (array_key_exists('description', $data)) $updateData['description'] = $data['description'];
        if (isset($data['is_default'])) $updateData['is_default'] = $data['is_default'];

        $role->update($updateData);

        // Update module permissions if provided
        if (isset($data['modules'])) {
            $moduleData = [];
            foreach ($data['modules'] as $mod) {
                $moduleData[$mod['module_id']] = [
                    'can_view'   => $mod['can_view'] ?? true,
                    'can_create' => $mod['can_create'] ?? false,
                    'can_edit'   => $mod['can_edit'] ?? false,
                    'can_delete' => $mod['can_delete'] ?? false,
                ];
            }
            $role->modules()->sync($moduleData);
        }

        return ['status' => true, 'role' => $role->load('modules')];
    }

    /**
     * Delete a role (only if no users are assigned).
     */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return ['status' => false, 'message' => 'Cannot delete role: ' . $role->users()->count() . ' user(s) are still assigned to this role.', 'code' => 409];
        }

        $role->modules()->detach();
        $role->delete();

        return ['status' => true, 'message' => 'Role deleted successfully'];
    }
}
