<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    GoalController,
    StudentClassController,
    ClassController,
    SubjectController,
    TeacherController,
    ClassSubjectController,
    StudentController,
    UserController,
    InClassPlanController,
    SelfStudyPlanController,
    AuthController
};
use Illuminate\Support\Facades\Auth;

// --- Auth ---
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, "logout"])->middleware("logout");
Route::post("register", [AuthController::class, "register"])->middleware("admin-account");

Route::get("/student", [UserController::class, 'show'])->middleware("student-account");

// --- Public APIs ---
Route::prefix('public')->group(function () {
    Route::apiResource('classes', ClassController::class)->only(['index', 'show']);
    Route::apiResource('subjects', SubjectController::class)->only(['index', 'show']);
    Route::apiResource('students', StudentController::class)->only(['index', 'show']);
    Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);
    Route::apiResource('users', UserController::class)->only(['index', 'show']);
    Route::apiResource('class-subjects', ClassSubjectController::class)->only(['index', 'show']);
    Route::apiResource('self-study-plans', SelfStudyPlanController::class);

    // Danh sách lớp học của sinh viên theo user_id
    Route::get('student/{user_id}/classes', function ($user_id) {
        $student = \App\Models\Student::where('user_id', $user_id)->first();
        if (!$student) return response()->json(['error' => 'Student not found'], 404);

        $classes = \App\Models\ClassStudent::where('student_id', $student->id)
            ->join('classes', 'class_students.class_id', '=', 'classes.id')
            ->select('classes.*')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    });
});

// --- In-class plans ---
Route::apiResource('in-class-plans', InClassPlanController::class);

// API mở rộng: lọc theo class_name
Route::get('self-study-plans/goal/{goalId}', [SelfStudyPlanController::class, 'filterByClass']);

// --- Student Goals (Public) ---
Route::prefix('student/{student_id}')
    ->controller(GoalController::class)
    ->group(function () {
        Route::get('subject/{class_subject_id}/goals', 'getGoalsBySubject');
        Route::get('goal/{goal_id}', 'getGoalDetail');
        Route::post('subject/{class_subject_id}/goals', 'createGoalForSubject');
        Route::put('goal/{goal_id}', 'updateGoal');
        Route::delete('goal/{goal_id}', 'deleteGoal');
    });

// --- Student Subjects ---
Route::get('/student/{user_id}/subjects', [StudentController::class, 'getSubjects']);

// --- Authenticated routes ---
Route::middleware('auth:sanctum')->group(function () {
    // --- Student Classes ---
    Route::get('/student/classes', [StudentClassController::class, 'getClasses']);
    Route::get('/student/class-details', [StudentClassController::class, 'getClassDetails']);

    // --- Student Subjects ---
    Route::get('/student/subjects', [StudentController::class, 'getSubjects']);
    Route::get('/student/subjects/{subjectId}/detail', [StudentController::class, 'getSubjectDetail']);

    // --- Student Goals ---
    Route::get('/student/subjects/{classSubjectId}/goals', [GoalController::class, 'getGoalsBySubject']);
    Route::get('/student/goals/{goalId}', [GoalController::class, 'getGoalDetail']);
    Route::post('/student/subjects/{classSubjectId}/goals', [GoalController::class, 'createGoalForSubject']);

    Route::get('/student/goals/{goalId}', [GoalController::class, 'getGoalDetail']);
    Route::put('/student/goals/{goalId}', [GoalController::class, 'updateGoal']);
    Route::delete('/student/goals/{goalId}', [GoalController::class, 'deleteGoal']);


    // --- Subject-based Plans ---
    // Route::prefix('student/subjects/{classSubjectId}')->group(function () {
    //     // In Class Plans
    //     Route::get('in-class-plans', [InClassPlanController::class, 'getPlansBySubject']);
    //     Route::post('in-class-plans', [InClassPlanController::class, 'store']);
    //     Route::get('in-class-plans/{id}', [InClassPlanController::class, 'show']);
    //     Route::put('in-class-plans/{id}', [InClassPlanController::class, 'update']);
    //     Route::delete('in-class-plans/{id}', [InClassPlanController::class, 'destroy']);

    //     // Self Study Plans
    //     Route::get('self-study-plans', [SelfStudyPlanController::class, 'getPlansBySubject']);
    //     Route::post('self-study-plans', [SelfStudyPlanController::class, 'store']);
    //     Route::get('self-study-plans/{id}', [SelfStudyPlanController::class, 'show']);
    //     Route::put('self-study-plans/{id}', [SelfStudyPlanController::class, 'update']);
    //     Route::delete('self-study-plans/{id}', [SelfStudyPlanController::class, 'destroy']);
    // });

        Route::prefix('student/subjects/{subjectId}')->group(function () {
            Route::get('in-class-plans', [InClassPlanController::class, 'indexBySubject']);
            Route::post('in-class-plans', [InClassPlanController::class, 'store']);
            Route::get('in-class-plans/{id}', [InClassPlanController::class, 'show']);
            Route::put('in-class-plans/{id}', [InClassPlanController::class, 'update']);
            Route::delete('in-class-plans/{id}', [InClassPlanController::class, 'destroy']);

            Route::get('self-study-plans', [SelfStudyPlanController::class, 'getPlansBySubject']);
            Route::post('self-study-plans', [SelfStudyPlanController::class, 'store']);
            Route::get('self-study-plans/{id}', [SelfStudyPlanController::class, 'show']);
            Route::put('self-study-plans/{id}', [SelfStudyPlanController::class, 'update']);
            Route::delete('self-study-plans/{id}', [SelfStudyPlanController::class, 'destroy']);
    });

    // --- Goal-based Plans ---
    Route::prefix('student/goals/{goalId}')->group(function () {
        Route::get('in-class-plans', [InClassPlanController::class, 'filterByGoal']);
        Route::get('self-study-plans', [SelfStudyPlanController::class, 'filterByGoal']);
    });

    // --- Profile ---
    Route::get('/student/profile', [StudentController::class, 'getProfile']);
    Route::put('/student/profile', [StudentController::class, 'updateProfile']);    
});