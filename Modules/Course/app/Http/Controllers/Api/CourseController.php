<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Course\Models\Course;
use Modules\Course\Transformers\CourseTransformer;
use Spatie\Fractal\Fractal;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['categories']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by title or instructor
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('instructor', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $courses = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => Fractal::create()
                ->collection($courses->items(), new CourseTransformer())
                ->toArray(),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'last_page' => $courses->lastPage(),
            ]
        ]);
    }

    public function show($id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $course->load(['categories', 'enrolledEmployees']);

        return response()->json([
            'success' => true,
            'data' => Fractal::create()
                ->item($course, new CourseTransformer())
                ->toArray()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|string|max:255',
            'duration_hours' => 'required|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published,completed,cancelled',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'price' => 'nullable|numeric|min:0',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:course_categories,id',
        ]);

        $categoryIds = $validated['category_ids'] ?? [];
        unset($validated['category_ids']);

        $course = Course::create($validated);

        if (!empty($categoryIds)) {
            $course->categories()->attach($categoryIds);
        }

        $course->load('categories');

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => Fractal::create()
                ->item($course, new CourseTransformer())
                ->toArray()
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'instructor' => 'sometimes|string|max:255',
            'duration_hours' => 'sometimes|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:draft,published,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'price' => 'nullable|numeric|min:0',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:course_categories,id',
        ]);

        $categoryIds = $validated['category_ids'] ?? null;
        unset($validated['category_ids']);

        $course->update($validated);

        if ($categoryIds !== null) {
            $course->categories()->sync($categoryIds);
        }

        $course->load('categories');

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => Fractal::create()
                ->item($course, new CourseTransformer())
                ->toArray()
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }
}
