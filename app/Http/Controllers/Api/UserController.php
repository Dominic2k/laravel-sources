<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{


    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:users',
            'full_name' => 'required|string|max:255',
            'birthday' => 'nullable|date'
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        
        $user = User::create($validated);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }
    
    public function show(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
    
    public function update(Request $request, $id)
    {
        // $user = User::findOrFail($id);
        $user = Auth::guard('sanctum')->user();
        
        $validated = $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'password' => 'sometimes|string|min:6',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'full_name' => 'sometimes|string|max:255',
            'birthday' => 'nullable|date'
        ]);
        
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        
        $user->update($validated);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}