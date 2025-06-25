<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $users = User::with('outlet','roles')->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return response()->json([
            'users' => $users,
            'message' => 'Users fetched successfully',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = User::create($validatedData);
        $user->assignRole($validatedData['role']);
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $user = User::with('outlet','roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'users' => $user,
            'message' => 'Users fetched successfully',
        ], 200);
    }

    public function showCurrentUser()
    {
        $user = auth()->user()->load('outlet');

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'user' => $user,
            'message' => 'User fetched successfully',
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    
     

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
