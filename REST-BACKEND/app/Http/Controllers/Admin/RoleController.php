<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\RoleService;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * List all roles, optionally filtered by user_type.
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->getAllRoles($request->user_type);
        return response()->json(['roles' => $roles]);
    }

    /**
     * Create a new role with module permissions.
     */
    public function store(Request $request)
    {
        $result = $this->roleService->createRole($request->all());

        if (!$result['status']) {
            return response()->json(['errors' => $result['errors']], $result['code']);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role'    => $result['role'],
        ], 201);
    }

    /**
     * Get role details with module permissions.
     */
    public function show($id)
    {
        $role = $this->roleService->getRole($id);
        return response()->json(['role' => $role]);
    }

    /**
     * Update a role and its module permissions.
     */
    public function update(Request $request, $id)
    {
        $result = $this->roleService->updateRole($id, $request->all());

        if (!$result['status']) {
            return response()->json(['errors' => $result['errors']], $result['code']);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'role'    => $result['role'],
        ]);
    }

    /**
     * Delete a role (only if no users are assigned).
     */
    public function destroy($id)
    {
        $result = $this->roleService->deleteRole($id);

        if (!$result['status']) {
            return response()->json(['message' => $result['message']], $result['code']);
        }

        return response()->json(['message' => $result['message']]);
    }
}

