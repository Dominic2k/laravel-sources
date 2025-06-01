<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class ActivityLogController extends Controller
{
    public function index()
        {
            $goalLogs = DB::table('goals')
                ->join('users', 'goals.student_id', '=', 'users.id')
                ->select('goals.created_at as time', 'users.full_name as user_name', DB::raw("'created goal' as action"), 'goals.title as entity');

            $deadlineLogs = DB::table('deadlines')
                ->join('users', 'deadlines.set_by', '=', 'users.id')
                ->select('deadlines.created_at as time', 'users.full_name as user_name', DB::raw("'created deadline' as action"), 'deadlines.title as entity');

            $inClassLogs = DB::table('in_class_plans')
                ->join('users', 'in_class_plans.student_id', '=', 'users.id')
                ->select('in_class_plans.created_at as time', 'users.full_name as user_name', DB::raw("'submitted in-class plan' as action"), 'in_class_plans.skills_module as entity');

            $selfStudyLogs = DB::table('self_study_plans')
                ->join('users', 'self_study_plans.student_id', '=', 'users.id')
                ->select('self_study_plans.created_at as time', 'users.full_name as user_name', DB::raw("'submitted self-study plan' as action"), 'self_study_plans.lesson as entity');

            $feedbackLogs = DB::table('feedbacks')
                ->join('users', 'feedbacks.teacher_id', '=', 'users.id')
                ->select('feedbacks.created_at as time', 'users.full_name as user_name', DB::raw("'gave feedback' as action"), 'feedbacks.entity_type as entity');

            $lastLoginLogs = DB::table('users')
                ->whereNotNull('last_login')
                ->select('last_login as time', 'full_name as user_name', DB::raw("'logged in' as action"), DB::raw("'-' as entity"));

            $logs = $goalLogs
                ->unionAll($deadlineLogs)
                ->unionAll($inClassLogs)
                ->unionAll($selfStudyLogs)
                ->unionAll($feedbackLogs)
                ->unionAll($lastLoginLogs)
                ->orderBy('time', 'desc')
                ->get();

//hihihihihi
            return response()->json($logs);
        }
}