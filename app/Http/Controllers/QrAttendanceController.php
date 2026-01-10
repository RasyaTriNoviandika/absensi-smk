<?php

namespace App\Services;

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate QR Token
     */
    public static function generateToken(User $user): string
    {
        $token = hash_hmac(
            'sha256',
            $user->id . '|' . $user->nisn . '|' . now()->timestamp,
            config('app.key')
        );

        $user->update([
            'qr_token' => $token,
            'qr_generated_at' => now()
        ]);

        return $token;
    }

    /**
     * Generate QR Code (Base64)
     */
    public static function generateQrCode(User $user): string
    {
        $token = $user->qr_token ?? self::generateToken($user);

        $payload = 'ABSEN|' . $token;

        $qr = QrCode::format('png')
            ->size(300)
            ->errorCorrection('M')
            ->generate($payload);

        return 'data:image/png;base64,' . base64_encode($qr);
    }

    /**
     * Validate QR Data
     */
    public static function validateQrCode(string $qrData, string $type): ?User
    {
        if (!str_starts_with($qrData, 'ABSEN|')) {
            return null;
        }

        $token = str_replace('ABSEN|', '', $qrData);

        $user = User::where('qr_token', $token)->first();
        if (!$user) {
            return null;
        }

        // Check expiry (misalnya 30 hari)
        if (!$user->qr_generated_at || $user->qr_generated_at->addDays(30)->isPast()) {
            return null;
        }

        $attendance = $user->todayAttendance();

        if ($type === 'checkin' && $attendance && $attendance->check_in) {
            return null;
        }

        if ($type === 'checkout') {
            if (!$attendance || !$attendance->check_in) return null;
            if ($attendance->check_out) return null;
        }

        return $user;
    }
}
