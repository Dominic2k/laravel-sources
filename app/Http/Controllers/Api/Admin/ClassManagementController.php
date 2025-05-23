<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\ClassStudent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ClassManagementController extends Controller
{
    /**
     * Lấy danh sách tất cả các lớp
     */
    public function index()
    {
        $classes = Classes::withCount('students')->get();
        
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Tạo lớp học mới
     */
    

public function store(Request $request)
{
    $validated = $request->validate([
        'class_name' => 'required|string|max:100',
        'semester' => 'required|in:1,2,3,4,5,6',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'status' => 'required|in:planning,ongoing,completed'
    ]);

    // Chuyển định dạng ISO -> MySQL (Y-m-d)
    $validated['start_date'] = Carbon::parse($validated['start_date'])->format('Y-m-d');
    $validated['end_date'] = Carbon::parse($validated['end_date'])->format('Y-m-d');

    $class = Classes::create($validated);

    return response()->json([
        'success' => true,
        'data' => $class
    ], 201);
}


    /**
     * Hiển thị thông tin chi tiết của một lớp
     */
    public function show($id)
    {
        $class = Classes::with('students.user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * Cập nhật thông tin lớp học
     */
    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);
        
        $validated = $request->validate([
            'class_name' => 'sometimes|string|max:100',
            'semester' => 'sometimes|in:1,2,3,4,5,6',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'sometimes|in:planning,ongoing,completed'
        ]);

        $validated['start_date'] = Carbon::parse($validated['start_date'])->format('Y-m-d');
        $validated['end_date'] = Carbon::parse($validated['end_date'])->format('Y-m-d');
        
        $class->update($validated);
        
        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * Xóa lớp học
     */
    public function destroy($id)
    {
        $class = Classes::findOrFail($id);
        $class->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully'
        ]);
    }

    /**
     * Lấy danh sách sinh viên trong lớp
     */
    public function getStudents($classId)
    {
        $class = Classes::findOrFail($classId);
        $students = $class->students()->with('user')->get();
        
        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Thêm sinh viên vào lớp
     */
    public function addStudent(Request $request, $classId)
    {
        $class = Classes::findOrFail($classId);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);
        
        // Kiểm tra xem sinh viên đã có trong lớp chưa
        $exists = ClassStudent::where('class_id', $classId)
            ->where('student_id', $validated['student_id'])
            ->exists();
            
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Student already in this class'
            ], 422);
        }
        
        // Thêm sinh viên vào lớp
        ClassStudent::create([
            'class_id' => $classId,
            'student_id' => $validated['student_id']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Student added to class successfully'
        ]);
    }

    /**
     * Xóa sinh viên khỏi lớp
     */
    public function removeStudent($classId, $studentId)
    {
        $classStudent = ClassStudent::where('class_id', $classId)
            ->where('student_id', $studentId)
            ->firstOrFail();
            
        $classStudent->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Student removed from class successfully'
        ]);
    }

    /**
     * Tạo nhiều tài khoản sinh viên và thêm vào lớp
     */
    public function createStudentsForClass(Request $request, $classId)
    {
        $class = Classes::findOrFail($classId);
        
        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.full_name' => 'required|string|max:255',
            'students.*.email' => 'required|email|unique:users,email',
            'students.*.password' => 'required|string|min:6',
            'students.*.student_code' => 'required|string|unique:students,student_code',
            'students.*.admission_date' => 'required|date',
            'students.*.current_semester' => 'required|integer|min:1|max:6'
        ]);
        
        DB::beginTransaction();
        try {
            $createdStudents = [];
            
            foreach ($validated['students'] as $studentData) {
                // Tạo user
                $user = User::create([
                    'full_name' => $studentData['full_name'],
                    'email' => $studentData['email'],
                    'password' => Hash::make($studentData['password']),
                    'role' => 'student'
                ]);
                
                // Tạo student profile
                $student = Student::create([
                    'user_id' => $user->id,
                    'student_code' => $studentData['student_code'],
                    'admission_date' => $studentData['admission_date'],
                    'current_semester' => $studentData['current_semester']
                ]);
                
                // Thêm vào lớp
                ClassStudent::create([
                    'class_id' => $classId,
                    'student_id' => $student->id
                ]);
                
                $student->load('user');
                $createdStudents[] = $student;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($createdStudents) . ' students created and added to class successfully',
                'data' => $createdStudents
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create students',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}