<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Student;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $studentId = $request->route('student_id');
        $user = $request->user();
        
        // Nếu không phải là admin và không phải là chủ sở hữu
        if (!$user->isAdmin() && $user->id != $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access this resource'
            ], 403);
        }
        
        return $next($request);
    }
}