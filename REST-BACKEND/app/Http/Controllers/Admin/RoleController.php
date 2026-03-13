<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * List all roles, optionally filtered by user_type.
     */
    public function index(Request $request)
    {
        $query = Role::with('modules');

        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        $roles = $query->orderBy('user_type')->orderBy('name')->get();

        return response()->json(['roles' => $roles]);
    }

    /**
     * Create a new role with module permissions.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If this is set as default, unset other defaults for same user_type
        if ($request->is_default) {
            Role::where('user_type', $request->user_type)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $role = Role::create($request->only(['name', 'user_type', 'description', 'is_default']));

        // Attach module permissions
        $moduleData = [];
        foreach ($request->modules as $mod) {
            $moduleData[$mod['module_id']] = [
                'can_view'   => $mod['can_view'] ?? true,
                'can_create' => $mod['can_create'] ?? false,
                'can_edit'   => $mod['can_edit'] ?? false,
                'can_delete' => $mod['can_delete'] ?? false,
            ];
        }
        $role->modules()->sync($moduleData);

        return response()->json([
            'message' => 'Role created successfully',
            'role'    => $role->load('modules'),
        ], 201);
    }

    /**
     * Get role details with module permissions.
     */
    public function show($id)
    {
        $role = Role::with('modules')->findOrFail($id);

        return response()->json(['role' => $role]);
    }

    /**
     * Update a role and its module permissions.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If setting as default, unset other defaults
        if ($request->is_default) {
            $userType = $request->user_type ?? $role->user_type;
            Role::where('user_type', $userType)
                ->where('is_default', true)
                ->where('id', '!=', $role->id)
                ->update(['is_default' => false]);
        }

        $role->update($request->only(['name', 'user_type', 'description', 'is_default']));

        // Update module permissions if provided
        if ($request->has('modules')) {
            $moduleData = [];
            foreach ($request->modules as $mod) {
                $moduleData[$mod['module_id']] = [
                    'can_view'   => $mod['can_view'] ?? true,
                    'can_create' => $mod['can_create'] ?? false,
                    'can_edit'   => $mod['can_edit'] ?? false,
                    'can_delete' => $mod['can_delete'] ?? false,
                ];
            }
            $role->modules()->sync($moduleData);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'role'    => $role->load('modules'),
        ]);
    }

    /**
     * Delete a role (only if no users are assigned).
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role: ' . $role->users()->count() . ' user(s) are still assigned to this role.',
            ], 409);
        }

        $role->modules()->detach();
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
