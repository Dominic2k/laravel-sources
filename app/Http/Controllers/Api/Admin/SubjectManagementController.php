<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectManagementController extends Controller
{
    /**
     * Lấy danh sách tất cả môn học
     */
    public function index()
    {
        try {
            $subjects = Subject::all();
            
            return response()->json([
                'success' => true,
                'data' => $subjects
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching subjects:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subjects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo môn học mới
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject_name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            DB::beginTransaction();

            Log::info('Creating subject with data:', $validated);

            $subject = Subject::create($validated);
            
            Log::info('Subject created successfully', ['subject' => $subject->toArray()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $subject
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating subject:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết của một môn học
     */
    public function show($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $subject
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching subject:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin môn học
     */
    public function update(Request $request, $id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            $validated = $request->validate([
                'subject_name' => 'sometimes|string|max:255',
                'description' => 'nullable|string'
            ]);

            DB::beginTransaction();

            Log::info('Updating subject:', [
                'subject_id' => $id,
                'data' => $validated
            ]);

            $subject->update($validated);
            
            Log::info('Subject updated successfully', ['subject' => $subject->toArray()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $subject
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating subject:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa môn học
     */
    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);

            DB::beginTransaction();

            Log::info('Deleting subject:', ['subject_id' => $id]);

            $subject->delete();
            
            Log::info('Subject deleted successfully', ['subject_id' => $id]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting subject:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 