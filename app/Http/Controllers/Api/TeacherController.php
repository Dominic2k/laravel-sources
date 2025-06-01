<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Classes;
use App\Models\InClassPlan;
use App\Models\SelfStudyPlan;

use Illuminate\Support\Facades\Auth;


class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->get();
        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string',
            'join_date' => 'required|date',
            'bio' => 'nullable|string'
        ]);
        
        $teacher = Teacher::create($validated);
        $teacher->load('user');
        
        return response()->json([
            'success' => true,
            'data' => $teacher
        ], 201);
    }
    
    public function show($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        
        $validated = $request->validate([
            'specialization' => 'sometimes|string',
            'join_date' => 'sometimes|date',
            'bio' => 'nullable|string'
        ]);
        
        $teacher->update($validated);
        $teacher->load('user');
        
        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }
    
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ]);
    }

        public function getClasses($teacherId)
    {
        // Lấy danh sách class_id từ bảng class_subjects
        $classIds = DB::table('class_subjects')
                    ->where('teacher_id', $teacherId)
                    ->pluck('class_id')
                    ->unique();

        if ($classIds->isEmpty()) {
            return response()->json(['data' => []]);
        }

        // Truy vấn danh sách lớp và đếm học sinh
        $classes = Classes::whereIn('id', $classIds)
                    ->withCount('students')
                    ->get();

        // Định dạng dữ liệu cho frontend
        $formatted = $classes->map(function ($class) {
            return [
                'class_id' => $class->id,
                'class_name' => $class->class_name,
                'student_count' => $class->students_count
            ];
        });

        return response()->json([
            'data' => $formatted
        ]);
    }

  public function viewStudentProfile($studentId)
{
    $student = User::with([
        'goals' => function ($query) {
            $query->whereHas('ClassSubject', function ($q) {
                $q->where('teacher_id', Auth::guard("sanctum")->user()->id);
            });
        },
        'goals.ClassSubject'
    ])->find($studentId);

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    if ($student->role !== 'student') {
        return response()->json(['message' => 'This user is not a student'], 403);
    }

    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->full_name,
            'email' => $student->email,
            'class' => $student->goals
        ],
        'goals' => $student->goals
    ]);
}

public function getStudentPlans($studentId)
{
    $inClassPlans = InClassPlan::where('student_id', $studentId)
        ->orderBy('date', 'desc')
        ->get();

    $selfStudyPlans = SelfStudyPlan::where('student_id', $studentId)
        ->orderBy('date', 'desc')
        ->get();

    return response()->json([
        'in_class_plans' => $inClassPlans,
        'self_study_plans' => $selfStudyPlans
    ]);
}
}