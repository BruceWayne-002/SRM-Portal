<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role != $role) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Unauthorized access. Please log in again.',
            ]);
        }

        return $next($request);
    }
}
