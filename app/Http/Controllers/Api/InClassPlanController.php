<?php

namespace App\Http\Controllers\Api;

use App\Models\InClassPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InClassPlanController extends Controller
{
    // GET /api/in-class-plans
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plans = InClassPlan::where('student_id', $student->id)
            ->with(['goal'])
            ->orderBy('date', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    // POST /api/in-class-plans
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
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
        $plan->load('goal');

        return response()->json([
            'success' => true,
            'data' => $plan
        ], 201);
    }

    // GET /api/in-class-plans/{id}
    public function show(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = InClassPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['goal'])
            ->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    // PUT/PATCH /api/in-class-plans/{id}
    public function update(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
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
        $plan->load('goal');

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    // DELETE /api/in-class-plans/{id}
    public function destroy(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = InClassPlan::where('id', $id)
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
            ->whereHas('class', function($query) use ($student) {
                $query->whereHas('students', function($q) use ($student) {
                    $q->where('student_id', $student->user_id);
                });
            })
            ->first();

        if (!$classSubject) {
            return response()->json(['error' => 'Subject not found or you are not enrolled in this subject'], 404);
        }

        // Lấy tất cả kế hoạch của student cho môn học này
        $plans = InClassPlan::where('student_id', $student->id)
            ->where('class_subject_id', $classSubjectId)
            ->with(['goal', 'classSubject'])
            ->orderBy('date', 'desc')
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

        $plans = InClassPlan::where('goal_id', $goalId)
            ->where('student_id', $student->id)
            ->with(['goal'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }
}

