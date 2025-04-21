<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RoleRequest as UserRoleRequest;
use Illuminate\Http\Request;
use RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function indexPermission()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            return response()->json(['message' => 'No permissions found'], 404);
        }

        return response()->json([
            'permissions' => $permissions,
            'message' => 'Permissions fetched successfully',
        ], 200);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $roles = Role::all();

        if ($roles->isEmpty()) {
            return response()->json(['message' => 'No roles found'], 404);
        }

        return response()->json([
            'roles' => $roles,
            'message' => 'Roles fetched successfully',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRoleRequest $request)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $role = Role::create([
            'name' => $request->input('name'),
        ]);

        if (!$role) {
            return response()->json(['message' => 'Failed to create role'], 500);
        }
        if (!$request->has('permissions')) {
            return response()->json(['message' => 'Permissions are required'], 422);
        }
        $permissions = $request->input('permissions');

        $role->syncPermissions($permissions);

        if (!$role->hasAllPermissions($permissions)) {
            return response()->json(['message' => 'Failed to assign permissions'], 500);
        }

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json(['role' => $role], 200);
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
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->name = $request->input('name');
        $role->save();

        if ($request->has('permissions')) {
            $permissions = $request->input('permissions');
            $role->syncPermissions($permissions);
        }

        return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
