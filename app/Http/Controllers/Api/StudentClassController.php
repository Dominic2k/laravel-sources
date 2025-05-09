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
    public function getClasses(Request $request)
    {
        // Get student_id from authenticated user or from request
        $studentId = Auth::id();
        
        // If user_id is provided in request and user is admin, use that instead
        if ($request->has('user_id') && Auth::user()->role === 'admin') {
            $studentId = $request->input('user_id');
        }
        
        // Get all classes the student is enrolled in with related information
        $classes = ClassStudent::where('student_id', $studentId)
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
                // You would need to add avatar field to users table if needed
            ])
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }
}
