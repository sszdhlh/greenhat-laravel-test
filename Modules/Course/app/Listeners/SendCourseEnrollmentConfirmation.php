<?php

namespace Modules\Course\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Course\Events\EmployeeEnrolledInCourse;
use Modules\Course\Notifications\CourseEnrollmentConfirmation;

class SendCourseEnrollmentConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(EmployeeEnrolledInCourse $event): void
    {
        // Send the confirmation notification
        $event->employee->user->notify(new CourseEnrollmentConfirmation($event->course, $event->employee));
    }
}
