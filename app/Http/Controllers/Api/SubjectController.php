<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return response()->json([
            'success' => true,
            'data' => $subjects
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $subject = Subject::create($validated);
        return response()->json([
            'success' => true,
            'data' => $subject
        ], 201);
    }
    
    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $subject
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        
        $validated = $request->validate([
            'subject_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $subject->update($validated);
        return response()->json([
            'success' => true,
            'data' => $subject
        ]);
    }
    
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully'
        ]);
    }
}
