<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deadline;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDeadlineController extends Controller
{
    /**
     * Get all deadlines for the authenticated student
     */
    public function getMyDeadlines()
    {
        try {
            $student = Auth::user()->student;
            
            // Get all classes that the student is enrolled in
            $studentClasses = $student->classes()->pluck('classes.id');
            
            // Get all deadlines for these classes
            $deadlines = Deadline::whereIn('class_id', $studentClasses)
                ->with(['class' => function($query) {
                    $query->select('id', 'class_name');
                }])
                ->orderBy('due_date', 'asc')
                ->get();

            // Add status for each deadline
            $deadlines->transform(function ($deadline) {
                $deadline->status = $this->getDeadlineStatus($deadline->due_date);
                return $deadline;
            });

            return response()->json([
                'success' => true,
                'data' => $deadlines
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch deadlines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming deadlines (within next 7 days)
     */
    public function getUpcomingDeadlines()
    {
        try {
            $student = Auth::user()->student;
            $studentClasses = $student->classes()->pluck('classes.id');
            
            $deadlines = Deadline::whereIn('class_id', $studentClasses)
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(7))
                ->with(['class' => function($query) {
                    $query->select('id', 'class_name');
                }])
                ->orderBy('due_date', 'asc')
                ->get();

            $deadlines->transform(function ($deadline) {
                $deadline->status = $this->getDeadlineStatus($deadline->due_date);
                return $deadline;
            });

            return response()->json([
                'success' => true,
                'data' => $deadlines
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming deadlines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get deadline details
     */
    public function getDeadlineDetails($id)
    {
        try {
            $student = Auth::user()->student;
            $studentClasses = $student->classes()->pluck('classes.id');
            
            $deadline = Deadline::whereIn('class_id', $studentClasses)
                ->where('id', $id)
                ->with(['class' => function($query) {
                    $query->select('id', 'class_name');
                }])
                ->first();

            if (!$deadline) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deadline not found'
                ], 404);
            }

            $deadline->status = $this->getDeadlineStatus($deadline->due_date);

            return response()->json([
                'success' => true,
                'data' => $deadline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch deadline details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to determine deadline status
     */
    private function getDeadlineStatus($dueDate)
    {
        $now = now();
        $dueDate = \Carbon\Carbon::parse($dueDate);

        if ($now->gt($dueDate)) {
            return 'overdue';
        } elseif ($now->diffInHours($dueDate) <= 24) {
            return 'urgent';
        } elseif ($now->diffInDays($dueDate) <= 7) {
            return 'upcoming';
        } else {
            return 'future';
        }
    }
} 