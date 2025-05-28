<?php

namespace App\Http\Controllers\Api;

use App\Models\SelfStudyPlan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Subject;

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
    public function store(Request $request, $subjectId)
    {

    $user = Auth::guard('sanctum')->user();
    $student = Student::where('user_id', $user->id)->first();

    if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
    // Kiểm tra subject có tồn tại không
    

        $validated = $request->validate([
            'date' => 'required|date',
            'lesson' => 'required|string|max:255',
            'time' => 'required|string',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|string',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string'
        ]);

        $validated['subject_id'] = $subjectId;

        $plan = SelfStudyPlan::create($validated);

        return response()->json(['message' => 'Saved successfully', 'data' => $plan], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $student = Student::where('user_id', $request->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = SelfStudyPlan::where('id', $id)
            ->where('student_id', $student->id)
            ->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        return response()->json($plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $subjectId, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();
        
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
            'reinforcing' => 'nullable|string'
        ]);

        $plan = SelfStudyPlan::where('id', $id)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($subjectId, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $plan = SelfStudyPlan::where('id', $id)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

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
}


