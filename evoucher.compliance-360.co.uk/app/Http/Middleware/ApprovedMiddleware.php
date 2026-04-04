<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApprovedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // All users are auto-approved on registration.
        // Only block accounts that have been explicitly deactivated by an admin.
        if ($user && !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('warning', 'Your account has been deactivated. Please contact support for assistance.');
        }

        return $next($request);
    }
}
