<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Classes;

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

    public function getClasses($user_id)
{
    // Lấy danh sách class_id từ bảng class_subjects
    $classIds = DB::table('class_subjects')
                  ->where('teacher_id', $user_id)
                  ->pluck('class_id')
                  ->unique();

    if ($classIds->isEmpty()) {
        return response()->json(['data' => []]);
    }

    // Lấy danh sách lớp và đếm số học sinh trong từng lớp
    $classes = Classes::whereIn('id', $classIds)
                ->withCount('students')
                ->get();

    // Định dạng lại để frontend sử dụng được
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

}