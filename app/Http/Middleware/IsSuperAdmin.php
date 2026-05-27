<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role === 'SuperAdmin') {
            return $next($request);
        }

        // Redirect unauthorized users
        return redirect()->route('login')->withErrors([
            'error' => 'Unauthorized access. Super Admin only.',
        ]);
    }
}
