<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetPostgresUserId
{
    public function handle($request, Closure $next)
    {
        if (auth::check()) {
            DB::statement("SET LOCAL app.current_user_id = '" . auth::id() . "'");
        }

        return $next($request);
    }
}
