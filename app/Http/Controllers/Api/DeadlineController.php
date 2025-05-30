<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deadline;
use App\Models\Classes;

class DeadlineController extends Controller
{

    public function index($classId)
    {
       // Optional: Validate classId nếu cần đảm bảo class tồn tại
    $deadlines = Deadline::with('creator')->latest()->get();

    $formatted = $deadlines->map(function ($d) {
        return [
            'id' => $d->id,
            'title' => $d->title,
            'description' => $d->description,
            'due_date' => $d->due_date,
            'created_by' => [
                'name' => $d->creator->name ?? null,
                'email' => $d->creator->email ?? null,
            ],
        ];
    });

    return response()->json($formatted);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $classId)
    {
        $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'required|date|after:now',
        ]);

        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            return response()->json(['error' => 'Only teachers can set deadlines.'], 403);
        }

        $class = Classes::findOrFail($classId);

        $deadline = Deadline::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'set_by' => Auth::id(),
            'class_id' => $classId,
        ]);

        return response()->json([
            'message' => 'Deadline created successfully.',
            'deadline' => $deadline,
            'created_by' => [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $deadline = Deadline::findOrFail($id);
        return response()->json($deadline);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deadline = Deadline::findOrFail($id);
        $this->authorize('update', $deadline);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:now',
        ]);

        $deadline->update($request->only(['title', 'description', 'due_date']));

        return response()->json([
            'message' => 'Deadline updated successfully.',
            'deadline' => $deadline,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deadline = Deadline::findOrFail($id);
        $this->authorize('delete', $deadline);
        $deadline->delete();

        return response()->json(['message' => 'Deadline deleted successfully.']);
    }
}
