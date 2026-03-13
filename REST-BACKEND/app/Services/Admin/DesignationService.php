<?php

namespace App\Services\Admin;

use App\Models\Designation;
use Illuminate\Support\Facades\Validator;

class DesignationService
{
    /**
     * Get all designations.
     */
    public function getAllDesignations()
    {
        return Designation::withCount('users')->orderBy('name')->get();
    }

    /**
     * Create a new designation.
     */
    public function createDesignation(array $data)
    {
        $validator = Validator::make($data, [
            'name'        => 'required|string|max:255|unique:designations,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors(), 'code' => 422];
        }

        $designation = Designation::create([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null
        ]);

        return ['status' => true, 'designation' => $designation];
    }

    /**
     * Update an existing designation.
     */
    public function updateDesignation($id, array $data)
    {
        $designation = Designation::findOrFail($id);

        $validator = Validator::make($data, [
            'name'        => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors(), 'code' => 422];
        }

        $designation->update([
            'name' => $data['name'] ?? $designation->name,
            'description' => array_key_exists('description', $data) ? $data['description'] : $designation->description
        ]);

        return ['status' => true, 'designation' => $designation];
    }

    /**
     * Delete a designation if it has no assigned users.
     */
    public function deleteDesignation($id)
    {
        $designation = Designation::findOrFail($id);

        if ($designation->users()->count() > 0) {
            return ['status' => false, 'message' => 'Cannot delete designation: ' . $designation->users()->count() . ' employee(s) are assigned to it.', 'code' => 409];
        }

        $designation->delete();

        return ['status' => true, 'message' => 'Designation deleted successfully'];
    }
}
