<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompletedLessonController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\UserProgressController;
use App\Models\CompletedLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);


    Route::middleware('admin')->group(function () {
        Route::apiResource('courses', CourseController::class)->only('store', 'update', 'destroy');

        Route::post("courses/{course}/sets", [SetController::class, 'store']);
        Route::delete("courses/{courseSlug}/sets/{setId}", [SetController::class, 'destroy']);
        Route::post('lessons', [LessonController::class, 'store']);
        Route::delete('lessons/{id}', [LessonController::class, 'destroy']);
    });
    Route::apiResource('courses', CourseController::class)->only('index', 'show');

    Route::post('lessons/{lesson_id}/contents/{content_id}/check', [LessonController::class, 'checkAnswer']);
    Route::put('lessons/{lesson_id}/complete', [CompletedLessonController::class, 'store']);
    Route::post('courses/{course_slug}/register', [CourseController::class, 'registerCourse']);
    Route::get('users/progress', [UserProgressController::class, 'index']);
});
