<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 🔐 Belum login → ke login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ⛔ Sudah login tapi bukan admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bukan admin');
        }

        return $next($request);
    }
}
