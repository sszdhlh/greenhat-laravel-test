<?php

namespace Modules\Course\Exceptions;

use Exception;

class CourseEnrollmentException extends Exception
{
    public static function courseNotAvailable(): self
    {
        return new self('Course is not available for enrollment.');
    }

    public static function courseFull(): self
    {
        return new self('Course has reached maximum participant capacity.');
    }

    public static function alreadyEnrolled(): self
    {
        return new self('Employee is already enrolled in this course.');
    }
}
