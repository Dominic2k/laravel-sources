<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'teacher_id' => 'required|exists:users,id',
            'field_name' => 'nullable|string',
            'content' => 'required|string',
        ]);

        $feedback = Feedback::create($validated);

        return response()->json([
            'message' => 'Feedback saved successfully.',
            'feedback' => $feedback
        ]);
    }

    // Lấy tất cả comment cho 1 mục cụ thể
    public function index(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $feedbacks = Feedback::where('entity_type', $validated['entity_type'])
            ->where('entity_id', $validated['entity_id'])
            ->get();

        return response()->json($feedbacks);
    }



    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);

        if ($feedback->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted']);
    }

public function getStudentFeedbacks($studentId)
{
    $feedbacks = Feedback::whereIn('entity_type', [
        'App\\Models\\Goal',
        'App\\Models\\InClassPlan',
        'App\\Models\\SelfStudyPlan',
    ])
    ->whereHas('entity', function ($query) use ($studentId) {
        $query->where('student_id', $studentId);
    })
    ->get();

    return response()->json($feedbacks);
}


}
