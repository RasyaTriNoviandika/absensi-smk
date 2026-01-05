<?php
// app/Services/QrCodeService.php

namespace App\Services;

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate QR Token untuk user
     */
    public static function generateToken(User $user): string
    {
        // Generate token yang secure
        $token = hash_hmac('sha256', 
            $user->id . '|' . $user->nisn . '|' . now()->timestamp,
            config('app.key')
        );
        
        $user->update([
            'qr_token' => $token,
            'qr_generated_at' => now()
        ]);
        
        Log::info('QR Token generated', ['user_id' => $user->id]);
        
        return $token;
    }
    
    /**
     * Generate QR Code Image (Base64)
     */
    public static function generateQrCode(User $user): string
    {
        $token = $user->qr_token ?? self::generateToken($user);
        
        // Payload: encrypted data
        $payload = Crypt::encryptString(json_encode([
            'user_id' => $user->id,
            'nisn' => $user->nisn,
            'token' => $token,
            'expires' => now()->addDays(30)->timestamp
        ]));
        
        // Generate QR dengan format PNG Base64
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($payload);
        
        return 'data:image/png;base64,' . base64_encode($qrCode);
    }
    
    /**
     * Validate QR Code
     */
    public static function validateQrCode(string $qrData): ?User
    {
        try {
            // Decrypt payload
            $decrypted = Crypt::decryptString($qrData);
            $data = json_decode($decrypted, true);
            
            // Check expiration
            if ($data['expires'] < now()->timestamp) {
                Log::warning('QR Code expired', ['data' => $data]);
                return null;
            }
            
            // Find user
            $user = User::where('id', $data['user_id'])
                ->where('nisn', $data['nisn'])
                ->where('qr_token', $data['token'])
                ->first();
            
            if (!$user) {
                Log::warning('QR Code invalid user', ['data' => $data]);
                return null;
            }
            
            // Check if token is still valid (within 30 days)
            if ($user->qr_generated_at && $user->qr_generated_at->addDays(30)->isPast()) {
                Log::warning('QR Token expired', ['user_id' => $user->id]);
                return null;
            }
            
            return $user;
            
        } catch (\Exception $e) {
            Log::error('QR Validation failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Regenerate QR Token (if compromised)
     */
    public static function regenerateToken(User $user): string
    {
        Log::info('QR Token regenerated', ['user_id' => $user->id]);
        return self::generateToken($user);
    }
}