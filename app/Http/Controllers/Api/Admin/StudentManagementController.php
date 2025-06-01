<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ClassStudent;

class StudentManagementController extends Controller
{
    /**
     * Lấy danh sách tất cả sinh viên
     */
    public function index()
    {
        $students = Student::with('user')->get();
        
        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Tạo tài khoản sinh viên mới và gắn vào lớp (nếu có)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'student_code' => 'required|string|unique:students',
            'admission_date' => 'required|date',
            'current_semester' => 'required|integer|min:1|max:6',
            'class_id' => 'nullable|exists:classes,id',
            'last_login' => 'nullable|date',
            'birthday' => 'nullable|date',
            'role' => 'required|in:student,teacher,admin'
        ]);

        DB::beginTransaction();
        try {
            // Tạo user trước
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'], // Sử dụng role từ request
                'birthday' => $validated['birthday'],
                'last_login' => $validated['last_login']
            ]);

            // Tạo student profile
            $student = Student::create([
                'user_id' => $user->id,
                'student_code' => $validated['student_code'],
                'admission_date' => $validated['admission_date'],
                'current_semester' => $validated['current_semester']
            ]);

            // Nếu có class_id, thêm sinh viên vào lớp
            if (isset($validated['class_id'])) {
                ClassStudent::create([
                    'class_id' => $validated['class_id'],
                    'student_id' => $student->user_id
                ]);
            }

            DB::commit();

            // Load thông tin user và lớp học (nếu có)
            $student->load('user');
            if (isset($validated['class_id'])) {
                $student->load('classes');
            }
            
            return response()->json([
                'success' => true,
                'data' => $student
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết của một sinh viên
     */
    // public function show($id)
    // {
    //     $student = Student::with('user')->findOrFail($id);
        
    //     return response()->json([
    //         'success' => true,
    //         'data' => $student
    //     ]);
    // }


    public function show($id)
    {
        $student = Student::with('user')->findOrFail($id);

        // Lấy class_id mới nhất của student từ bảng trung gian class_students
        $classId = ClassStudent::where('student_id', $student->user_id)
                            ->latest('created_at')
                            ->value('class_id');

        // Thêm class_id vào kết quả trả về (không ảnh hưởng DB)
        $student->setAttribute('class_id', $classId);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }



    /**
     * Cập nhật thông tin sinh viên
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $user = User::findOrFail($student->user_id);
        
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|string|min:6',
            'admission_date' => 'sometimes|date',
            'current_semester' => 'sometimes|integer|min:1|max:6',
            'role' => 'sometimes|in:student,teacher,admin'
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật thông tin user
            if (isset($validated['full_name'])) {
                $user->full_name = $validated['full_name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            if (isset($validated['role'])) {
                $user->role = $validated['role'];
            }
            $user->save();

            // Cập nhật thông tin student
            if (isset($validated['admission_date'])) {
                $student->admission_date = $validated['admission_date'];
            }
            if (isset($validated['current_semester'])) {
                $student->current_semester = $validated['current_semester'];
            }
            $student->save();

            DB::commit();

            $student->load('user');
            return response()->json([
                'success' => true,
                'data' => $student
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa tài khoản sinh viên
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Delete the associated user (this will cascade delete the student record)
            $student->user->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

