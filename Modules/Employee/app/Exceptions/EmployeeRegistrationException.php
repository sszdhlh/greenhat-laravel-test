<?php

namespace Modules\Employee\Exceptions;

use Exception;

class EmployeeRegistrationException extends Exception
{
    public static function invalidCode(): self
    {
        return new self('Invalid registration code provided.');
    }

    public static function emailAlreadyExists(): self
    {
        return new self('An account with this email already exists.');
    }
}
