<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Student;
use App\Models\ClassSubject;

class GoalController extends Controller
{
    public function index()
    {
        return Goal::with(['student', 'classSubject'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,user_id',
            'class_subject_id' => 'required|exists:class_subjects,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'goal_type' => 'required|in:weekly,monthly,semester,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:not_started,in_progress,completed,failed,archived',
            'priority' => 'required|in:low,medium,high,critical',
            'is_private' => 'boolean'
        ]);

        return Goal::create($data);
    }

    public function show($id)
    {
        return Goal::with(['student', 'classSubject'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);
        $goal->update($request->all());
        return $goal;
    }

    public function destroy($id)
    {
        Goal::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
