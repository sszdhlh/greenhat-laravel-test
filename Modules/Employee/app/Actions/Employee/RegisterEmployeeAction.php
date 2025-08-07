<?php

namespace Modules\Employee\Actions\Employee;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Data\EmployeeRegistrationData;
use Modules\Employee\Events\EmployeeRegistered;
use Modules\Employee\Models\Employee;
use Modules\Employee\Transformers\EmployeeTransformer;
use Modules\User\Models\User;
use Spatie\Fractal\Fractal;

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

    public function asController(ActionRequest $request): Employee
    {
        $data = EmployeeRegistrationData::validateAndCreate($request->all());
        return $this->handle($data);
    }

    public function jsonResponse(Employee $employee): JsonResponse
    {
        $fractalData = fractal($employee, EmployeeTransformer::class)
            ->parseIncludes('user')
            ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Employee registration successful. A confirmation email has been sent.',
            'data' => $fractalData['data'] ?? $fractalData
        ], 201);
    }
}
