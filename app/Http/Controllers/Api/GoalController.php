<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Goal;


class GoalController extends Controller
{
    protected $goalService;

    public function __construct(GoalService $goalService)
    {
        $this->goalService = $goalService;
    }

    private function getStudentFromRequest(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if (!$student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }
            return $student;
        } catch (\Exception $e) {
            Log::error('Error in getStudentFromRequest: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function getGoalsBySubject(Request $request, $classSubjectId)
    {
        try {
            $student = $this->getStudentFromRequest($request);
            if ($student instanceof \Illuminate\Http\JsonResponse) {
                return $student;
            }

            $goals = $this->goalService->getGoalsBySubject($student->user_id, $classSubjectId);
            return response()->json(['success' => true, 'data' => $goals]);
        } catch (\Exception $e) {
            Log::error('Error in getGoalsBySubject: ' . $e->getMessage());
            return $this->handleException($e);
        }
    }

    public function getGoalDetail(Request $request, $goalId)
    {
        try {
            $student = $this->getStudentFromRequest($request);
            if ($student instanceof \Illuminate\Http\JsonResponse) {
                return $student;
            }

            $goal = $this->goalService->getGoalDetail($student, $goalId);
            return response()->json(['success' => true, 'data' => $goal]);
        } catch (\Exception $e) {
            Log::error('Error in getGoalDetail: ' . $e->getMessage());
            return $this->handleException($e);
        }
    }

    public function createGoalForSubject(Request $request, $classSubjectId)
    {
        try {
            $student = $this->getStudentFromRequest($request);
            if ($student instanceof \Illuminate\Http\JsonResponse) {
                return $student;
            }

            $goal = $this->goalService->createGoalForSubject($student, $classSubjectId, $request->all());
            return response()->json(['success' => true, 'data' => $goal], 201);
        } catch (\Exception $e) {
            Log::error('Error in createGoalForSubject: ' . $e->getMessage());
            return $this->handleException($e);
        }
    }

    public function updateGoal(Request $request, $goalId)   
    {
        try {
            $student = $this->getStudentFromRequest($request);
            if ($student instanceof \Illuminate\Http\JsonResponse) {
                return $student;
            }

            Log::info('Student: ' . $student);

            $goal = $this->goalService->updateGoal($student, $goalId, $request->all());
            return response()->json(['success' => true, 'data' => $goal]);
        } catch (\Exception $e) {
            Log::error('Error in updateGoal: ' . $e->getMessage());
            return $this->handleException($e);
        }
    }

    public function deleteGoal(Request $request, $goalId)
    {
        try {
            $student = $this->getStudentFromRequest($request);
            if ($student instanceof \Illuminate\Http\JsonResponse) {
                return $student;
            }

            $result = $this->goalService->deleteGoal($student, $goalId);
            return response()->json(['success' => true, 'message' => 'Goal deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error in deleteGoal: ' . $e->getMessage());
            return $this->handleException($e);
        }
    }


    protected function handleException(\Exception $e)
    {
        Log::error('GoalController Error: ' . $e->getMessage());
        
        $statusCode = $e->getCode() ?: 500;
        if ($statusCode < 100 || $statusCode > 599) {
            $statusCode = 500;
        }

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], $statusCode);
    }
}
