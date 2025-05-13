<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoalService;
use Illuminate\Validation\ValidationException;

class GoalController extends Controller
{
    protected $goalService;

    /**
     * Khởi tạo controller với service
     *
     * @param GoalService $goalService
     */
    public function __construct(GoalService $goalService)
    {
        $this->goalService = $goalService;
    }

    /**
     * Lấy danh sách goals theo sinh viên và môn học
     *
     * @param int $studentId
     * @param int $classSubjectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoalsBySubject($studentId, $classSubjectId)
    {
        try {
            $result = $this->goalService->getGoalsBySubject($studentId, $classSubjectId);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Lấy chi tiết goal
     *
     * @param int $studentId
     * @param int $goalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoalDetail($studentId, $goalId)
    {
        try {
            $result = $this->goalService->getGoalDetail($studentId, $goalId);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Tạo goal mới
     *
     * @param Request $request
     * @param int $studentId
     * @param int $classSubjectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGoalForSubject(Request $request, $studentId, $classSubjectId)
    {
        try {
            $result = $this->goalService->createGoal($request, $studentId, $classSubjectId);
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Cập nhật goal
     *
     * @param Request $request
     * @param int $studentId
     * @param int $goalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGoal(Request $request, $studentId, $goalId)
    {
        try {
            $result = $this->goalService->updateGoal($request, $studentId, $goalId);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Xóa goal
     *
     * @param int $studentId
     * @param int $goalId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGoal($studentId, $goalId)
    {
        try {
            $result = $this->goalService->deleteGoal($studentId, $goalId);
            return response()->json($result);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Xử lý exception
     *
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleException(\Exception $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        $statusCode = method_exists($e, 'getCode') && $e->getCode() >= 400 && $e->getCode() < 600 
            ? $e->getCode() 
            : 500;

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], $statusCode);
    }
}