<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }
                
                if ($user->isStudent() && $user->isApproved()) {
                    return redirect()->route('student.dashboard');
                }
            }
        }

        return $next($request);
    }
}