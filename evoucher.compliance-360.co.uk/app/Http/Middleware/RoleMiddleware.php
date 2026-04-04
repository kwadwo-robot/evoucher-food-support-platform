<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // Check if user has one of the required roles
        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) return $next($request);
            if ($role === 'super_admin' && $user->isSuperAdmin()) return $next($request);
            if ($user->role === $role) return $next($request);
        }

        // Redirect to their own dashboard with error
        return redirect()->route($user->getDashboardRoute())
            ->with('error', 'You do not have permission to access that page.');
    }
}
