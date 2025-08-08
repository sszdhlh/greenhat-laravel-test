<?php

namespace Modules\Employee\Data;

use Spatie\LaravelData\Data;

class EmployeeRegistrationData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $role,
        public ?string $highest_qualification = null,
        public ?float $desired_salary = null,
        public ?string $note = null,
        public string $code = 'ACME',
    ) {}

    public static function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'string', 'max:255'],
            'highest_qualification' => ['nullable', 'string', 'max:255'],
            'desired_salary' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'code' => ['required', 'string', 'in:ACME'],
        ];
    }

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
