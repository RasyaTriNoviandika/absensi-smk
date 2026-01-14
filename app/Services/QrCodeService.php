<?php

namespace App\Services;

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate QR Token untuk user
     */
    public static function generateToken(User $user): string
    {
        $token = hash_hmac('sha256', 
            $user->id . '|' . $user->nisn . '|' . now()->timestamp,
            config('app.key')
        );
        
        $user->update([
            'qr_token' => $token,
            'qr_generated_at' => now(),
            'qr_token_used_at' => null, // reset setiap generate
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

        $payload = 'ABSEN|' . $token;

        $qrCode = QrCode::format('png')
            ->size(350)
            ->errorCorrection('L') 
            ->margin(3)
            ->generate($payload);

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }
    
    /**
     * Validate QR Code dengan kontrol berbasis attendance harian
     */
    public static function validateQrCode(string $qrData, string $type): ?User
    {
        // 1. Validasi format QR
        if (!str_starts_with($qrData, 'ABSEN|')) {
            Log::warning('QR: Format invalid', ['data' => substr($qrData, 0, 20)]);
            return null;
        }

        $token = str_replace('ABSEN|', '', $qrData);

        // 2. Cari user berdasarkan token
        $user = User::where('qr_token', $token)->first();

        if (!$user) {
            Log::warning('QR: Token tidak ditemukan', ['token' => substr($token, 0, 10)]);
            return null;
        }

        // 3. Validasi expiry (5 menit)
        if (!$user->qr_generated_at || $user->qr_generated_at->addMinutes(5)->isPast()) {
            Log::warning('QR: Token expired', [
                'user_id' => $user->id,
                'generated_at' => $user->qr_generated_at,
            ]);
            return null;
        }

        // 4. Ambil attendance hari ini
        $attendance = $user->todayAttendance();

        // 5. VALIDASI BERDASARKAN TYPE
        if ($type === 'checkin') {
            //  CHECK-IN: Belum boleh ada check_in
            if ($attendance && $attendance->check_in) {
                Log::warning('QR: Sudah check-in hari ini', [
                    'user_id' => $user->id,
                    'check_in' => $attendance->check_in,
                ]);
                return null;
            }
            
            // Boleh check-in
            Log::info('QR: Check-in valid', ['user_id' => $user->id]);
            return $user;
        }

        if ($type === 'checkout') {
            //  CHECK-OUT: Harus sudah check-in dulu
            if (!$attendance || !$attendance->check_in) {
                Log::warning('QR: Belum check-in', ['user_id' => $user->id]);
                return null;
            }

            //  boleh check-out
            if ($attendance->check_out) {
                Log::warning('QR: Sudah check-out hari ini', [
                    'user_id' => $user->id,
                    'check_out' => $attendance->check_out,
                ]);
                return null;
            }
            
            //  Boleh check-out
            Log::info('QR: Check-out valid', ['user_id' => $user->id]);
            return $user;
        }

        return null;
    }
    
    /**
     * Regenerate QR Token (if compromised)
     */
    public static function regenerateToken(User $user): string
    {
        Log::info('QR Token regenerated', ['user_id' => $user->id]);
        return self::generateToken($user);
    }
    
    /**
     * Get last QR usage
     */
    public static function getLastUsage(User $user): ?array
    {
        $lastAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->where(function($q) {
                $q->where('check_in_method', 'qr_backup')
                  ->orWhere('check_out_method', 'qr_backup');
            })
            ->latest()
            ->first();
        
        if (!$lastAttendance) {
            return null;
        }
        
        return [
            'date' => $lastAttendance->date,
            'type' => $lastAttendance->check_in_method === 'qr_backup' ? 'Check-In' : 'Check-Out',
            'time' => $lastAttendance->check_in_method === 'qr_backup' 
                ? $lastAttendance->check_in 
                : $lastAttendance->check_out
        ];
    }
}