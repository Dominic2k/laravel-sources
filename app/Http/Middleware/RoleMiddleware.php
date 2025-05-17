<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Lấy user từ token (Sanctum xử lý sẵn)
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Token không hợp lệ hoặc không tồn tại'], 401);
        }

        if ($user->role === $role) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden - Không có quyền truy cập'], 403);
    }
}
