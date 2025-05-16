<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SelfStudyPlan;

class SelfStudyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plans = SelfStudyPlan::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'goal_id' => 'nullable|exists:goals,id',
            'date' => 'required|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'time_allocation' => 'required|integer',
            'learning_resources' => 'nullable|string',
            'learning_activities' => 'nullable|string',
            'concentration_level' => 'required|integer',
            'plan_follow_reflection' => 'required|string',
            'work_evaluation' => 'nullable|string',
            'reinforcing_techniques' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $validated['student_id'] = $student->id;
        $plan = SelfStudyPlan::create($validated);

        return response()->json(['message' => 'Saved successfully', 'data' => $plan], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        return response()->json($plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'goal_id' => 'nullable|exists:goals,id',
            'date' => 'required|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'time_allocation' => 'required|integer',
            'learning_resources' => 'nullable|string',
            'learning_activities' => 'nullable|string',
            'concentration_level' => 'required|integer',
            'plan_follow_reflection' => 'required|string',
            'work_evaluation' => 'nullable|string',
            'reinforcing_techniques' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        $plan->update($validated);

        return response()->json(['message' => 'Updated successfully', 'data' => $plan]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        $plan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function filterByGoal(Request $request, $goalId)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plans = SelfStudyPlan::where('goal_id', $goalId)
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($plans);
    }
}
