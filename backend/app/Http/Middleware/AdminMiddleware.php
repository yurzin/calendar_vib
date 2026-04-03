<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!Auth::user()->hasAnyRole('admin', 'editor')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
