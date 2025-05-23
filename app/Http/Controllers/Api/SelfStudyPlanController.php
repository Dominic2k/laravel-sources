<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SelfStudyPlan;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Subject;

class SelfStudyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $student = Student::where('user_id', $request->user()->id)->first();
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

        return response()->json($plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $subjectId)
{
    // Kiểm tra subject có tồn tại không
    if (!Subject::where('id', $subjectId)->exists()) {
            return response()->json(['error' => 'Subject not found'], 404);
        }

        $validated = $request->validate([
            'subject_id' => 'required|integer',
            'date' => 'required|date',
            'module' => 'required|string|max:255',
            'lesson' => 'required|string',
            'time' => 'required|integer',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|integer',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string',
            'notes' => 'nullable|string'
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
    public function update(Request $request, string $id)
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

        $validated = $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'module' => 'required|string|max:255',
            'lesson' => 'required|string',
            'time' => 'required|integer',
            'resources' => 'nullable|string',
            'activities' => 'nullable|string',
            'concentration' => 'required|string',
            'plan_follow' => 'required|string',
            'evaluation' => 'nullable|string',
            'reinforcing' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

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

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }


    // public function storeBySubject(Request $request, $subjectId)
    //     {
    //         $student = Student::where('user_id', $request->user()->id)->first();
    //         if (!$student) {
    //             return response()->json(['error' => 'Student not found'], 404);
    //         }

    //         $validated = $request->validate([
    //             'subject_id' =>'required|bigint|unsigned',
    //             'date' => 'required|date',
    //             'module' => 'nullable|string|max:255',
    //             'lesson' => 'required|string',
    //             'time' => 'required|string',
    //             'resources' => 'nullable|string',
    //             'activities' => 'nullable|string',
    //             'concentration' => 'required|string',
    //             'plan_follow' => 'required|string',
    //             'evaluation' => 'nullable|string',
    //             'reinforcing' => 'nullable|string',
    //             'notes' => 'nullable|string'
    //         ]);

    //         $validated['student_id'] = $student->id;
    //         $validated['subject_id'] = $subjectId;  // Gán subjectId từ URL

    //         $plan = SelfStudyPlan::create($validated);

    //         return response()->json(['message' => 'Saved successfully', 'data' => $plan], 201);
    //     }


    // Lọc kế hoạch theo subject_id
    public function filterBySubject(Request $request, $subjectId)
    {
        $plans = SelfStudyPlan::where('subject_id', $subjectId)
            ->orderBy('date', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $plans
            ]);
        }
}
