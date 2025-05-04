<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        // Cek apakah pengguna memiliki salah satu role yang diizinkan
        if (count($roles) > 0) {
            $userRole = $request->user()->role;
            
            foreach ($roles as $role) {
                if ($userRole === $role) {
                    return $next($request);
                }
            }
            
            // Jika tidak memiliki role yang sesuai
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        return $next($request);
    }
}