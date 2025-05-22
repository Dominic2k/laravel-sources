<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
  use Illuminate\Support\Facades\DB;

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


public function getTeachers()
{
    $teachers = DB::table('teachers')
        ->join('users', 'teachers.user_id', '=', 'users.id')
        ->where('users.role', 'teacher')
        ->select('teachers.user_id as id', 'users.full_name')
        ->orderBy('users.full_name')
        ->get();

    return response()->json($teachers);
}

}