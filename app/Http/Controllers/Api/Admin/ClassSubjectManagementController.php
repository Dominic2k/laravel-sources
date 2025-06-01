<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClassSubjectManagementController extends Controller
{
    /**
     * Get all available data for assignment form
     */
    public function getAssignmentData()
    {
        try {
            // Log each query result
            $classes = Classes::select('id', 'class_name')->get();
            Log::info('Classes query result:', ['count' => $classes->count(), 'data' => $classes->toArray()]);

            $subjects = Subject::select('id', 'subject_name')->get();
            Log::info('Subjects query result:', ['count' => $subjects->count(), 'data' => $subjects->toArray()]);

            $teachers = Teacher::with(['user:id,full_name'])->get();
            Log::info('Teachers query result:', ['count' => $teachers->count(), 'data' => $teachers->toArray()]);

            $students = Student::with(['user:id,full_name'])->get();
            Log::info('Students query result:', ['count' => $students->count(), 'data' => $students->toArray()]);

            $data = [
                'classes' => $classes,
                'subjects' => $subjects,
                'teachers' => $teachers->map(function($teacher) {
                    return [
                        'id' => $teacher->user_id,
                        'name' => $teacher->user->full_name
                    ];
                }),
                'students' => $students->map(function($student) {
                    return [
                        'id' => $student->user_id,
                        'name' => $student->user->full_name,
                        'student_code' => $student->student_code
                    ];
                })
            ];

            // Log final response data
            Log::info('Final response data:', ['data' => $data]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // Log the error with stack trace
            Log::error('Error in getAssignmentData:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assignment data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle form submission for class subject assignment
     */
    public function handleAssignment(Request $request)
    {
        try {
            // Log request data
            Log::info('Assignment request data:', $request->all());

            $validator = Validator::make($request->all(), [
                'class_id' => 'required|exists:classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:teachers,user_id',
                'students' => 'required|array',
                'students.*' => 'exists:students,user_id',
                'schedule_info' => 'required|string',
                'room' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed:', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // 1. Create class subject and assign teacher
                $classSubjectData = [
                    'class_id' => $request->class_id,
                    'subject_id' => $request->subject_id,
                    'teacher_id' => $request->teacher_id,
                    'schedule_info' => $request->schedule_info,
                    'room' => $request->room,
                    'status' => 'active'
                ];
                Log::info('Creating class subject with data:', $classSubjectData);

                $classSubject = ClassSubject::create($classSubjectData);
                Log::info('Class subject created:', ['id' => $classSubject->id]);

                // 2. Assign students to class
                $classStudents = [];
                foreach ($request->students as $studentId) {
                    $classStudents[] = [
                        'class_id' => $request->class_id,
                        'student_id' => $studentId,
                        'created_at' => now()
                    ];
                }
                Log::info('Preparing to insert students:', ['count' => count($classStudents)]);

                DB::table('class_students')->insert($classStudents);
                Log::info('Students inserted successfully');

                DB::commit();
                Log::info('Transaction committed successfully');

                // 3. Return complete data
                $responseData = [
                    'class_subject' => $classSubject->load(['subject', 'teacher.user', 'class']),
                    'students' => DB::table('class_students')
                        ->join('students', 'class_students.student_id', '=', 'students.user_id')
                        ->join('users', 'students.user_id', '=', 'users.id')
                        ->where('class_students.class_id', $request->class_id)
                        ->select('students.user_id', 'users.full_name', 'students.student_code')
                        ->get()
                ];
                Log::info('Response data prepared:', ['class_subject_id' => $classSubject->id, 'students_count' => count($responseData['students'])]);

                return response()->json([
                    'success' => true,
                    'message' => 'Class subject and students assigned successfully',
                    'data' => $responseData
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error during transaction:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Failed to process assignment:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 