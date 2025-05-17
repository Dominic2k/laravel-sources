<?php

namespace App\Repositories;

use App\Models\Goal;
use App\Models\Student;
use App\Models\ClassSubject;

class GoalRepository
{
    /**
     * Lấy tất cả goals của một sinh viên và môn học
     *
     * @param int $studentId
     * @param int $classSubjectId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStudentAndSubject($studentId, $classSubjectId)
    {
        return Goal::where('student_id', $studentId)
            ->where('class_subject_id', $classSubjectId)
            ->with([
                'student.user',
                'classSubject.subject',
                'classSubject.class',
                'classSubject.teacher.user'
            ])
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'description' => $goal->description,
                    'goal_type' => $goal->goal_type,
                    'start_date' => $goal->start_date,
                    'end_date' => $goal->end_date,
                    'status' => $goal->status,
                    'priority' => $goal->priority,
                    'is_private' => $goal->is_private,
                    'subject' => [
                        'id' => $goal->classSubject->subject->id,
                        'name' => $goal->classSubject->subject->subject_name,
                    ],
                    'class' => [
                        'id' => $goal->classSubject->class->id,
                        'name' => $goal->classSubject->class->class_name,
                    ],
                    'teacher' => [
                        'id' => $goal->classSubject->teacher->user->id,
                        'name' => $goal->classSubject->teacher->user->full_name,
                    ],
                    'room' => $goal->classSubject->room,
                    'schedule_info' => $goal->classSubject->schedule_info,
                    'created_at' => $goal->created_at,
                    'updated_at' => $goal->updated_at,
                ];
            });
    }

    /**
     * Lấy chi tiết một goal
     *
     * @param int $goalId
     * @param int $studentId
     * @return Goal|null
     */
    public function getDetail($goalId, $studentId)
    {
        return Goal::where('id', $goalId)
            ->where('student_id', $studentId)
            ->with(['student', 'classSubject.subject', 'classSubject.class', 'classSubject.teacher.user'])
            ->first();
    }

    /**
     * Tạo goal mới
     *
     * @param array $data
     * @return Goal
     */
    public function create(array $data)
    {
        $goal = Goal::create($data);
        $goal->load(['student', 'classSubject']);
        return $goal;
    }

    /**
     * Cập nhật goal
     *
     * @param Goal $goal
     * @param array $data
     * @return Goal
     */
    public function update(Goal $goal, array $data)
    {
        $goal->update($data);
        $goal->load(['student', 'classSubject']);
        return $goal;
    }

    /**
     * Xóa goal
     *
     * @param Goal $goal
     * @return bool
     */
    public function delete(Goal $goal)
    {
        return $goal->delete();
    }

    /**
     * Kiểm tra sinh viên tồn tại
     *
     * @param int $studentId
     * @return Student|null
     */
    public function findStudent($studentId)
    {
        return Student::where('user_id', $studentId)->first();
    }

    /**
     * Kiểm tra môn học tồn tại
     *
     * @param int $classSubjectId
     * @return ClassSubject|null
     */
    public function findClassSubject($classSubjectId)
    {
        return ClassSubject::find($classSubjectId);
    }

    /**
     * Tìm goal theo ID và student ID
     *
     * @param int $goalId
     * @param int $studentId
     * @return Goal|null
     */
    public function findGoal($goalId, $studentId)
    {
        return Goal::where('id', $goalId)
            ->where('student_id', $studentId)
            ->first();
    }
}
