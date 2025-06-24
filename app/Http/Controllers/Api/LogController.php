<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Log;



class LogController extends Controller
{
    public function getAllLogs() {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $logs = Log::orderBy('changed_at', 'desc')->limit(100)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Logs fetched successfully',
            'data' => $logs
        ]);

        
    }
}
