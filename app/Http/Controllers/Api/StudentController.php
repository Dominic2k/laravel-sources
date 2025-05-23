<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user')->get();
        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'student_code' => 'required|string|unique:students',
            'admission_date' => 'required|date',
            'current_semester' => 'required|integer|min:1|max:6'
        ]);
        
        $student = Student::create($validated);
        $student->load('user');
        
        return response()->json([
            'success' => true,
            'data' => $student
        ], 201);
    }
    
    public function show($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $validated = $request->validate([
            'student_code' => 'sometimes|string|unique:students,student_code,' . $id . ',user_id',
            'admission_date' => 'sometimes|date',
            'current_semester' => 'sometimes|integer|min:1|max:6'
        ]);
        
        $student->update($validated);
        $student->load('user');
        
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }
    
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }

    public function getSubjects(Request $request)
    {
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
    }

    public function getSubjectDetail(Request $request, $subjectId)
    {
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
    }

    public function getProfile(Request $request)
    {
        try {
            $authUser = Auth::guard('sanctum')->user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $user = \App\Models\User::find($authUser->id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
    
            $student = $user->student;
            if (!$student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'student' => $student
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
{
    try {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $student = $user->student;
        if (!$student) {
            return response()->json(['error' => 'Student profile not found'], 404);
        }

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
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage()
        ], 500);
    }
} 

}
