<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurePhotoAccess
{
    /**
     * Validasi akses foto absensi
     *
     * Rules:
     * - Admin: akses semua foto
     * - Student: hanya foto milik sendiri
     * - Guest / role lain: ditolak (403)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 🚨 Middleware RESOURCE: TIDAK BOLEH REDIRECT
        if (!$user) {
            abort(403);
        }

        $path = (string) $request->route('path');

        // ✅ ADMIN: FULL ACCESS
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ✅ STUDENT: HANYA FOTO SENDIRI
        if ($user->role === 'student') {

            /**
             * Contoh path:
             * attendance/user_296/2026-01-13/early_letter_xxx.jpg
             */
            if (preg_match('/user_(\d+)\//', $path, $matches)) {
                $photoUserId = (int) $matches[1];

                if ($photoUserId === (int) $user->id) {
                    return $next($request);
                }
            }

            // ⛔ Akses tidak sah
            Log::warning('Unauthorized photo access attempt', [
                'user_id' => $user->id,
                'path'    => $path,
                'ip'      => $request->ip(),
            ]);

            abort(403);
        }

        // ⛔ Role lain ditolak
        abort(403);
    }
}
