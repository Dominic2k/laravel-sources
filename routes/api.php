<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\Api\GoalController;

Route::apiResource('goals', GoalController::class);
Route::get('student-profile/{id}', [StudentProfileController::class, 'show']); 
Route::put('student-profile/{id}', [StudentProfileController::class, 'update']); 
Route::post('student-profile', [StudentProfileController::class, 'store']);    
Route::delete('student-profile/{id}', [StudentProfileController::class, 'destroy']);
// Route::get('student-profile/{id}', [StudentProfileController::class, 'show']);
// Route::put('student-profile/{id}', [StudentProfileController::class, 'update']);