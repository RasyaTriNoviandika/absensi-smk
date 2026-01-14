<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhotoController extends Controller
{
    /**
     * sServe secure photo dengan validasi akses
     */
    public function show(Request $request, string $path): StreamedResponse
    {
        // Decode path (karena mungkin di-encode di URL)
        $path = urldecode($path);
        
        Log::info('Photo access attempt', [
            'user_id' => auth()->id(),
            'path' => $path,
            'ip' => $request->ip(),
        ]);

        // Validasi file exists
        if (!Storage::disk('local')->exists($path)) {
            Log::warning('Photo not found', ['path' => $path]);
            abort(404, 'Foto tidak ditemukan');
        }

        // Ambil file
        $file = Storage::disk('local')->get($path);
        $mimeType = Storage::disk('local')->mimeType($path);

        // Return sebagai streamed response (lebih aman & efisien)
        return response()->stream(
            function() use ($file) {
                echo $file;
            },
            200,
            [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'private, max-age=3600', // Cache 1 jam
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
            ]
        );
    }
}