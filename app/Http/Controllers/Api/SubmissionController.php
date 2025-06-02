<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;



class SubmissionController extends Controller
{
    // Lấy danh sách tất cả submissions
    public function index()
    {
        $submissions = Submission::with(['student', 'deadline'])->get();
        return response()->json($submissions);
    }

    // Tạo submission mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'deadline_id' => 'required|exists:deadlines,id',
            'status' => 'required|in:' . implode(',', [
                Submission::STATUS_MISSED,
                Submission::STATUS_LATE,
                Submission::STATUS_PROCESS,
                Submission::STATUS_DONE,
            ]),
            'submitted_day' => 'nullable|date',
        ]);

        $submission = Submission::create($validated);

        return response()->json($submission, 201);
    }

    // Lấy thông tin chi tiết một submission
    public function show($id)
    {
        $submission = Submission::with(['student', 'deadline'])->findOrFail($id);
        return response()->json($submission);
    }

    // Cập nhật submission
    public function update(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'sometimes|exists:users,id',
            'deadline_id' => 'sometimes|exists:deadlines,id',
            'status' => 'sometimes|in:' . implode(',', [
                Submission::STATUS_MISSED,
                Submission::STATUS_LATE,
                Submission::STATUS_PROCESS,
                Submission::STATUS_DONE,
            ]),
            'submitted_day' => 'nullable|date',
        ]);

        $submission->update($validated);

        return response()->json($submission);
    }

    // Xoá submission
    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);
        $submission->delete();

        return response()->json(['message' => 'Submission deleted successfully']);
    }


    public function dasboardByTeeacher () {

        $teacher_id = Auth::guard("sanctum")->user()->id;
        $students = User::whereHas('submissions.deadline', function ($query) use ($teacher_id) {
            $query->where('set_by', $teacher_id);
        })->with(['submissions' => function ($q) use ($teacher_id) {
            $q->whereHas('deadline', function ($d) use ($teacher_id) {
            $d->where('set_by', $teacher_id);
            })->with('deadline'); // Optional: load cả deadline info nếu cần
        }])->get();

        return response()->json(["data" => $students]);
    }

}
