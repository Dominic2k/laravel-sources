<?php

// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$role)
    {
        // Kiểm tra người dùng đã đăng nhập chưa và có role không
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Kiểm tra người dùng có role phù hợp hay không
        $user = $request->user();
        if ($user->hasRole($role)) {
            return $next($request);  // Nếu có quyền thì tiếp tục
        }

        // Nếu không có quyền, trả về lỗi Unauthorized
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
