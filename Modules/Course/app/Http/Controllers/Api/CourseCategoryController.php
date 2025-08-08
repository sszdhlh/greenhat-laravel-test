<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseCategoryTransformer;
use Spatie\Fractal\Fractal;

class CourseCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CourseCategory::query();

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => Fractal::create()
                ->collection($categories, new CourseCategoryTransformer())
                ->toArray()
        ]);
    }

    public function show($id): JsonResponse
    {
        $courseCategory = CourseCategory::findOrFail($id);
        $courseCategory->load('courses');

        return response()->json([
            'success' => true,
            'data' => Fractal::create()
                ->item($courseCategory, new CourseCategoryTransformer())
                ->toArray()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:course_categories,slug',
            'is_active' => 'boolean',
        ]);

        $category = CourseCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course category created successfully',
            'data' => Fractal::create()
                ->item($category, new CourseCategoryTransformer())
                ->toArray()
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $courseCategory = CourseCategory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:course_categories,name,' . $courseCategory->id,
            'description' => 'nullable|string',
            'slug' => 'sometimes|string|max:255|unique:course_categories,slug,' . $courseCategory->id,
            'is_active' => 'boolean',
        ]);

        $courseCategory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course category updated successfully',
            'data' => Fractal::create()
                ->item($courseCategory, new CourseCategoryTransformer())
                ->toArray()
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $courseCategory = CourseCategory::findOrFail($id);
        $courseCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course category deleted successfully'
        ]);
    }
}
