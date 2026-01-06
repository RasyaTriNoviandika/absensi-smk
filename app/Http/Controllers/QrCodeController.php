<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    // Student: Show QR Code Page
    public function show()
    {
        $user = auth()->user();
        
        // Generate QR jika belum ada
        if (
            !$user->qr_token ||
            !$user->qr_generated_at ||
            $user->qr_generated_at->copy()->addDays(30)->isPast()
        ) {
            QrCodeService::generateToken($user);
        }

        
        $qrCode = QrCodeService::generateQrCode($user);
        
        return view('student.qr-code', compact('qrCode', 'user'));
    }
    
    // Student: Generate New QR Token
    public function generate(Request $request)
    {
        $user = auth()->user();
        
        try {
            QrCodeService::regenerateToken($user);
            $qrCode = QrCodeService::generateQrCode($user);
            
            return response()->json([
                'success' => true,
                'message' => 'QR Code berhasil di-generate ulang',
                'qr_code' => $qrCode
            ]);
        } catch (\Exception $e) {
            Log::error('QR Generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QR Code'
            ], 500);
        }
    }
    
    // Student: Download QR Code
    public function download()
    {
        $user = auth()->user();
        $qrCode = QrCodeService::generateQrCode($user);
        
        // Convert base64 to image
        $image = str_replace('data:image/png;base64,', '', $qrCode);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);
        
        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-code-' . $user->nisn . '.png"');
    }
    
    // Admin: Show Scanner Page
    public function scanner()
    {
        return view('admin.qr-scanner');
    }
    
    // Admin: Process QR Scan
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'type' => 'required|in:checkin,checkout'
        ]);
        
        try {
            $user = QrCodeService::validateQrCode($validated['qr_data']);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau expired'
                ], 400);
            }
            
            // Check if already checked in/out today
            $attendance = $user->todayAttendance();
            
            if ($validated['type'] === 'checkin') {
                if ($attendance && $attendance->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => $user->name . ' sudah absen masuk hari ini'
                    ], 400);
                }
                
                // Create check-in
                $now = now('Asia/Jakarta');
                $limitTime = \App\Models\Setting::get('check_in_time_limit', '07:30');
                $limitDateTime = today('Asia/Jakarta')->setTimeFromTimeString($limitTime);
                $status = $now->lessThanOrEqualTo($limitDateTime) ? 'hadir' : 'terlambat';
                
                \App\Models\Attendance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'date' => $now->toDateString(),
                    ],
                    [
                        'check_in' => $now->toTimeString(),
                        'check_in_status' => $status,
                        'check_in_method' => 'qr_backup',
                        'status' => $status,
                        'ip_address' => $request->ip(),
                    ]
                );
                
                return response()->json([
                    'success' => true,
                    'message' => $user->name . ' berhasil absen masuk',
                    'status' => $status,
                    'time' => $now->format('H:i'),
                    'student' => [
                        'name' => $user->name,
                        'nisn' => $user->nisn,
                        'class' => $user->class
                    ]
                ]);
            } else {
                // Check-out
                if (!$attendance || !$attendance->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => $user->name . ' belum absen masuk'
                    ], 400);
                }
                
                if ($attendance->check_out) {
                    return response()->json([
                        'success' => false,
                        'message' => $user->name . ' sudah absen pulang'
                    ], 400);
                }
                

                $minCheckoutTime = \App\Models\Setting::get('check_out_time_min', '16:00');
                $minTime = today('Asia/Jakarta')->setTimeFromTimeString($minCheckoutTime);

                    if (now('Asia/Jakarta')->lessThan($minTime)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Belum waktunya absen pulang'
                        ], 400);
                    }


                $attendance->update([
                    'check_out' => now('Asia/Jakarta')->toTimeString(),
                    'check_out_method' => 'qr_backup',
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $user->name . ' berhasil absen pulang',
                    'time' => now('Asia/Jakarta')->format('H:i'),
                    'student' => [
                        'name' => $user->name,
                        'nisn' => $user->nisn,
                        'class' => $user->class
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('QR Scan failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses QR Code'
            ], 500);
        }
    }
}