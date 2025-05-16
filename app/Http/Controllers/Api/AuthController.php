<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Sai thông tin đăng nhập'], 401);
        }

        $user = Auth::user();
        
        // Tạo token với thời hạn 24 giờ
        $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

        // Lấy thông tin student nếu user là student
        $student = null;
        if ($user->role === 'student') {
            $student = $user->student;
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 24 * 60 * 60, // 24 giờ tính bằng giây
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'role' => $user->role,
            ],
            'student' => $student ? [
                'id' => $student->user_id,
                'student_code' => $student->student_code,
                'admission_date' => $student->admission_date,
                'current_semester' => $student->current_semester,
            ] : null
        ]);
    }

    
    public function logout()
    {
        Auth::guard('sanctum')->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}

