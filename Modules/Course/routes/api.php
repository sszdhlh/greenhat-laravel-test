<?php

use Illuminate\Support\Facades\Route;
use Modules\Course\Http\Controllers\Api\CourseController;
use Modules\Course\Http\Controllers\Api\CourseCategoryController;
use Modules\Course\Http\Controllers\Api\CourseEnrollmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Course Categories
    Route::apiResource('course-categories', CourseCategoryController::class);
    
    // Courses
    Route::apiResource('courses', CourseController::class);
    
    // Course Enrollment
    Route::prefix('courses/{course}')->group(function () {
        Route::post('enroll', [CourseEnrollmentController::class, 'enroll'])
            ->name('courses.enroll');
        Route::post('unenroll', [CourseEnrollmentController::class, 'unenroll'])
            ->name('courses.unenroll');
        Route::get('enrollments', [CourseEnrollmentController::class, 'enrollments'])
            ->name('courses.enrollments');
    });
});
