<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminCategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(AdminCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();

        return response()->json(['status' => 1, 'categories' => $categories]);
    }

    public function formData(Request $request)
    {
        $excludeId = $request->query('exclude_id') ? (int) $request->query('exclude_id') : null;
        $options   = $this->categoryService->getFormData($excludeId);

        return response()->json(['status' => 1, 'parent_options' => $options]);
    }

    public function show($id)
    {
        try {
            $category = $this->categoryService->getCategory((int) $id);
            return response()->json(['status' => 1, 'category' => $category]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Category not found.'], 404);
        }
    }

    public function store(Request $request)
    {
        $result = $this->categoryService->createCategory($request->all());

        if (!$result['status']) {
            return response()->json(['status' => 0, 'errors' => $result['errors']], $result['code']);
        }

        return response()->json([
            'status'   => 1,
            'message'  => 'Category created successfully.',
            'category' => $result['category'],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $result = $this->categoryService->updateCategory((int) $id, $request->all());

        if (!$result['status']) {
            return response()->json(['status' => 0, 'errors' => $result['errors']], $result['code']);
        }

        return response()->json([
            'status'   => 1,
            'message'  => 'Category updated successfully.',
            'category' => $result['category'],
        ]);
    }

    public function toggleStatus($id)
    {
        try {
            $result = $this->categoryService->toggleStatus((int) $id);
            return response()->json([
                'status'    => 1,
                'message'   => $result['message'],
                'is_active' => $result['is_active'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Category not found.'], 404);
        }
    }
}