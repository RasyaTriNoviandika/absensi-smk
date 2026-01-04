<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Attendance;

class SecurePhotoAccess
{
    /**
     * Handle an incoming request untuk akses foto attendance
     * Hanya owner foto atau admin yang bisa akses
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Admin bisa akses semua foto
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Student hanya bisa akses foto mereka sendiri
        $photoPath = $request->route('path');
        
        // Extract user_id dari path: attendance/user_{id}/...
        if (preg_match('/user_(\d+)/', $photoPath, $matches)) {
            $photoUserId = (int) $matches[1];
            
            if ($user->id !== $photoUserId) {
                abort(403, 'You can only access your own photos');
            }
        }

        return $next($request);
    }
}