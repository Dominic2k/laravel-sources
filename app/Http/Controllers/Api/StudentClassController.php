<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassStudent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Classes;
use App\Models\ClassSubject;

class StudentClassController extends Controller
{
    public function getClasses(Request $request, $userId = null)
    {
        // Nếu có userId từ public route thì sử dụng, ngược lại lấy từ token
        $user = $userId ? User::find($userId) : Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) return response()->json(['error' => 'Student not found'], 404);

        $classes = ClassStudent::where('student_id', $student->id)
            ->join('classes', 'class_students.class_id', '=', 'classes.id')
            ->select('classes.*')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    }

    public function getClassDetails(Request $request, $userId = null)
    {
        // Nếu có userId từ public route thì sử dụng, ngược lại lấy từ token
        $user = $userId ? User::find($userId) : Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) return response()->json(['error' => 'Student not found'], 404);

        $classes = ClassStudent::where('student_id', $student->id)
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
    }
}
