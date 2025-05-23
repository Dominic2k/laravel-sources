<?php

namespace App\Http\Controllers\Api;

use App\Models\SelfStudyPlan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class SelfStudyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plans = SelfStudyPlan::where('student_id', $student->id)
            ->with(['goal'])
            ->orderBy('date', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'lesson' => 'required|string|max:255',
            'time' => 'required|string',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|string',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $validated['student_id'] = $student->id;
        $plan = SelfStudyPlan::create($validated);
        $plan->load('goal');

        return response()->json([
            'success' => true,
            'data' => $plan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['goal'])
            ->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'goal_id' => 'nullable|exists:goals,id',
            'date' => 'required|date',
            'lesson' => 'required|string|max:255',
            'time' => 'required|string',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|string',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        $plan->update($validated);
        $plan->load('goal');

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }

    /**
     * Get plans by subject
     */
    public function getPlansBySubject($subjectId)
    {
        $user = Auth::guard('sanctum')->user();

        // Nếu cần xác nhận là student vẫn giữ đoạn sau
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Vì bảng không có student_id nên không thể lọc theo đó
        $plans = SelfStudyPlan::where('subject_id', $subjectId)->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }


    /**
     * Filter plans by goal
     */
    public function filterByGoal(Request $request, $goalId)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Kiểm tra xem goal có thuộc về student này không
        $goal = \App\Models\Goal::where('id', $goalId)
            ->where('student_id', $student->user_id)
            ->first();

        if (!$goal) {
            return response()->json(['error' => 'Goal not found'], 404);
        }

        $plans = SelfStudyPlan::where('goal_id', $goalId)
            ->where('student_id', $student->id)
            ->with(['goal'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }
}


