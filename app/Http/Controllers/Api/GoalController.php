<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Student;
use App\Models\ClassSubject;

class GoalController extends Controller
{
    public function index()
    {
        return Goal::with(['student', 'classSubject'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,user_id',
            'class_subject_id' => 'required|exists:class_subjects,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'goal_type' => 'required|in:weekly,monthly,semester,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:not_started,in_progress,completed,failed,archived',
            'priority' => 'required|in:low,medium,high,critical',
            'is_private' => 'boolean'
        ]);

        return Goal::create($data);
    }

    public function show($id)
    {
        return Goal::with(['student', 'classSubject'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);
        $goal->update($request->all());
        return $goal;
    }

    public function destroy($id)
    {
        Goal::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Get goals by student and class subject
     *
     * @param int $studentId
     * @param int $classSubjectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoalsBySubject($studentId, $classSubjectId)
    {
        try {
            // Kiểm tra xem student có tồn tại không
            $student = \App\Models\Student::where('user_id', $studentId)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            // Kiểm tra xem class_subject có tồn tại không
            $classSubject = \App\Models\ClassSubject::find($classSubjectId);
            
            if (!$classSubject) {
                return response()->json(['error' => 'Class subject not found'], 404);
            }
            
            // Lấy danh sách goals của môn học đó
            $goals = Goal::where('student_id', $studentId)
                ->where('class_subject_id', $classSubjectId)
                ->with(['student', 'classSubject'])
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $goals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get goal detail
     *
     * @param int $studentId
     * @param int $goalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoalDetail($studentId, $goalId)
    {
        try {
            // Kiểm tra xem student có tồn tại không
            $student = \App\Models\Student::where('user_id', $studentId)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            // Lấy chi tiết goal
            $goal = Goal::where('id', $goalId)
                ->where('student_id', $studentId)
                ->with(['student', 'classSubject.subject', 'classSubject.class', 'classSubject.teacher.user'])
                ->first();
                
            if (!$goal) {
                return response()->json(['error' => 'Goal not found or not authorized'], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $goal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Create a new goal for a subject
     *
     * @param \Illuminate\Http\Request $request
     * @param int $studentId
     * @param int $classSubjectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGoalForSubject(Request $request, $studentId, $classSubjectId)
    {
        try {
            // Kiểm tra xem student có tồn tại không
            $student = \App\Models\Student::where('user_id', $studentId)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            // Kiểm tra xem class_subject có tồn tại không
            $classSubject = \App\Models\ClassSubject::find($classSubjectId);
            
            if (!$classSubject) {
                return response()->json(['error' => 'Class subject not found'], 404);
            }
            
            // Validate dữ liệu đầu vào
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
                'goal_type' => 'required|in:weekly,monthly,semester,custom',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|in:not_started,in_progress,completed,failed,archived',
                'priority' => 'required|in:low,medium,high,critical',
                'is_private' => 'boolean'
            ]);
            
            // Thêm student_id và class_subject_id vào dữ liệu
            $data['student_id'] = $studentId;
            $data['class_subject_id'] = $classSubjectId;
            
            // Tạo goal mới
            $goal = Goal::create($data);
            $goal->load(['student', 'classSubject']);
            
            return response()->json([
                'success' => true,
                'data' => $goal
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Update a goal
     *
     * @param \Illuminate\Http\Request $request
     * @param int $studentId
     * @param int $goalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGoal(Request $request, $studentId, $goalId)
    {
        try {
            // Kiểm tra xem student có tồn tại không
            $student = \App\Models\Student::where('user_id', $studentId)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            // Lấy goal cần cập nhật
            $goal = Goal::where('id', $goalId)
                ->where('student_id', $studentId)
                ->first();
                
            if (!$goal) {
                return response()->json(['error' => 'Goal not found or not authorized'], 404);
            }
            
            // Validate dữ liệu đầu vào
            $data = $request->validate([
                'title' => 'sometimes|string',
                'description' => 'nullable|string',
                'goal_type' => 'sometimes|in:weekly,monthly,semester,custom',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
                'status' => 'sometimes|in:not_started,in_progress,completed,failed,archived',
                'priority' => 'sometimes|in:low,medium,high,critical',
                'is_private' => 'boolean'
            ]);
            
            // Cập nhật goal
            $goal->update($data);
            $goal->load(['student', 'classSubject']);
            
            return response()->json([
                'success' => true,
                'data' => $goal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Delete a goal
     *
     * @param int $studentId
     * @param int $goalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGoal($studentId, $goalId)
    {
        try {
            // Kiểm tra xem student có tồn tại không
            $student = \App\Models\Student::where('user_id', $studentId)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            // Lấy goal cần xóa
            $goal = Goal::where('id', $goalId)
                ->where('student_id', $studentId)
                ->first();
                
            if (!$goal) {
                return response()->json(['error' => 'Goal not found or not authorized'], 404);
            }
            
            // Xóa goal
            $goal->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Goal deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}





