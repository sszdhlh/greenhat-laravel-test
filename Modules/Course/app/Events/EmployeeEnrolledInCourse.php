<?php

namespace Modules\Course\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Course\Models\Course;
use Modules\Employee\Models\Employee;

class EmployeeEnrolledInCourse
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Course $course,
        public Employee $employee
    ) {}
}
