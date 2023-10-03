<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->estado === 1) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => message('MSG007'),
            'status' => 401
        ], 401);
    }
}
