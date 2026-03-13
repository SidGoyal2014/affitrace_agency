<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\DesignationService;

class DesignationController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    /**
     * List all designations.
     */
    public function index()
    {
        $designations = $this->designationService->getAllDesignations();

        return response()->json(['designations' => $designations]);
    }

    /**
     * Create a new designation.
     */
    public function store(Request $request)
    {
        $result = $this->designationService->createDesignation($request->all());

        if (!$result['status']) {
            return response()->json(['errors' => $result['errors']], $result['code']);
        }

        return response()->json([
            'message'     => 'Designation created successfully',
            'designation' => $result['designation'],
        ], 201);
    }

    /**
     * Update a designation.
     */
    public function update(Request $request, $id)
    {
        $result = $this->designationService->updateDesignation($id, $request->all());

        if (!$result['status']) {
            return response()->json(['errors' => $result['errors']], $result['code']);
        }

        return response()->json([
            'message'     => 'Designation updated successfully',
            'designation' => $result['designation'],
        ]);
    }

    /**
     * Delete a designation (only if no users are assigned).
     */
    public function destroy($id)
    {
        $result = $this->designationService->deleteDesignation($id);

        if (!$result['status']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        return response()->json(['message' => $result['message']]);
    }
}
