<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Outlet;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function getAllAbsensi(Request $request)
    {
        $absensi = Absensi::with('user', 'outlet')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($absensi->isEmpty()) {
            return response()->json(['message' => 'No absensi found'], 404);
        }

        return response()->json([
            'data' => $absensi,
            'message' => 'Absensi fetched successfully',
        ], 200);
    }

    public function getAbsensiByUserId(Request $request, string $outlet_id, string $id)
    {
        $absensi = Absensi::with('user', 'outlet')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($absensi->isEmpty()) {
            return response()->json(['message' => 'No absensi found for this user'], 404);
        }

        return response()->json([
            'data' => $absensi,
            'message' => 'Absensi fetched successfully',
        ], 200);
    }

    public function getAbsensiCurrentUser(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $absensi = Absensi::with('user', 'outlet')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'No absensi found for the current user'], 404);
        }

        return response()->json([
            'data' => $absensi,
            'message' => 'Absensi fetched successfully',
        ], 200);
    }

    public function getAbsensiByOutletId(Request $request, string $outlet_id)
    {

        $absensi = Absensi::with('user', 'outlet')
            ->where('outlet_id', $outlet_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($absensi->isEmpty()) {
            return response()->json(['message' => 'No absensi found'], 404);
        }

        return response()->json([
            'data' => $absensi,
            'message' => 'Absensi fetched successfully',
        ], 200);
    }

    public function getAbsensiById(Request $request, string $outlet_id, string $id)
    {
        $absensi = Absensi::with('user', 'outlet')->find($id);

        if (!$absensi) {
            return response()->json(['message' => 'Absensi not found'], 404);
        }

        return response()->json([
            'data' => $absensi,
            'message' => 'Absensi fetched successfully',
        ], 200);
    }

    public function checkInAbsensi(Request $request, string $outlet_id)
    {
        $validatedData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $outlet = Outlet::where('id', $outlet_id)
            ->first();
        if (!$outlet) {
            return response()->json(['message' => 'Outlet not found'], 404);
        }

        $absensi = Absensi::where('user_id', $user->id)
            ->where('outlet_id', $outlet_id)
            ->whereDate('check_in', today())
            ->first();

        if ($absensi) {
            return response()->json(['message' => 'User already checked in today'], 400);
        }

        $distance = $this->calculateDistance(
            $validatedData['latitude'],
            $validatedData['longitude'],
            $outlet->latitude,
            $outlet->longitude
        );

        if ($distance > 0.2) { // 200 meters
            return response()->json(['message' => 'You are too far from the outlet'], 400);
        }

        $createAbsensi = Absensi::create([
            'user_id' => $user->id,
            'outlet_id' => $outlet_id,
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'status' => 'check_in',
            'check_in' => now(),
        ]);

        if (!$createAbsensi) {
            return response()->json(['message' => 'Failed to create absensi'], 500);
        }

        return response()->json([
            'data' => $createAbsensi,
            'message' => 'Check-in successful',
        ], 201);
    }

    public function checkOutAbsensi(Request $request, string $outlet_id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $absensi = Absensi::where('user_id', $user->id)
            ->where('outlet_id', $outlet_id)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'You have not checked in or already checked out'], 400);
        }

        $absensi->check_out = now();
        $absensi->status = 'check_out';
        $absensi->save();

        return response()->json([
            'data' => $absensi,
            'message' => 'Check-out successful',
        ], 200);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
