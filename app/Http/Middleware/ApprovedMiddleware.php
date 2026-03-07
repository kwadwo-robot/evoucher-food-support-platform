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

        if ($user && !$user->is_approved && !$user->isAdmin()) {
            Auth::logout();
            return redirect()->route('login')
                ->with('warning', 'Your account is pending approval by an administrator. You will be notified once approved.');
        }

        return $next($request);
    }
}
