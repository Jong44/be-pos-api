<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OutletController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
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
    // outlet change to resource
    Route::resource('outlets', OutletController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('permissions', [RoleController::class, 'indexPermission']);

});

