<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isStudent()) {
            abort(403, 'Unauthorized access');
        }

        if (!auth()->user()->isApproved()) {
            return redirect()->route('login')->with('error', 'Akun Anda masih menunggu approval dari admin.');
        }

        return $next($request);
    }
}
