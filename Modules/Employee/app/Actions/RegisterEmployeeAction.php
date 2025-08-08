<?php

namespace Modules\Employee\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Data\EmployeeRegistrationData;
use Modules\Employee\Events\EmployeeRegistered;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class RegisterEmployeeAction
{
    use AsAction;

    public function handle(EmployeeRegistrationData $data): Employee
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'name' => $data->getFullName(),
                'email' => $data->email,
                'password' => Hash::make(Str::random(16)), // Auto-generated password
            ]);

            // Create employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'role' => $data->role,
                'highest_qualification' => $data->highest_qualification,
                'desired_salary' => $data->desired_salary,
                'note' => $data->note,
            ]);

            // Fire event for email notification
            event(new EmployeeRegistered($employee));

            return $employee->load('user');
        });
    }
}
