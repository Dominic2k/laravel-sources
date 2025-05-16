<?php

// app/Http/Controllers/Api/TeacherTagController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeacherTag;
use Illuminate\Http\Request;

class TeacherTagController extends Controller
{
    public function index()
    {
        return TeacherTag::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,user_id',
            'tagged_by' => 'required|exists:students,user_id',
            'entity_type' => 'required|in:journal,self_study_plan,in_class_plan,goal',
            'entity_id' => 'required|integer',
            'message' => 'required|string|max:1000',
        ]);

        $tag = TeacherTag::create($validated);
        return response()->json($tag, 201);
    }

    public function show($id)
    {
        $tag = TeacherTag::findOrFail($id);
        return response()->json($tag);
    }

    public function update(Request $request, $id)
    {
        $tag = TeacherTag::findOrFail($id);
        $tag->update($request->only(['message', 'resolved_at']));
        return response()->json($tag);
    }

    public function destroy($id)
    {
        $tag = TeacherTag::findOrFail($id);
        $tag->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function resolve($id)
    {
        $tag = TeacherTag::findOrFail($id);
        $tag->resolved_at = now();
        $tag->save();
        return response()->json(['message' => 'Resolved']);
    }
}
