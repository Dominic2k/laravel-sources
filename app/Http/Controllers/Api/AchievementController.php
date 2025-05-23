<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;


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

    public function store(Request $request) {
        $validated = $request->validate([
            'file_url' => 'required|image|max:10120',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'class_subject_id' => 'required|integer',
            'achievement_date' => 'required|date',
            'semester' => 'required|integer'
        ]);

        // Lưu ảnh
        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/achievements');
            $file->move($uploadPath, $fileName);
            $imageUrl = asset('uploads/achievements/' . $fileName);


        } else {
            return response()->json(['message' => 'No file uploaded.'], 400);
        }


        $achievement = Achievement::create([
            // 'student_id' => auth()->id(),
            'student_id' => Auth::guard('sanctum')->user()->id,
            'file_url' => $imageUrl,
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

    // Validate input
    $validated = $request->validate([
        'file_url' => 'nullable|image|max:10120',
        'title' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'class_subject_id' => 'sometimes|integer',
        'achievement_date' => 'sometimes|date',
        'semester' => 'sometimes|in:1,2,3,4,5,6',
    ]);

    // Nếu người dùng upload ảnh mới
    if ($request->hasFile('file_url')) {
        $file = $request->file('file_url');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $uploadPath = public_path('uploads/achievements');
        $file->move($uploadPath, $fileName);
        $imageUrl = asset('uploads/achievements/' . $fileName);

        $validated['file_url'] = $imageUrl;
    }
    $achievement->update($validated);

    return response()->json([
        'message' => 'Achievement updated successfully!',
        'data' => $achievement
    ]);
}
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
