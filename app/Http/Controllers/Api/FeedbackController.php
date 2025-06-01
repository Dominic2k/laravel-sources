<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // Lấy tất cả comment cho một entity cụ thể

public function index($id)
{
    $feedbacks = Feedback::where('entity_id', $id)->get();
    return response()->json($feedbacks);
}

public function store(Request $request)
{
    $request->validate([
        'entity_type' => 'required|string',
        'entity_id' => 'required|integer',
        'field_name' => 'required|string',
        'content' => 'required|string',
    ]);

    $feedback = Feedback::create([
        'entity_type' => $request->entity_type,
        'entity_id' => $request->entity_id,
        'field_name' => $request->field_name,
        'teacher_id' => Auth::id(),
        'content' => $request->content,
    ]);

    return response()->json($feedback, 201);
}


    // Xoá comment (nếu cần)
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
