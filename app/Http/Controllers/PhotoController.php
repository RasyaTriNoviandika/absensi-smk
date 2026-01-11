<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PhotoController extends Controller
{
    /**
     * Secure attendance photo viewer
     * URL: /secure-photo/attendance/xxx.jpg
     */
    public function show(string $path)
    {
       
        if (str_contains($path, '..')) {
            abort(403);
        }

   
        if (! str_starts_with($path, 'attendance/')) {
            abort(403);
        }

  
        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'Photo not found');
        }

        return response()->file(
            Storage::disk('public')->path($path),
            [
                'Content-Type'  => Storage::disk('public')->mimeType($path),
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]
        );
    }
}
