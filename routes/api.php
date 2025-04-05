<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', 'App\Http\Controllers\UserController@register');
Route::post('/login', 'App\Http\Controllers\UserController@login');
Route::get('/user', 'App\Http\Controllers\UserController@getUserDetails')->middleware('auth:sanctum');
