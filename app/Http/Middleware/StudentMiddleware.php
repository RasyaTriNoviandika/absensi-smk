<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 🔐 Belum login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ⛔ Bukan student
        if (!auth()->user()->isStudent()) {
            abort(403, 'Bukan student');
        }

        // ⏳ Belum di-approve
        if (!auth()->user()->isApproved()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda masih menunggu approval dari admin.');
        }

        return $next($request);
    }
}
