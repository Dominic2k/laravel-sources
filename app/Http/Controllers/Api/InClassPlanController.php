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

    // Nếu cần xác nhận là student vẫn giữ đoạn sau
    $student = Student::where('user_id', $user->id)->first();
    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    // Vì bảng không có student_id nên không thể lọc theo đó
    $plans = InClassPlan::where('subject_id', $subjectId)->get();

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
        'problem_solved' => 'required|boolean',
        'additional_notes' => 'nullable|string'
    ]);

    $plan = InClassPlan::where('id', $id)
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
        return response()->json(['success' => false, 'message' => 'Student not found'], 404);
    }

    $plan = InClassPlan::where('id', $id)
        ->where('subject_id', $subjectId)
        ->first();

    if (!$plan) {
        return response()->json(['success' => false, 'message' => 'Plan not found or unauthorized'], 404);
    }

    $plan->delete();

    return response()->json([
        'success' => true,
        'message' => 'In-class plan deleted successfully.'
    ]);
}

}
