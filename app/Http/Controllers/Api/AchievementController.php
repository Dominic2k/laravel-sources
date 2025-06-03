<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Achievement;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        $achievements = Achievement::with(['student:id,full_name', 'classSubject:id,subject_id', 'classSubject.subject:id,subject_name'])
            ->orderBy('achievement_date', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $achievements
        ]);
    }

    public function show($id)
    {
        $achievement = Achievement::with(['student:id,full_name', 'classSubject:id,subject_id', 'classSubject.subject:id,subject_name'])
            ->find($id);

        if (!$achievement) {
            return response()->json([
                'success' => false,
                'message' => 'Achievement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $achievement
        ]);
    }

    public function store(Request $request) {
        try {
            $user = Auth::guard('sanctum')->user();
            $student = Student::where('user_id', $user->id)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only students can create achievements'
                ], 403);
            }

            $validated = $request->validate([
                'file_url' => 'required|image|max:10120',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'class_subject_id' => 'required|integer',
                'achievement_date' => 'required|date',
                'semester' => 'required|integer'
            ]);

            if ($request->hasFile('file_url')) {
                $file = $request->file('file_url');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $uploadPath = public_path('uploads/achievements');
                
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $fileName);
                $imageUrl = asset('uploads/achievements/' . $fileName);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded.'
                ], 400);
            }

            $achievement = Achievement::create([
                'student_id' => $student->user_id,
                'file_url' => $imageUrl,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'class_subject_id' => $validated['class_subject_id'],
                'achievement_date' => $validated['achievement_date'],
                'semester' => $validated['semester'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Achievement uploaded successfully!',
                'data' => $achievement
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating achievement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create achievement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $student = Student::where('user_id', $user->id)->first();
            
            $achievement = Achievement::find($id);

            if (!$achievement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Achievement not found'
                ], 404);
            }

            if (!$student || $achievement->student_id !== $student->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this achievement'
                ], 403);
            }

            $validated = $request->validate([
                'file_url' => 'nullable|image|max:10120',
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'class_subject_id' => 'sometimes|integer',
                'achievement_date' => 'sometimes|date',
                'semester' => 'sometimes|in:1,2,3,4,5,6',
            ]);

            if ($request->hasFile('file_url')) {
                $file = $request->file('file_url');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $uploadPath = public_path('uploads/achievements');
                
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $fileName);
                $imageUrl = asset('uploads/achievements/' . $fileName);
                $validated['file_url'] = $imageUrl;
            }

            $achievement->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Achievement updated successfully!',
                'data' => $achievement
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating achievement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update achievement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $student = Student::where('user_id', $user->id)->first();
            
            $achievement = Achievement::find($id);

            if (!$achievement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Achievement not found'
                ], 404);
            }

            if (!$student || $achievement->student_id !== $student->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this achievement'
                ], 403);
            }

            $achievement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Achievement deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting achievement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete achievement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByStudent($studentId)
    {
        try {
            $achievements = Achievement::where('student_id', $studentId)
                ->with(['classSubject:id,subject_id', 'classSubject.subject:id,subject_name'])
                ->orderBy('achievement_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $achievements
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting student achievements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get student achievements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByClassSubject($classSubjectId)
    {
        try {
            $achievements = Achievement::where('class_subject_id', $classSubjectId)
                ->with(['student:id,full_name', 'classSubject:id,subject_id', 'classSubject.subject:id,subject_name'])
                ->orderBy('achievement_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $achievements
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting class subject achievements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get class subject achievements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBySemester($semester)
    {
        try {
            $achievements = Achievement::where('semester', $semester)
                ->with(['student:id,full_name', 'classSubject:id,subject_id', 'classSubject.subject:id,subject_name'])
                ->orderBy('achievement_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $achievements
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting semester achievements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get semester achievements',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
