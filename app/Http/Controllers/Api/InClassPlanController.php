<?php

namespace App\Http\Controllers\Api;

use App\Models\InClassPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InClassPlanController extends Controller
{
    // GET /api/in-class-plans
    public function index(Request $request)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plans = InClassPlan::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($plans);
    }

    // POST /api/in-class-plans
    public function store(Request $request)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([   
            'goal_id' => 'nullable|integer|exists:goals,id',
            'date' => 'required|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'self_assessment' => 'required|in:1,2,3',
            'difficulties_faced' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
            'problem_solved' => 'required|boolean'
        ]);

        $validated['student_id'] = $student->id;
        $plan = InClassPlan::create($validated);

        if ($plan->goal_id) {
            $plan->load('goal.student', 'goal.classSubject');
        }

        return response()->json([
            'success' => true,
            'data' => $plan
        ], 201);
    }

    // GET /api/in-class-plans/{id}
    public function show(Request $request, $id)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = InClassPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        return response()->json($plan);
    }

    // PUT/PATCH /api/in-class-plans/{id}
    public function update(Request $request, $id)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'goal_id' => 'nullable|integer|exists:goals,id',
            'date' => 'required|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'self_assessment' => 'required|in:1,2,3',
            'difficulties_faced' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
            'problem_solved' => 'required|boolean'
        ]);

        $plan = InClassPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        $plan->update($validated);

        return response()->json($plan);
    }

    // DELETE /api/in-class-plans/{id}
    public function destroy(Request $request, $id)
    {
        $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = InClassPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        $plan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

