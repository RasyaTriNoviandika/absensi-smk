<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class PhotoController extends Controller
{
    /**
     * Serve attendance photos securely
     * Route: /secure-photo/{path}
     */
    public function show($path)
    {
        // Path sudah di-validate oleh SecurePhotoAccess middleware
        
        $fullPath = 'attendance/' . $path;
        
        if (!Storage::disk('local')->exists($fullPath)) {
            abort(404, 'Photo not found');
        }

        $file = Storage::disk('local')->get($fullPath);
        $mimeType = Storage::disk('local')->mimeType($fullPath);

        return Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}