<?php

namespace App\Http\Controllers\Api;

use App\Models\InClassPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InClassPlanController extends Controller
{
    // GET /api/in-class-plans
    public function index()
    {
        return response()->json(InClassPlan::all(), 200);
    }

    // POST /api/in-class-plans
    public function store(Request $request)
    {
        $validated = $request->validate([
            'goal_id' => 'nullable|integer',
            'student_id' => 'nullable|integer',
            'class_subject_id' => 'nullable|integer',
            'date' => 'nullable|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'self_assessment' => 'required|in:1,2,3',
            'difficulties_faced' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
            'problem_solved' => 'required|boolean',
            'additional_notes' => 'nullable|string',
        ]);

        $plan = InClassPlan::create($validated);

        return response()->json($plan, 201);
    }

    // GET /api/in-class-plans/{id}
    public function show($id)
    {
        $plan = InClassPlan::findOrFail($id);
        return response()->json($plan);
    }

    // PUT/PATCH /api/in-class-plans/{id}
    public function update(Request $request, $id)
    {
        $plan = InClassPlan::findOrFail($id);
        $plan->update($request->all());

        return response()->json($plan);
    }

    // DELETE /api/in-class-plans/{id}
    public function destroy($id)
    {
        InClassPlan::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
