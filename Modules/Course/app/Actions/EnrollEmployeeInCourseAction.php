<?php

namespace Modules\Course\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Events\EmployeeEnrolledInCourse;
use Modules\Course\Exceptions\CourseEnrollmentException;
use Modules\Course\Models\Course;
use Modules\Employee\Models\Employee;

class EnrollEmployeeInCourseAction
{
    use AsAction;

    public function handle(Course $course, Employee $employee): object
    {
        return DB::transaction(function () use ($course, $employee) {
            // Check if course is available for enrollment
            if ($course->status !== Course::STATUS_PUBLISHED) {
                throw CourseEnrollmentException::courseNotAvailable();
            }

            // Check if course has capacity
            if ($course->max_participants) {
                $currentEnrollments = $course->enrolledEmployees()->count();
                if ($currentEnrollments >= $course->max_participants) {
                    throw CourseEnrollmentException::courseFull();
                }
            }

            // Check if employee is already enrolled
            if ($course->enrolledEmployees()->where('employee_id', $employee->id)->exists()) {
                throw CourseEnrollmentException::alreadyEnrolled();
            }

            // Enroll the employee
            $course->enrolledEmployees()->attach($employee->id, [
                'enrolled_at' => now(),
                'status' => 'enrolled',
            ]);

            // Fire event
            event(new EmployeeEnrolledInCourse($course, $employee));

            // Return enrollment data
            return (object) [
                'course_id' => $course->id,
                'employee_id' => $employee->id,
                'enrolled_at' => now(),
                'status' => 'enrolled',
            ];
        });
    }
}
