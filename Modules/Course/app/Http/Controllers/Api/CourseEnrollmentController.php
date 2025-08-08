<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Course\Actions\EnrollEmployeeInCourseAction;
use Modules\Course\Models\Course;
use Modules\Employee\Models\Employee;

class CourseEnrollmentController extends Controller
{
    public function enroll(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        try {
            $enrollment = EnrollEmployeeInCourseAction::make()->handle($course, $employee);

            return response()->json([
                'success' => true,
                'message' => 'Employee enrolled successfully',
                'data' => [
                    'course_id' => $course->id,
                    'employee_id' => $employee->id,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'status' => $enrollment->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function unenroll(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        $course->enrolledEmployees()->detach($employee->id);

        return response()->json([
            'success' => true,
            'message' => 'Employee unenrolled successfully'
        ]);
    }

    public function enrollments(Course $course): JsonResponse
    {
        $enrollments = $course->enrolledEmployees()
            ->withPivot(['enrolled_at', 'completed_at', 'status'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $enrollments->map(function ($employee) {
                return [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->user->name,
                    'enrolled_at' => $employee->pivot->enrolled_at,
                    'completed_at' => $employee->pivot->completed_at,
                    'status' => $employee->pivot->status,
                ];
            })
        ]);
    }
}
