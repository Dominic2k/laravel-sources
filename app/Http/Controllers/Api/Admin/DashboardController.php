<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classes;
use App\Models\Class as ClassModel;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get overall statistics for the dashboard
     */
    public function getStatistics()
    {
        try {
            $statistics = [
                'total_students' => Student::count(),
                'total_teachers' => Teacher::count(),
                'total_classes' => Classes::count(),
                'total_subjects' => Subject::count()
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

} 