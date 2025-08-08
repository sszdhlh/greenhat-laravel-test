<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Actions\Auth\ReadEmployeeAuthUser;

Route::get('/employee/user', ReadEmployeeAuthUser::class);
