<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Student;
use App\Models\ClassSubject;
use App\Repositories\GoalRepository;

class GoalService
{
    protected $goalRepository;

    public function __construct(GoalRepository $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    public function getGoalsBySubject($studentId, $classSubjectId)
    {
        return $this->goalRepository->getByStudentAndSubject($studentId, $classSubjectId);
    }

    public function getGoalDetail(Student $student, $goalId)
    {
        $goal = $this->goalRepository->findGoal($goalId, $student->id);
        if (!$goal) {
            throw new \Exception('Goal not found', 404);
        }
        return $this->goalRepository->getDetail($goalId, $student->id);
    }

    public function createGoalForSubject(Student $student, $classSubjectId, array $data)
    {
        if (!$student || !$student->user_id) {
            throw new \Exception('Student not found', 404);
        }

        $classSubject = $this->goalRepository->findClassSubject($classSubjectId);
        if (!$classSubject) {
            throw new \Exception('Class subject not found', 404);
        }

        // Validate required fields
        $requiredFields = ['title', 'goal_type', 'start_date', 'end_date', 'priority'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Field {$field} is required", 422);
            }
        }

        // Validate goal_type
        $validGoalTypes = ['weekly', 'monthly', 'semester', 'custom'];
        if (!in_array($data['goal_type'], $validGoalTypes)) {
            throw new \Exception('Invalid goal type. Must be one of: ' . implode(', ', $validGoalTypes), 422);
        }

        // Validate priority
        $validPriorities = ['low', 'medium', 'high', 'critical'];
        if (!in_array($data['priority'], $validPriorities)) {
            throw new \Exception('Invalid priority. Must be one of: ' . implode(', ', $validPriorities), 422);
        }

        // Validate dates
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            throw new \Exception('Start date must be before end date', 422);
        }

        // Prepare data
        $goalData = [
            'student_id' => $student->user_id,
            'class_subject_id' => $classSubjectId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'goal_type' => $data['goal_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'priority' => $data['priority'],
            'is_private' => $data['is_private'] ?? false,
            'status' => 'not_started' // Default status
        ];

        try {
            $goal = $this->goalRepository->create($goalData);
            return $goal;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
