<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Attendance;

class SecurePhotoAccess
{
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

        $photoPath = $request->route('path');
        
        // 🔒 SECURITY FIX: Prevent path traversal
        if (str_contains($photoPath, '..') || 
            str_contains($photoPath, '//') ||
            str_contains($photoPath, '\\') ||
            preg_match('/%2e|%2f|%5c/i', $photoPath)) {
            abort(403, 'Invalid path');
        }
        
        // 🔒 SECURITY FIX: Verify ownership via database
        $attendance = Attendance::where('user_id', $user->id)
            ->where(function($q) use ($photoPath) {
                $q->where('check_in_photo', 'like', "%{$photoPath}%")
                  ->orWhere('check_out_photo', 'like', "%{$photoPath}%")
                  ->orWhere('early_checkout_photo', 'like', "%{$photoPath}%");
            })
            ->first();
        
        if (!$attendance) {
            abort(403, 'You can only access your own photos');
        }

        return $next($request);
    }
}