<?php

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
    AchievementController
};

// --- Public API ---
Route::prefix('public')->group(function () {
    Route::apiResource('goals', GoalController::class)->only(['index', 'show']);
    Route::apiResource('classes', ClassController::class)->only(['index', 'show']);
    Route::apiResource('subjects', SubjectController::class)->only(['index', 'show']);
    Route::apiResource('students', StudentController::class)->only(['index', 'show']);
    Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);
    Route::apiResource('users', UserController::class)->only(['index', 'show']);
    Route::apiResource('class-subjects', ClassSubjectController::class)->only(['index', 'show']);
    Route::apiResource('self-study-plans', SelfStudyPlanController::class);

    // Lấy danh sách lớp học cho student qua user_id
    Route::get('student/{user_id}/classes', function ($userId) {
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) return response()->json(['error' => 'Student not found'], 404);

        $classes = \App\Models\ClassStudent::where('student_id', $student->id)
            ->join('classes', 'class_students.class_id', '=', 'classes.id')
            ->select('classes.*')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    });

    // Lấy danh sách lớp học kèm thông tin môn, giáo viên
    Route::get('student/{user_id}/class-details', function ($userId) {
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) return response()->json(['error' => 'Student not found'], 404);

        $classes = \App\Models\ClassStudent::where('student_id', $student->id)
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

        return response()->json(['success' => true, 'data' => $classes]);
    });
});

// --- Public: student profile ---
Route::get('/students/{id}/profile', [StudentController::class, 'getProfile']);
Route::put('/students/{id}/profile', [StudentController::class, 'updateProfile']);

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
    Route::get('/student/classes', [StudentClassController::class, 'getClasses']);

    Route::apiResource('classes', ClassController::class)->except(['index', 'show']);
    Route::apiResource('subjects', SubjectController::class)->except(['index', 'show']);
    Route::apiResource('students', StudentController::class)->except(['index', 'show']);
    Route::apiResource('teachers', TeacherController::class)->except(['index', 'show']);
    Route::apiResource('users', UserController::class)->except(['index', 'show']);
    Route::apiResource('class-subjects', ClassSubjectController::class)->except(['index', 'show']);

    // Authenticated student-goal routes with ownership check
    Route::prefix('student/{student_id}')
        ->middleware('check.student.ownership')
        ->controller(GoalController::class)
        ->group(function () {
            Route::get('subject/{class_subject_id}/goals', 'getGoalsBySubject');
            Route::get('goal/{goal_id}', 'getGoalDetail');
            Route::post('subject/{class_subject_id}/goals', 'createGoalForSubject');
            Route::put('goal/{goal_id}', 'updateGoal');
            Route::delete('goal/{goal_id}', 'deleteGoal');
        });
});
Route::apiResource('achievements', AchievementController::class);








