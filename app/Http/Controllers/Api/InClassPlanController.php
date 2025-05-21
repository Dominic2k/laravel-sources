<?php

namespace App\Http\Controllers\Api;

use App\Models\InClassPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class InClassPlanController extends Controller
{
    // Lấy danh sách kế hoạch theo subject
    public function indexBySubject($subjectId)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plans = InClassPlan::where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    // Thêm kế hoạch mới cho subject
    public function store(Request $request, $subjectId)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'skills_module' => 'required|string|max:255',
            'lesson_summary' => 'required|string',
            'self_assessment' => 'required|in:1,2,3',
            'difficulties_faced' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
            'problem_solved' => 'required|boolean'
        ]);

        $validated['student_id'] = $student->id;
        $validated['subject_id'] = $subjectId;

        $plan = InClassPlan::create($validated);

        return response()->json([
            'success' => true,
            'data' => $plan
        ], 201);
    }

    // Lấy chi tiết kế hoạch
    public function show($subjectId, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = InClassPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    // Cập nhật kế hoạch
    public function update(Request $request, $subjectId, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $validated = $request->validate([
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
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    // Xoá kế hoạch
    public function destroy(Request $request, $subjectId, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = InClassPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
