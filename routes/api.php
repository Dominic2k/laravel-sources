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
    TeacherTagController
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

    // Lấy chi tiết lớp học kèm môn học và giáo viên
    // Route::get('student/{user_id}/class-details', function ($user_id) {
    //     $student = \App\Models\Student::where('user_id', $user_id)->first();
    //     if (!$student) return response()->json(['error' => 'Student not found'], 404);

    //     $classes = \App\Models\ClassStudent::where('student_id', $student->id)
    //         ->join('classes', 'class_students.class_id', '=', 'classes.id')
    //         ->join('class_subjects', 'classes.id', '=', 'class_subjects.class_id')
    //         ->join('subjects', 'class_subjects.subject_id', '=', 'subjects.id')
    //         ->join('teachers', 'class_subjects.teacher_id', '=', 'teachers.user_id')
    //         ->join('users', 'teachers.user_id', '=', 'users.id')
    //         ->select([
    //             'classes.id as class_id',
    //             'classes.class_name',
    //             'classes.status as class_status',
    //             'subjects.id as subject_id',
    //             'subjects.subject_name',
    //             'class_subjects.id as class_subject_id',
    //             'class_subjects.status as subject_status',
    //             'class_subjects.room',
    //             'class_subjects.schedule_info',
    //             'users.id as teacher_id',
    //             'users.full_name as teacher_name',
    //         ])
    //         ->get();

    //     return response()->json(['success' => true, 'data' => $classes]);
    // });
});




// --- Authenticated APIs ---
// Đưa route lấy lớp học của student vào auth, lấy user từ token
// --- Public: student profile ---

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
    Route::get('/student/classes', function (Request $request) {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) return response()->json(['error' => 'Student not found'], 404);

        $classes = \App\Models\ClassStudent::where('student_id', $student->id)
            ->join('classes', 'class_students.class_id', '=', 'classes.id')
            ->select('classes.*')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    });

    Route::get('/student/class-details', function (Request $request) {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
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

    // Lấy danh sách môn học của sinh viên đã đăng nhập
    Route::get('/student/subjects', function (Request $request) {
        try {
            $user = Auth::guard('sanctum')->user();
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            $subjects = \App\Models\ClassStudent::where('class_students.student_id', $student->user_id)
                ->join('classes', 'class_students.class_id', '=', 'classes.id')
                ->join('class_subjects', 'classes.id', '=', 'class_subjects.class_id')
                ->join('subjects', 'class_subjects.subject_id', '=', 'subjects.id')
                ->join('teachers', 'class_subjects.teacher_id', '=', 'teachers.user_id')
                ->join('users', 'teachers.user_id', '=', 'users.id')
                ->select([
                    'subjects.id as subject_id',
                    'subjects.subject_name',
                    'subjects.description',
                    'class_subjects.id as class_subject_id',
                    'class_subjects.status as subject_status',
                    'class_subjects.room',
                    'class_subjects.schedule_info',
                    'users.id as teacher_id',
                    'users.full_name as teacher_name',
                ])
                ->distinct()
                ->get();
            return response()->json(['success' => true, 'data' => $subjects]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    // Lấy thông tin chi tiết của một subject
    Route::get('/student/subjects/{subjectId}/detail', function (Request $request, $subjectId) {
        try {
            $user = Auth::guard('sanctum')->user();
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            $subject = \App\Models\ClassStudent::where('class_students.student_id', $student->user_id)
                ->join('classes', 'class_students.class_id', '=', 'classes.id')
                ->join('class_subjects', 'classes.id', '=', 'class_subjects.class_id')
                ->join('subjects', 'class_subjects.subject_id', '=', 'subjects.id')
                ->join('teachers', 'class_subjects.teacher_id', '=', 'teachers.user_id')
                ->join('users', 'teachers.user_id', '=', 'users.id')
                ->where('subjects.id', $subjectId)
                ->select([
                    'subjects.id as subject_id',
                    'subjects.subject_name',
                    'subjects.description',
                    'class_subjects.id as class_subject_id',
                    'class_subjects.status as subject_status',
                    'class_subjects.room',
                    'class_subjects.schedule_info',
                    'users.id as teacher_id',
                    'users.full_name as teacher_name',
                ])
                ->first();

            if (!$subject) {
                return response()->json(['error' => 'Subject not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $subject]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    });
    
    // --- Goals ---
    Route::get('/student/subjects/{classSubjectId}/goals', [GoalController::class, 'getGoalsBySubject']);
    Route::get('/student/goals/{goalId}', [GoalController::class, 'getGoalDetail']);
    Route::post('/student/subjects/{classSubjectId}/goals', [GoalController::class, 'createGoalForSubject']);

    // --- Self Study Plans ---
    Route::get('/student/self-study-plans', [SelfStudyPlanController::class, 'index']);
    Route::post('/student/self-study-plans', [SelfStudyPlanController::class, 'store']);
    Route::get('/student/self-study-plans/{id}', [SelfStudyPlanController::class, 'show']);
    Route::put('/student/self-study-plans/{id}', [SelfStudyPlanController::class, 'update']);
    Route::delete('/student/self-study-plans/{id}', [SelfStudyPlanController::class, 'destroy']);
    Route::get('/student/goals/{goalId}/self-study-plans', [SelfStudyPlanController::class, 'filterByGoal']);

    // --- In Class Plans ---
    Route::get('/student/in-class-plans', [InClassPlanController::class, 'index']);
    Route::post('/student/in-class-plans', [InClassPlanController::class, 'store']);
    Route::get('/student/in-class-plans/{id}', [InClassPlanController::class, 'show']);
    Route::put('/student/in-class-plans/{id}', [InClassPlanController::class, 'update']);
    Route::delete('/student/in-class-plans/{id}', [InClassPlanController::class, 'destroy']);


// --- Achievements ---
Route::apiResource('achievements', AchievementController::class);

// --- Student Subjects ---
Route::get('/student/{student_id}/subjects', [StudentController::class, 'getSubjects']);

    // --- Profile ---
    Route::get('/student/profile', function (Request $request) {
        $user = Auth::guard('sanctum')->user();
        $student = $user->student;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'student' => $student
            ]
        ]);
    });

    Route::put('/student/profile', function (Request $request) {
        $user = Auth::guard('sanctum')->user();
        $student = $user->student;

        $validated = $request->validate([
            'full_name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'student_code' => 'sometimes|string|unique:students,student_code,' . $user->id . ',user_id',
            'admission_date' => 'sometimes|date',
            'current_semester' => 'sometimes|integer|min:1|max:6',
        ]);

        if (isset($validated['full_name'])) {
            $user->full_name = $validated['full_name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (isset($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        if (isset($validated['student_code'])) {
            $student->student_code = $validated['student_code'];
        }
        if (isset($validated['admission_date'])) {
            $student->admission_date = $validated['admission_date'];
        }
        if (isset($validated['current_semester'])) {
            $student->current_semester = $validated['current_semester'];
        }

        $user->save();
        $student->save();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'student' => $student
            ]
        ]);
    });
});


//Tag teacher route
Route::apiResource('teacher-tags', TeacherTagController::class);
Route::put('teacher-tags/{id}/resolve', [TeacherTagController::class, 'resolve']);
