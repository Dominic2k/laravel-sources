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
    TeacherManagementController
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
    // Route::apiResource('classes', ClassController::class)->only(['index', 'show']);
    // Route::apiResource('subjects', SubjectController::class)->only(['index', 'show']);
    // Route::apiResource('students', StudentController::class)->only(['index', 'show']);
    Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);
    // Route::apiResource('users', UserController::class)->only(['index', 'show']);
    // Route::apiResource('class-subjects', ClassSubjectController::class)->only(['index', 'show']);
    // Route::apiResource('self-study-plans', SelfStudyPlanController::class);
    // Route::get('/public/teachers', [TeacherTagController::class, 'getTeachers']);
Route::prefix('public')->group(function () {
    Route::apiResource('teachers', TeacherController::class)->only(['index', 'show']);
});

    // Route::apiResource('users', UserController::class)->only(['index', 'show']);
    // Route::apiResource('class-subjects', ClassSubjectController::class)->only(['index', 'show']);
    // Route::apiResource('self-study-plans', SelfStudyPlanController::class);
    // Route::get('/public/teachers', [TeacherTagController::class, 'getTeachers']);


    // Danh sách lớp học của sinh viên theo user_id
    // Route::get('student/{user_id}/classes', function ($user_id) {
    //     $student = \App\Models\Student::where('user_id', $user_id)->first();
    //     if (!$student) return response()->json(['error' => 'Student not found'], 404);
    // Danh sách lớp học của sinh viên theo user_id
    // Route::get('student/{user_id}/classes', function ($user_id) {
    //     $student = \App\Models\Student::where('user_id', $user_id)->first();
    //     if (!$student) return response()->json(['error' => 'Student not found'], 404);

    //     $classes = \App\Models\ClassStudent::where('student_id', $student->id)
    //         ->join('classes', 'class_students.class_id', '=', 'classes.id')
    //         ->select('classes.*')
    //         ->get();
    //     $classes = \App\Models\ClassStudent::where('student_id', $student->id)
    //         ->join('classes', 'class_students.class_id', '=', 'classes.id')
    //         ->select('classes.*')
    //         ->get();

    //     return response()->json(['success' => true, 'data' => $classes]);
    // });
});
    //     return response()->json(['success' => true, 'data' => $classes]);
    // });


// --- In-class plans ---
// Route::apiResource('in-class-plans', InClassPlanController::class);

// API mở rộng: lọc theo class_name
// Route::get('self-study-plans/goal/{goalId}', [SelfStudyPlanController::class, 'filterByClass']);

// --- Student Goals (Public) ---
// Route::prefix('student/{student_id}')
//     ->controller(GoalController::class)
//     ->group(function () {
//         Route::get('subject/{class_subject_id}/goals', 'getGoalsBySubject');
//         Route::get('goal/{goal_id}', 'getGoalDetail');
//         Route::post('subject/{class_subject_id}/goals', 'createGoalForSubject');
//         Route::put('goal/{goal_id}', 'updateGoal');
//         Route::delete('goal/{goal_id}', 'deleteGoal');
//     });

// --- Student Subjects ---
// Route::get('/student/{user_id}/subjects', [StudentController::class, 'getSubjects']);


// --- Teacher tag: Ơn ---
Route::apiResource('teacher-tags', TeacherTagController::class);    

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('student')->group(function () {
        Route::get('/subjects', [StudentSubjectController::class, 'index']);
        Route::get('/subjects/{subjectId}/detail', [StudentSubjectController::class, 'show']);
        Route::get('/classes', [StudentClassController::class, 'index']);
        Route::get('/class-details', [StudentClassController::class, 'classDetails']);
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
});

Route::middleware('auth:sanctum')->get('/admin/activity-logs', [\App\Http\Controllers\Api\Admin\ActivityLogController::class, 'index']);
});
    // Route::get('/student/profile', function (Request $request) {
    //     $user = Auth::guard('sanctum')->user();
    //     $student = $user->student;

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'user' => $user,
    //             'student' => $student
    //         ]
    //     ]);
    // });

    // Route::put('/student/profile', function (Request $request) {
    //     $user = Auth::guard('sanctum')->user();
    //     $student = $user->student;

    //     $validated = $request->validate([
    //         'full_name' => 'sometimes|string',
    //         'email' => 'sometimes|email|unique:users,email,' . $user->id,
    //         'password' => 'sometimes|string|min:6',
    //         'student_code' => 'sometimes|string|unique:students,student_code,' . $user->id . ',user_id',
    //         'admission_date' => 'sometimes|date',
    //         'current_semester' => 'sometimes|integer|min:1|max:6',
    //     ]);

    //     if (isset($validated['full_name'])) {
    //         $user->full_name = $validated['full_name'];
    //     }
    //     if (isset($validated['email'])) {
    //         $user->email = $validated['email'];
    //     }
    //     if (isset($validated['password'])) {
    //         $user->password = bcrypt($validated['password']);
    //     }

    //     if (isset($validated['student_code'])) {
    //         $student->student_code = $validated['student_code'];
    //     }
    //     if (isset($validated['admission_date'])) {
    //         $student->admission_date = $validated['admission_date'];
    //     }
    //     if (isset($validated['current_semester'])) {
    //         $student->current_semester = $validated['current_semester'];
    //     }

    //     $user->save();
    //     $student->save();

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'user' => $user,
    //             'student' => $student
    //         ]
    //     ]);
    // });
    Route::apiResource('achievements', AchievementController::class);



    // --- Goal-based Plans ---
    // Route::prefix('student/goals/{goalId}')->group(function () {
    //     Route::get('in-class-plans', [InClassPlanController::class, 'filterByGoal']);
    //     Route::get('self-study-plans', [SelfStudyPlanController::class, 'filterByGoal']);
    // });

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







