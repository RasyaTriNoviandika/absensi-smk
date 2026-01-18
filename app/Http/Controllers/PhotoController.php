<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhotoController extends Controller
{
    public function show(Request $request, string $path): StreamedResponse
    {
        $path = urldecode($path);

        // SATU-SATUNYA LOKASI FOTO
        $fullPath = storage_path('app/' . $path);

        Log::info('Photo access', [
            'user_id' => auth()->id(),
            'role' => auth()->user()?->role,
            'path' => $path,
            'full_path' => $fullPath,
        ]);

        if (!file_exists($fullPath)) {
            abort(404, 'Foto tidak ditemukan');
        }

        return response()->file($fullPath, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }
}
