<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // auth:sanctum уже отработал до нас, но на случай прямого использования
        if (!Auth::check()) {
            throw new AuthenticationException();
        }

        if (!Auth::user()->hasAnyRole('admin', 'editor')) {
            throw new AuthorizationException('Forbidden');
        }

        return $next($request);
    }
}
