<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SelfStudyPlan;
use Illuminate\Support\Facades\Auth;

class SelfStudyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
            'goal_id' => 'nullable|exists:goals,id',
            'date' => 'required|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'time_allocation' => 'required|integer|min:1',
            'learning_resources' => 'nullable|string',
            'learning_activities' => 'nullable|string',
            'concentration_level' => 'required|integer|min:1|max:5',
            'plan_follow_reflection' => 'required|string',
            'work_evaluation' => 'nullable|string',
            'reinforcing_techniques' => 'nullable|string',
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
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'time_allocation' => 'required|integer|min:1',
            'learning_resources' => 'nullable|string',
            'learning_activities' => 'nullable|string',
            'concentration_level' => 'required|integer|min:1|max:5',
            'plan_follow_reflection' => 'required|string',
            'work_evaluation' => 'nullable|string',
            'reinforcing_techniques' => 'nullable|string',
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
    public function getPlansBySubject(Request $request, $classSubjectId)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Kiểm tra xem student có thuộc class_subject này không
        $classSubject = \App\Models\ClassSubject::where('id', $classSubjectId)
            ->whereHas('class.students', function($query) use ($student) {
                $query->where('student_id', $student->user_id);
            })
            ->first();

        if (!$classSubject) {
            return response()->json(['error' => 'Subject not found or you are not enrolled in this subject'], 404);
        }

        // Lấy tất cả kế hoạch của student cho môn học này
        $plans = SelfStudyPlan::where('student_id', $student->id)
            ->where(function($query) use ($classSubjectId) {
                // Lấy kế hoạch có goal liên quan đến môn học
                $query->whereHas('goal', function($q) use ($classSubjectId) {
                    $q->where('class_subject_id', $classSubjectId);
                })
                // Hoặc kế hoạch không có goal (goal_id là null)
                ->orWhereNull('goal_id');
            })
            ->with(['goal'])
            ->orderBy('created_at', 'desc')
            ->get();

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
