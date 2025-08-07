<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Actions\Employee\RegisterEmployeeAction;

/*
|--------------------------------------------------------------------------
| Employee Registration Public API Routes
|--------------------------------------------------------------------------
|
| Here are the public API routes for employee registration functionality.
| These routes are available without authentication.
|
*/

Route::post('/employee-registration', RegisterEmployeeAction::class)
    ->name('employee.registration');
