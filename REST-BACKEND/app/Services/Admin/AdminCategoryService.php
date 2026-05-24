<?php

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class AdminCategoryService
{
    public function getAllCategories()
    {
        return Category::with('parent')
            ->orderBy('category_name')
            ->get();
    }

    public function getFormData(?int $excludeId = null)
    {
        $query = Category::where('is_active', 'Y')->orderBy('category_name');

        if ($excludeId) {
            $descendantIds = $this->getDescendantIds($excludeId);
            $descendantIds[] = $excludeId;
            $query->whereNotIn('id', $descendantIds);
        }

        $categories = $query->get(['id', 'category_name']);

        $options = [['id' => 0, 'category_name' => 'None (Top Level)']];
        foreach ($categories as $cat) {
            $options[] = ['id' => $cat->id, 'category_name' => $cat->category_name];
        }

        return $options;
    }

    public function getCategory(int $id)
    {
        return Category::with('parent')->findOrFail($id);
    }

    public function createCategory(array $data)
    {
        $validator = Validator::make($data, [
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'parent_id'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors(), 'code' => 422];
        }

        $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : 0;

        if ($parentId !== 0) {
            $parentExists = Category::where('id', $parentId)->exists();
            if (!$parentExists) {
                return ['status' => false, 'errors' => ['parent_id' => ['Selected parent category does not exist.']], 'code' => 422];
            }
        }

        $category = Category::create([
            'category_name' => $data['category_name'],
            'parent_id'     => $parentId,
            'is_active'     => 'Y',
        ]);

        return ['status' => true, 'category' => $category];
    }

    public function updateCategory(int $id, array $data)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($data, [
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
            'parent_id'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors(), 'code' => 422];
        }

        $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : 0;

        if ($parentId !== 0) {
            if ($parentId === $id) {
                return ['status' => false, 'errors' => ['parent_id' => ['A category cannot be its own parent.']], 'code' => 422];
            }

            $descendantIds = $this->getDescendantIds($id);
            if (in_array($parentId, $descendantIds)) {
                return ['status' => false, 'errors' => ['parent_id' => ['Cannot set a descendant category as parent.']], 'code' => 422];
            }

            $parentExists = Category::where('id', $parentId)->exists();
            if (!$parentExists) {
                return ['status' => false, 'errors' => ['parent_id' => ['Selected parent category does not exist.']], 'code' => 422];
            }
        }

        $category->update([
            'category_name' => $data['category_name'],
            'parent_id'     => $parentId,
        ]);

        return ['status' => true, 'category' => $category->fresh('parent')];
    }

    public function toggleStatus(int $id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = ($category->is_active === 'Y') ? 'N' : 'Y';
        $category->save();

        $message = ($category->is_active === 'Y') ? 'Category activated.' : 'Category deactivated.';

        return ['status' => true, 'message' => $message, 'is_active' => $category->is_active];
    }

    private function getDescendantIds(int $categoryId): array
    {
        $ids = [];
        $children = Category::where('parent_id', $categoryId)->get(['id']);
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids   = array_merge($ids, $this->getDescendantIds($child->id));
        }
        return $ids;
    }
}