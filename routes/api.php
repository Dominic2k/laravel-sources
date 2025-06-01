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
    AchievementController,
    AuthController,
    TeacherTagController,
    StudentSubjectController,
    DeadlineController,
    TeacherManagementController,
    FeedbackController
};
use App\Http\Controllers\Api\Admin\SubjectManagementController;
use App\Models\InClassPlan;
use Illuminate\Support\Facades\Auth;

// --- Authenticated routes - Binh ---
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, "logout"])->middleware("logout");
Route::post("register", [AuthController::class, "register"])->middleware("admin-account");


// --- Public APIs ---
Route::prefix('public')->group(function () {

    Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);

Route::prefix('public')->group(function () {
    Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);
});
});

// --- Teacher tag: Ơn ---
Route::apiResource('teacher-tags', TeacherTagController::class);    

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('student')->group(function () {
        Route::get('/subjects', [StudentSubjectController::class, 'index']);
        Route::get('/subjects/{subjectId}/detail', [StudentSubjectController::class, 'show']);
        Route::get('/classes', [StudentClassController::class, 'index']);
        Route::get('/class-details', [StudentClassController::class, 'classDetails']);

        // Deadline notifications
        Route::get('/deadlines', [App\Http\Controllers\Api\Student\StudentDeadlineController::class, 'getMyDeadlines']);
        Route::get('/deadlines/upcoming', [App\Http\Controllers\Api\Student\StudentDeadlineController::class, 'getUpcomingDeadlines']);
        Route::get('/deadlines/{id}', [App\Http\Controllers\Api\Student\StudentDeadlineController::class, 'getDeadlineDetails']);
    });

    // --- Goals ---
    Route::get('/student/subjects/{classSubjectId}/goals', [GoalController::class, 'getGoalsBySubject']);
    Route::get('/student/goals/{goalId}', [GoalController::class, 'getGoalDetail']);
    Route::post('/student/subjects/{classSubjectId}/goals', [GoalController::class, 'createGoalForSubject']);

    Route::get('/student/goals/{goalId}', [GoalController::class, 'getGoalDetail']);
    Route::put('/student/goals/{goalId}', [GoalController::class, 'updateGoal']);
    Route::delete('/student/goals/{goalId}', [GoalController::class, 'deleteGoal']);


    Route::prefix('student/subject/{subjectId}')->group(function () {
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

// --- Achievements - Ngoc ---
    Route::apiResource('achievements', AchievementController::class);

// --- Student Subjects ---
    Route::get('/student/{student_id}/subjects', [StudentController::class, 'getSubjects']);

    // --- Profile - Ngoc ---

    Route::get('/student/profile', [StudentController::class, 'getProfile']);
    Route::put('/student/profile', [StudentController::class, 'updateProfile']);
});

// --- Admin Routes - Dat ---
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard Statistics
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [App\Http\Controllers\Api\Admin\DashboardController::class, 'getStatistics']);
        }); 
    // Quản lý sinh viên
    Route::apiResource('students', App\Http\Controllers\Api\Admin\StudentManagementController::class);
    
    // Quản lý lớp học
    Route::apiResource('classes', App\Http\Controllers\Api\Admin\ClassManagementController::class);
    
    // Quản lý sinh viên trong lớp
    Route::get('classes/{class}/students', [App\Http\Controllers\Api\Admin\ClassManagementController::class, 'getStudents']);
    Route::post('classes/{class}/students', [App\Http\Controllers\Api\Admin\ClassManagementController::class, 'addStudent']);
    Route::delete('classes/{class}/students/{student}', [App\Http\Controllers\Api\Admin\ClassManagementController::class, 'removeStudent']);
    
    // Tạo nhiều sinh viên cho lớp
    Route::post('classes/{class}/create-students', [App\Http\Controllers\Api\Admin\ClassManagementController::class, 'createStudentsForClass']);

    // Teacher Management Routes
    Route::get('/teachers', [\App\Http\Controllers\Api\Admin\TeacherManagementController::class, 'index']);
    Route::post('/teachers', [\App\Http\Controllers\Api\Admin\TeacherManagementController::class, 'store']);
    Route::get('/teachers/{id}', [\App\Http\Controllers\Api\Admin\TeacherManagementController::class, 'show']);
    Route::put('/teachers/{id}', [\App\Http\Controllers\Api\Admin\TeacherManagementController::class, 'update']);
    Route::delete('/teachers/{id}', [\App\Http\Controllers\Api\Admin\TeacherManagementController::class, 'destroy']);

    Route::apiResource('subjects', SubjectManagementController::class);

    // Class Subject Management
    Route::prefix('class-subjects')->group(function () {
        Route::get('/assignment-data', [App\Http\Controllers\Api\Admin\ClassSubjectManagementController::class, 'getAssignmentData']);
        Route::post('/assign', [App\Http\Controllers\Api\Admin\ClassSubjectManagementController::class, 'handleAssignment']);
    });
});

Route::middleware('auth:sanctum')->get('/admin/activity-logs', [\App\Http\Controllers\Api\Admin\ActivityLogController::class, 'index']);

    
Route::apiResource('achievements', AchievementController::class);

    // --- Profile ---
      
// Route::apiResource('notifications', NotificationController::class);

// ---Class of Teacher - Kim---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teacher/{teacherId}/classes', [TeacherController::class, 'getClasses']);
    Route::get('/classes/{classId}/students', [ClassController::class, 'getStudents']);
});


// --Teacher set deadline--
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/deadlines', [DeadlineController::class, 'index']);
    Route::post('/classes/{classId}/deadlines', [DeadlineController::class, 'store']);
    Route::get('/deadlines/{id}', [DeadlineController::class, 'show']);
    Route::put('/deadlines/{id}', [DeadlineController::class, 'update']);
    Route::delete('/deadlines/{id}', [DeadlineController::class, 'destroy']);
});

// --Get name of teacher in sidebar UI teacher--
Route::middleware(['auth:sanctum'])->get('/teacher/{id}', [TeacherController::class, 'show']);
    Route::get('/teachers/student-profile/{studentId}', [TeacherController::class, 'viewStudentProfile']);
    Route::get('/teachers/students/{studentId}/plans', [TeacherController::class, 'getStudentPlans']);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students/{id}/feedbacks', [FeedbackController::class, 'index']);
    Route::post('/feedbacks', [FeedbackController::class, 'store']);
});







