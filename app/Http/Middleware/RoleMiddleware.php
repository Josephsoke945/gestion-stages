<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (Auth::user()->role === $role) {
            return $next($request);
        }

        // Redirect with error message if the user doesn't have the required role
        return redirect()->route('dashboard')->with('error', 'Accès non autorisé. Vous n\'avez pas les droits nécessaires.');
    }
} 