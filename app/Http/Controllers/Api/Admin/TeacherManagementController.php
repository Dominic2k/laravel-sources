<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ClassStudent;

class TeacherManagementController extends Controller
{
    /**
     * Lấy danh sách tất cả sinh viên
     */
    public function index()
    {
        $students = Teacher::with('user')->get();
        
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
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'birthday' => 'nullable|date',
                'specialization' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'join_date' => 'nullable|date',
                'role' => 'required|in:teacher'
            ]);

            DB::beginTransaction();


            // Create user record
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
                'birthday' => $validated['birthday'] ? date('Y-m-d', strtotime($validated['birthday'])) : null,
                'last_login' => null
            ]);


            // Create teacher record
            $teacherData = [
                'user_id' => $user->id,
                'specialization' => $validated['specialization'],
                'bio' => $validated['bio'],
                'join_date' => $validated['join_date'] ? date('Y-m-d', strtotime($validated['join_date'])) : now()->format('Y-m-d')
            ];


            $teacher = Teacher::create($teacherData);
            

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $teacher->load('user')
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create teacher account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết của một sinh viên
     */
    public function show($id)
    {
        $student = Student::with('user')->findOrFail($id);
        
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
        try {
            $teacher = Teacher::findOrFail($id);
            $user = User::findOrFail($teacher->user_id);
            
            $validated = $request->validate([
                'full_name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'email',
                    Rule::unique('users')->ignore($user->id)
                ],
                'password' => 'sometimes|string|min:6',
                'birthday' => 'sometimes|date',
                'specialization' => 'sometimes|string|max:255',
                'bio' => 'nullable|string',
                'join_date' => 'sometimes|date',
                'role' => 'sometimes|in:teacher'
            ]);

            DB::beginTransaction();

            // Update user record
            if (isset($validated['full_name'])) {
                $user->full_name = $validated['full_name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            if (isset($validated['birthday'])) {
                $user->birthday = date('Y-m-d', strtotime($validated['birthday']));
            }
            $user->save();

            // Update teacher record
            if (isset($validated['specialization'])) {
                $teacher->specialization = $validated['specialization'];
            }
            if (isset($validated['bio'])) {
                $teacher->bio = $validated['bio'];
            }
            if (isset($validated['join_date'])) {
                $teacher->join_date = date('Y-m-d', strtotime($validated['join_date']));
            }
            $teacher->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $teacher->load('user')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update teacher account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa tài khoản sinh viên
     */
    public function destroy($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $userId = $teacher->user_id;

            DB::beginTransaction();

            

            // Xóa teacher trước (vì có foreign key constraint)
            $teacher->delete();
            
            // Xóa user sau
            User::destroy($userId);
            
            DB::commit();
            
           
            
            return response()->json([
                'success' => true,
                'message' => 'Teacher account deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
           
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete teacher account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
