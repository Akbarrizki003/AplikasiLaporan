<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('login');
        }

        // Get authenticated user
        $user = Auth::user();
        
        // If no specific roles are required or user is admin, allow access
        if (empty($roles) || $user->role === 'admin') {
            return $next($request);
        }
        
        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }
        
        // If user doesn't have the required role, redirect or return error
        return response()->view('errors.unauthorized', [], 403);
    }
}