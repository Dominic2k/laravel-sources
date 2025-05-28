<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\ClassStudent;

class StudentSubjectController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $student = Student::where('user_id', $user->id)->first();

            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            $subjects = ClassStudent::where('class_students.student_id', $student->user_id)
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
    }

    public function show(Request $request, $subjectId)
{
    try {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $subject = ClassStudent::where('class_students.student_id', $student->user_id)
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
}
}
