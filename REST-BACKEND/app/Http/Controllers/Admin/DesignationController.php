<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    /**
     * List all designations.
     */
    public function index()
    {
        $designations = Designation::withCount('users')->orderBy('name')->get();

        return response()->json(['designations' => $designations]);
    }

    /**
     * Create a new designation.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:designations,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $designation = Designation::create($request->only(['name', 'description']));

        return response()->json([
            'message'     => 'Designation created successfully',
            'designation' => $designation,
        ], 201);
    }

    /**
     * Update a designation.
     */
    public function update(Request $request, $id)
    {
        $designation = Designation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $designation->update($request->only(['name', 'description']));

        return response()->json([
            'message'     => 'Designation updated successfully',
            'designation' => $designation,
        ]);
    }

    /**
     * Delete a designation (only if no users are assigned).
     */
    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);

        if ($designation->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete designation: ' . $designation->users()->count() . ' employee(s) are assigned to it.',
            ], 409);
        }

        $designation->delete();

        return response()->json(['message' => 'Designation deleted successfully']);
    }
}
