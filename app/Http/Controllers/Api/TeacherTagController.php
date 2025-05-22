<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeacherTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TeacherTagController extends Controller
{
    // Lấy tất cả các tag, kèm tên giáo viên từ bảng users
   public function index(Request $request)
{
    $query = TeacherTag::with('teacherUser:id,full_name')
        ->orderBy('created_at', 'desc');

    // Nếu có entity_id và entity_type thì lọc
    if ($request->has('entity_id') && $request->has('entity_type')) {
        $query->where('entity_id', $request->input('entity_id'))
              ->where('entity_type', $request->input('entity_type'));
    }

    $tags = $query->get()->map(function ($tag) {
        return [
            'id' => $tag->id,
            'teacher_id' => $tag->teacher_id,
            'teacher_name' => $tag->teacherUser->full_name ?? 'N/A',
            'tagged_by' => $tag->tagged_by,
            'entity_type' => $tag->entity_type,
            'entity_id' => $tag->entity_id,
            'message' => $tag->message,
            'resolved_at' => $tag->resolved_at,
            'created_at' => $tag->created_at,
            'updated_at' => $tag->updated_at,
        ];
    });

    return response()->json($tags);
}


    // Tạo tag mới
    public function store(Request $request)
    {


        $validated = $request->validate([
            'teacher_id' => 'required|integer|exists:users,id', // sửa dòng này
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'message' => 'required|string|max:1000'
        ]);


        $validated['tagged_by'] = Auth::guard("sanctum")->user()->id; // Lấy ID của người tạo tag từ token]

        $tag = TeacherTag::create($validated);

        // Load tên giáo viên sau khi tạo
        $tag->load('teacherUser');

        return response()->json([
            'id' => $tag->id,
            'teacher_id' => $tag->teacher_id,
            'teacher_name' => $tag->teacherUser->full_name ?? 'N/A',
            'tagged_by' => $tag->tagged_by,
            'entity_type' => $tag->entity_type,
            'entity_id' => $tag->entity_id,
            'message' => $tag->message,
            'resolved_at' => $tag->resolved_at,
            'created_at' => $tag->created_at,
            'updated_at' => $tag->updated_at,
        ], 201);
    }

    public function show($id)
    {
        $tag = TeacherTag::with('teacherUser')->findOrFail($id);

        return response()->json([
            'id' => $tag->id,
            'teacher_id' => $tag->teacher_id,
            'teacher_name' => $tag->teacherUser->full_name ?? 'N/A',
            'tagged_by' => $tag->tagged_by,
            'entity_type' => $tag->entity_type,
            'entity_id' => $tag->entity_id,
            'message' => $tag->message,
            'resolved_at' => $tag->resolved_at,
            'created_at' => $tag->created_at,
            'updated_at' => $tag->updated_at,
        ]);
    }

    public function update(Request $request, $id)
    {
        $tag = TeacherTag::findOrFail($id);
        $tag->update($request->only(['message', 'resolved_at']));

        return response()->json($tag);
    }

    public function destroy($id)
    {
        $tag = TeacherTag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function resolve($id)
    {
        $tag = TeacherTag::findOrFail($id);
        $tag->resolved_at = now();
        $tag->save();

        return response()->json(['message' => 'Resolved']);
    }
}
