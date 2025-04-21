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

        $users = User::all();

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
        $validatedData['outlet_id'] = $request->input('outlet_id', null); // Set to null if not provided
        $user = User::create($validatedData);
        $user->assignRole($validatedData['role']);
        // Attach outlet role if outlet_id is provided
        if ($validatedData['outlet_id']) {
            $user->outlet()->attach($validatedData['outlet_id']);
        }
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
