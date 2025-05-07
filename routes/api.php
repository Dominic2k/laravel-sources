<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\GoalController;

Route::apiResource('goals', GoalController::class);
