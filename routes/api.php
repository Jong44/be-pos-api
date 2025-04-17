<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// test route
Route::get('/test', function () {
    return response()->json(['message' => 'Test route is working!']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    // Protected routes go here
    // Example: Route::get('user', [UserController::class, 'index']);
    return response()->json(['message' => 'Protected route is working!']);
});

