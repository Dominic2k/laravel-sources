<?php

// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, $role)    {
        // return response()->json(['message' => $role], 406);

        // kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Token không tồn tại'], 401);
        }

        // Kiểm tra xác thực thông qua Sanctum
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Token không hợp lệ'], 401);
        }
        
        if (!$role) {
            return $next($request);
        }

        if (Auth::guard('sanctum')->user()->role === $role) {
            return $next($request);
        }
        
        // Nếu không có quyền, trả về lỗi Unauthorized
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
