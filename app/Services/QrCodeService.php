<?php

namespace App\Services;

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
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
            'qr_token_used_at' => null, // reset qr tiap generate ulang
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

    //  QR payload SUPER RINGAN
    $payload = 'ABSEN|' . $token;

    $qrCode = QrCode::format('png')
        ->size(350)
        ->errorCorrection('L') 
        ->margin(3)
        ->generate($payload);

    return 'data:image/png;base64,' . base64_encode($qrCode);
    }
    /**
     *  FIXED: Validate QR Code dengan TYPE (checkin/checkout)
     */
    public static function validateQrCode(string $qrData, string $type): ?User
    {
    if (!str_starts_with($qrData, 'ABSEN|')) {
        return null;
    }

    $token = str_replace('ABSEN|', '', $qrData);

    $user = User::where('qr_token', $token)->first();

    if ($user->qr_token_used_at !== null) {
        Log::warning('Qr Sudah Pernah Dipakai', ['user_id' => $user->id]);
        return null;
    }

    if (
        !$user->qr_generated_at ||
        $user->qr_generated_at->addMinutes(5)->isPast()
    ) {
        Log::warning('QR Sudah Expired', ['user_id' => $user->id]);
        return null;
    }

    $attendance = $user->todayAttendance();

    if ($type === 'checkin' && $attendance && $attendance->check_in) {
        return null;
    }

    if ($type === 'checkout' && (!$attendance || !$attendance->check_in)) {
        return null;
    }

    return $user;
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
     *  NEW: Get last QR usage
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