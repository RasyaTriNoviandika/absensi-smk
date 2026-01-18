<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurePhotoAccess
{
    public function handle(Request $request, Closure $next): Response
{
    // 🔒 Middleware ini HANYA untuk route yang punya parameter {path}
    if (!$request->route() || !$request->route()->hasParameter('path')) {
        return $next($request);
    }

    $user = auth()->user();
    $path = (string) $request->route('path');

    Log::info('SecurePhotoAccess Middleware', [
        'authenticated' => $user !== null,
        'user_id' => $user?->id,
        'user_role' => $user?->role,
        'path' => $path,
        'ip' => $request->ip(),
    ]);

    if (!$user) {
        abort(403, 'Unauthorized');
    }

    if ($user->role === 'admin') {
        return $next($request);
    }

    if ($user->role === 'student') {
        if (preg_match('/user_(\d+)\//', $path, $matches)) {
            if ((int)$matches[1] === (int)$user->id) {
                return $next($request);
            }
        }
        abort(403, 'Akses ditolak');
    }

    abort(403);
}

}