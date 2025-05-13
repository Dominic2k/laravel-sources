<?php

namespace App\Services;

use App\Repositories\GoalRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GoalService
{
    protected $goalRepository;

    /**
     * Khởi tạo service với repository
     *
     * @param GoalRepository $goalRepository
     */
    public function __construct(GoalRepository $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    /**
     * Lấy danh sách goals theo sinh viên và môn học
     *
     * @param int $studentId
     * @param int $classSubjectId
     * @return array
     * @throws \Exception
     */
    public function getGoalsBySubject($studentId, $classSubjectId)
    {
        // Kiểm tra sinh viên tồn tại
        $student = $this->goalRepository->findStudent($studentId);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }

        // Kiểm tra môn học tồn tại
        $classSubject = $this->goalRepository->findClassSubject($classSubjectId);
        if (!$classSubject) {
            throw new \Exception('Class subject not found', 404);
        }

        // Lấy danh sách goals
        $goals = $this->goalRepository->getByStudentAndSubject($studentId, $classSubjectId);
        
        return [
            'success' => true,
            'data' => $goals
        ];
    }

    /**
     * Lấy chi tiết goal
     *
     * @param int $studentId
     * @param int $goalId
     * @return array
     * @throws \Exception
     */
    public function getGoalDetail($studentId, $goalId)
    {
        // Kiểm tra sinh viên tồn tại
        $student = $this->goalRepository->findStudent($studentId);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }

        // Lấy chi tiết goal
        $goal = $this->goalRepository->getDetail($goalId, $studentId);
        if (!$goal) {
            throw new \Exception('Goal not found or not authorized', 404);
        }

        return [
            'success' => true,
            'data' => $goal
        ];
    }

    /**
     * Tạo goal mới
     *
     * @param Request $request
     * @param int $studentId
     * @param int $classSubjectId
     * @return array
     * @throws \Exception
     */
    public function createGoal(Request $request, $studentId, $classSubjectId)
    {
        // Kiểm tra sinh viên tồn tại
        $student = $this->goalRepository->findStudent($studentId);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }

        // Kiểm tra môn học tồn tại
        $classSubject = $this->goalRepository->findClassSubject($classSubjectId);
        if (!$classSubject) {
            throw new \Exception('Class subject not found', 404);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'goal_type' => 'required|in:weekly,monthly,semester,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:not_started,in_progress,completed,failed,archived',
            'priority' => 'required|in:low,medium,high,critical',
            'is_private' => 'boolean'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Chuẩn bị dữ liệu
        $data = $validator->validated();
        $data['student_id'] = $studentId;
        $data['class_subject_id'] = $classSubjectId;

        // Tạo goal mới
        $goal = $this->goalRepository->create($data);

        return [
            'success' => true,
            'data' => $goal
        ];
    }

    /**
     * Cập nhật goal
     *
     * @param Request $request
     * @param int $studentId
     * @param int $goalId
     * @return array
     * @throws \Exception
     */
    public function updateGoal(Request $request, $studentId, $goalId)
    {
        // Kiểm tra sinh viên tồn tại
        $student = $this->goalRepository->findStudent($studentId);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }

        // Kiểm tra goal tồn tại và thuộc về sinh viên
        $goal = $this->goalRepository->findGoal($goalId, $studentId);
        if (!$goal) {
            throw new \Exception('Goal not found or not authorized', 404);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'goal_type' => 'sometimes|in:weekly,monthly,semester,custom',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'sometimes|in:not_started,in_progress,completed,failed,archived',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'is_private' => 'boolean'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Cập nhật goal
        $goal = $this->goalRepository->update($goal, $validator->validated());

        return [
            'success' => true,
            'data' => $goal
        ];
    }

    /**
     * Xóa goal
     *
     * @param int $studentId
     * @param int $goalId
     * @return array
     * @throws \Exception
     */
    public function deleteGoal($studentId, $goalId)
    {
        // Kiểm tra sinh viên tồn tại
        $student = $this->goalRepository->findStudent($studentId);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }

        // Kiểm tra goal tồn tại và thuộc về sinh viên
        $goal = $this->goalRepository->findGoal($goalId, $studentId);
        if (!$goal) {
            throw new \Exception('Goal not found or not authorized', 404);
        }

        // Xóa goal
        $this->goalRepository->delete($goal);

        return [
            'success' => true,
            'message' => 'Goal deleted successfully'
        ];
    }
}