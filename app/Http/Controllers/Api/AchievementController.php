<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Achievement;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $achievements = Achievement::all();
        return response()->json($achievements);
    }

    // Lấy thành tựu theo id
    public function show($id)
    {
        $achievement = Achievement::find($id);

        if (!$achievement) {
            return response()->json(['message' => 'Achievement not found'], 404);
        }

        return response()->json($achievement);
    }

    // Tạo thành tựu mới
//     public function store(Request $request)
// {
//     // \Log::info('Store Achievement called', $request->all());

//     $validated = $request->validate([
//         'student_id' => 'required|integer',
//         'class_subject_id' => 'required|integer',
//         'title' => 'required|string|max:255',
//         'description' => 'nullable|string',
//         'file_url' => 'nullable|string',
//         'semester' => 'required|in:1,2,3,4,5,6',
//         'achievement_date' => 'required|date',
//     ]);

//     $achievement = Achievement::create($validated);

//     if (!$achievement) {
//         Log::error('Failed to create achievement');
//         return response()->json(['message' => 'Failed to create achievement'], 500);
//     }

//     Log::info('Achievement created:', $achievement->toArray());

//     return response()->json($achievement, 200);
// }
     public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'file_url' => 'required|image|max:5120',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'class_subject_id' => 'required|integer',
            'achievement_date' => 'required|date',
            'semester' => 'required|integer'
        ]);

        // Lưu ảnh
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('achievements', 'public');
        } else {
            return response()->json(['message' => 'No file uploaded.'], 400);
        }
        $achievement = Achievement::create([
            // 'student_id' => auth()->id(),
            'student_id' => $request->student_id,
            'file_url' => $path,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'class_subject_id' => $validated['class_subject_id'],
            'achievement_date' => $validated['achievement_date'],
            'semester' => $validated['semester'],
            
        ]);

        return response()->json([
            'message' => 'Achievement uploaded successfully!',
            'data' => $achievement
        ], 201);
    }

    // Cập nhật thành tựu
    public function update(Request $request, $id)
    {
        $achievement = Achievement::find($id);

        if (!$achievement) {
            return response()->json(['message' => 'Achievement not found'], 404);
        }

        $validated = $request->validate([
            'student_id' => 'sometimes|integer',
            'class_subject_id' => 'sometimes|integer',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'file_url' => 'nullable|string',
            'semester' => 'sometimes|in:1,2,3,4,5,6',
            'achievement_date' => 'sometimes|date',
        ]);

        $achievement->update($validated);

        return response()->json(["achiement" => $achievement]);
    }

    // Xóa thành tựu
    public function destroy($id)
    {
        $achievement = Achievement::find($id);

        if (!$achievement) {
            return response()->json(['message' => 'Achievement not found'], 404);
        }

        $achievement->delete();

        return response()->json(['message' => 'Achievement deleted']);
    }
}
