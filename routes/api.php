<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\StudentClassController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InClassPlanController;
use App\Http\Controllers\Api\AchievementController;

// Public API resources
Route::apiResource('goals', GoalController::class);
Route::apiResource('classes', ClassController::class)->only(['index', 'show']);
Route::apiResource('subjects', SubjectController::class)->only(['index', 'show']);
Route::apiResource('students', StudentController::class)->only(['index', 'show']);
Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);
Route::apiResource('users', UserController::class)->only(['index', 'show']);
Route::apiResource('class-subjects', ClassSubjectController::class)->only(['index', 'show']);

// Public API to get student classes
Route::get('/public/student/{id}/classes', function ($id) {
    try {
        $student = \App\Models\Student::where('user_id', $id)->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        
        $classes = \App\Models\ClassStudent::where('student_id', $id)
            ->join('classes', 'class_students.class_id', '=', 'classes.id')
            ->select('classes.*')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Auth routes
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

// Các routes cần authentication
Route::middleware('auth:sanctum')->group(function () {
    // Student classes endpoint
    Route::get('/student/classes', [StudentClassController::class, 'getClasses']);
    
    // Protected API resources
    Route::apiResource('classes', ClassController::class)->except(['index', 'show']);
    Route::apiResource('subjects', SubjectController::class)->except(['index', 'show']);
    Route::apiResource('students', StudentController::class)->except(['index', 'show']);
    Route::apiResource('teachers', TeacherController::class)->except(['index', 'show']);
    Route::apiResource('users', UserController::class)->except(['index', 'show']);
    Route::apiResource('class-subjects', ClassSubjectController::class)->except(['index', 'show']);
});

// API to get student classes by user ID (without authentication)
Route::get('/student/{user_id}/classes', function ($userId) {
    try {
        // Kiểm tra xem student có tồn tại không
        $student = \App\Models\Student::where('user_id', $userId)->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        
        // Lấy danh sách lớp học với thông tin liên quan
        $classes = \App\Models\ClassStudent::where('student_id', $userId)
            ->join('classes', 'class_students.class_id', '=', 'classes.id')
            ->join('class_subjects', 'classes.id', '=', 'class_subjects.class_id')
            ->join('subjects', 'class_subjects.subject_id', '=', 'subjects.id')
            ->join('teachers', 'class_subjects.teacher_id', '=', 'teachers.user_id')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->select([
                'classes.id as class_id',
                'classes.class_name',
                'classes.status as class_status',
                'subjects.id as subject_id',
                'subjects.subject_name',
                'class_subjects.id as class_subject_id',
                'class_subjects.status as subject_status',
                'class_subjects.room',
                'class_subjects.schedule_info',
                'users.id as teacher_id',
                'users.full_name as teacher_name',
            ])
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// API to get student subjects by user ID
Route::get('/student/{user_id}/subjects', [App\Http\Controllers\Api\StudentController::class, 'getSubjects']);

// API to get goals by student and class subject
Route::get('/student/{student_id}/subject/{class_subject_id}/goals', [App\Http\Controllers\Api\GoalController::class, 'getGoalsBySubject']);

// API to get goal detail
Route::get('/student/{student_id}/goal/{goal_id}', [App\Http\Controllers\Api\GoalController::class, 'getGoalDetail']);

// API to create a new goal for a subject
Route::post('/student/{student_id}/subject/{class_subject_id}/goals', [App\Http\Controllers\Api\GoalController::class, 'createGoalForSubject']);

// API to update a goal
Route::put('/student/{student_id}/goal/{goal_id}', [App\Http\Controllers\Api\GoalController::class, 'updateGoal']);

// API to delete a goal
Route::delete('/student/{student_id}/goal/{goal_id}', [App\Http\Controllers\Api\GoalController::class, 'deleteGoal']);

////
// Lấy thông tin profile của sinh viên
Route::get('/students/{id}/profile', [StudentController::class, 'getProfile']);

// Cập nhật thông tin profile của sinh viên
Route::put('/students/{id}/profile', [StudentController::class, 'updateProfile']);


Route::apiResource('in-class-plans', InClassPlanController::class);

Route::apiResource('achievements', AchievementController::class);








