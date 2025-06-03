<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    // Lấy tất cả comment cho một entity cụ thể
    public function index($id)
    {
        $feedbacks = Feedback::where('entity_id', $id)
            ->with('teacher:id,full_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $feedbacks
        ]);
    }

    public function store(Request $request)
    {
        // Log request data
        Log::info('Feedback request data:', $request->all());

        try {
            $validated = $request->validate([
                'entity_type' => 'required|string|in:goal,self_study_plan,in_class_plan,journal',
                'entity_id' => 'required|integer',
                'field_name' => 'required|string',
                'content' => 'required|string|max:1000',
            ]);

            $feedback = Feedback::create([
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id,
                'field_name' => $request->field_name,
                'teacher_id' => Auth::id(),
                'content' => $request->content,
            ]);

            return response()->json([
                'success' => true,
                'data' => $feedback
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::error('Feedback validation errors:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Xoá comment (nếu cần)
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        
        // Kiểm tra xem người dùng có quyền xóa feedback không
        if ($feedback->teacher_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this feedback'
            ], 403);
        }

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully'
        ]);
    }
}
