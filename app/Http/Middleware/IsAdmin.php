<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->hasRole('super_admin')) {
            return $next($request);
        }

        return response()->json([
            'message' => message('MSG008'),
            'status' => 403,
            'success' => false,
        ], 403);
    }
}
