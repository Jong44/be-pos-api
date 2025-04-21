<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outlet\OutletRequest;
use App\Models\Outlet;
use OutletResource;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }
        // Get all outlets
        try{
            $outlets = Outlet::all();

        if ($outlets->isEmpty()) {
            return response()->json(['message' => 'No outlets found'], 404);
        }
        return response()->json([
            'outlets' => $outlets,
            'message' => 'Outlets fetched successfully',
        ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch outlets', 'error' => $e->getMessage()], 500);
        }

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
    public function store(OutletRequest $request)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }
        // Create a new outlet
        $outlet = Outlet::create($request->validated());

        if (!$outlet) {
            return response()->json(['message' => 'Failed to create outlet'], 500);
        }
        return response()->json(['message' => 'Outlet created successfully', 'outlet' => $outlet], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get a single outlet
        $outlet = Outlet::find($id);

        if (!$outlet) {
            return response()->json(['message' => 'Outlet not found'], 404);
        }
        return response()->json([
            'message' => 'Outlet fetched successfully',
            'outlet' => $outlet
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
    public function update(OutletRequest $request, string $id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }
        // Update the outlet
        $outlet = Outlet::find($id);

        if (!$outlet) {
            return response()->json(['message' => 'Outlet not found'], 404);
        }

        $outlet->update($request->validated());

        return response()->json(['message' => 'Outlet updated successfully', 'outlet' => $outlet], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }
        // Delete the outlet
        $outlet = Outlet::find($id);

        if (!$outlet) {
            return response()->json(['message' => 'Outlet not found'], 404);
        }

        $outlet->delete();

        return response()->json(['message' => 'Outlet deleted successfully'], 200);
    }
}
