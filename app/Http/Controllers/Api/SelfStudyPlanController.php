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
    public function index()
    {
        $plans = SelfStudyPlan::orderBy('date', 'desc')->get();
        return response()->json($plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'date' => 'required|date',
            'lesson' => 'required|string',
            'time' => 'required|string',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|string',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string',
        ]);

        $plan = SelfStudyPlan::create($validated);

        return response()->json(['message' => 'Saved successfully', 'data' => $plan], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $plan = SelfStudyPlan::findOrFail($id);
        return response()->json($plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'lesson' => 'required|string',
            'time' => 'required|string',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|string',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string',
        ]);

        $plan = SelfStudyPlan::findOrFail($id);
        $plan->update($validated);

        return response()->json(['message' => 'Updated successfully', 'data' => $plan]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plan = SelfStudyPlan::findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function filterByGoal($goalId)
    {
        $plans = SelfStudyPlan::where('goal_id', $goalId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($plans);
    }
}
