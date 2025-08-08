<?php

declare(strict_types=1);

namespace Modules\Employee\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Employee\Actions\RegisterEmployeeAction;
use Modules\Employee\Data\EmployeeRegistrationData;

class EmployeeRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $data = EmployeeRegistrationData::from($request->all());
            
            $employee = RegisterEmployeeAction::run($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Employee registered successfully',
                'data' => [
                    'employee' => [
                        'id' => $employee->id,
                        'first_name' => $employee->first_name,
                        'last_name' => $employee->last_name,
                        'email' => $employee->email,
                        'department' => $employee->department,
                        'position' => $employee->position,
                        'start_date' => $employee->start_date->format('Y-m-d'),
                    ]
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
