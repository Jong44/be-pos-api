<?php

namespace App\Http\Middleware;

use App\Models\Outlet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateOutletAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $outletId = $request->route('outlet_id');

        if(!$outletId) {
            return response()->json(['message' => 'Outlet ID is required'], 400);
        }

        $activeOutlet = Outlet::where('id', $outletId)->first();
        if (!$activeOutlet) {
            return response()->json(['message' => 'Outlet not found'], 404);
        }

        $user = auth()->user();

        if($user->outlet_id != $outletId){
            if ($user->getRoleNames()[0] == 'superadmin' ) {
                return $next($request);
            }
            return response()->json(['message' => 'You do not have access to this outlet', 'role' => $user->getRoleNames()[0]], 403);
        }

        return $next($request);





    }
}
