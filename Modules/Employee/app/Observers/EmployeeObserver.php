<?php

namespace Modules\Employee\Observers;

use Modules\Employee\Jobs\SendEmployeeRegistrationEmail;
use Modules\Employee\Models\Employee;
use Modules\User\Actions\User\AssignRoleToUser;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        // Dispatch the email job when an employee is created
        SendEmployeeRegistrationEmail::dispatch($employee);
    }

    public function saved(Employee $model): void
    {
        if ($model->user?->isNotA('employee')) {
            AssignRoleToUser::make()->handle($model->user, 'employee');
        }
    }
}
