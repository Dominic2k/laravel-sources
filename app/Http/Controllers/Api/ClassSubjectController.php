<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSubject;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $classSubjects = ClassSubject::with(['class', 'subject', 'teacher'])->get();
        return response()->json([
            'success' => true,
            'data' => $classSubjects
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,user_id',
            'schedule_info' => 'nullable|string',
            'room' => 'nullable|string|max:50',
            'status' => 'required|in:pending,active,completed'
        ]);
        
        $classSubject = ClassSubject::create($validated);
        $classSubject->load(['class', 'subject', 'teacher']);
        
        return response()->json([
            'success' => true,
            'data' => $classSubject
        ], 201);
    }
    
    public function show($id)
    {
        $classSubject = ClassSubject::with(['class', 'subject', 'teacher'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $classSubject
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $classSubject = ClassSubject::findOrFail($id);
        
        $validated = $request->validate([
            'teacher_id' => 'sometimes|exists:teachers,user_id',
            'schedule_info' => 'nullable|string',
            'room' => 'nullable|string|max:50',
            'status' => 'sometimes|in:pending,active,completed'
        ]);
        
        $classSubject->update($validated);
        $classSubject->load(['class', 'subject', 'teacher']);
        
        return response()->json([
            'success' => true,
            'data' => $classSubject
        ]);
    }
    
    public function destroy($id)
    {
        $classSubject = ClassSubject::findOrFail($id);
        $classSubject->delete();
        return response()->json([
            'success' => true,
            'message' => 'Class subject mapping deleted successfully'
        ]);
    }
}