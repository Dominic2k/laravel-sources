<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::all();
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:100',
            'semester' => 'required|in:1,2,3,4,5,6',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planning,ongoing,completed'
        ]);
        
        $class = Classes::create($validated);
        return response()->json([
            'success' => true,
            'data' => $class
        ], 201);
    }
    
    public function show($id)
    {
        $class = Classes::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);
        
        $validated = $request->validate([
            'class_name' => 'sometimes|string|max:100',
            'semester' => 'sometimes|in:1,2,3,4,5,6',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'sometimes|in:planning,ongoing,completed'
        ]);
        
        $class->update($validated);
        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }
    
    public function destroy($id)
    {
        $class = Classes::findOrFail($id);
        $class->delete();
        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully'
        ]);
    }

    public function getStudents($id)
    {
        $class = Classes::with('students.user')->find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $class->students
        ]);
    }
}
