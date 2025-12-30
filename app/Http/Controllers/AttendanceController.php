<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            
            if (Attendance::where('user_id', $user->id)
                ->whereDate('date', today())
                ->whereNotNull('check_in')
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah absen masuk hari ini.'
                ], 400);
            }

            $validated = $request->validate([
                'face_descriptor' => 'required|array',
                'photo' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $schoolLat = -6.2706589;
            $schoolLng = 106.9593685;
            $maxDistance = 50000;
            
            $distance = $this->calculateDistance(
                $schoolLat, 
                $schoolLng, 
                $validated['latitude'], 
                $validated['longitude']
            );
            
            if ($distance > $maxDistance) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda berada " . round($distance) . " meter dari sekolah. Maksimal $maxDistance meter."
                ], 400);
            }

            $storedDescriptor = $user->face_descriptor;
            if (!is_array($storedDescriptor)) {
                $storedDescriptor = json_decode($storedDescriptor, true);
            }
            
            $threshold = Setting::get('face_match_threshold', 0.6);
            
            if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok. Silakan coba lagi.'
                ], 400);
            }

            try {
                $photoPath = $this->saveBase64Image($validated['photo'], 'checkin');
            } catch (\Exception $e) {
                Log::error('Photo save failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan foto. Coba lagi.'
                ], 500);
            }

            $checkInTime = Carbon::now('Asia/Jakarta');
            $limitTime = Setting::get('check_in_time_limit', '07:30');
            $limitDateTime = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($limitTime);
            $status = $checkInTime->lessThanOrEqualTo($limitDateTime) ? 'hadir' : 'terlambat';

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $checkInTime->toDateString(),
                'check_in' => $checkInTime->toTimeString(),
                'check_in_status' => $status,
                'check_in_photo' => $photoPath,
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => $status === 'hadir' 
                    ? 'Absen masuk berhasil! Anda tepat waktu.' 
                    : 'Absen masuk berhasil, namun Anda terlambat.',
                'status' => $status,
                'time' => $checkInTime->format('H:i'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('CheckIn Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Pastikan selalu return JSON
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        
        // Check attendance hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();
        
        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus absen masuk terlebih dahulu.'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini.'
            ], 400);
        }

        $currentTime = Carbon::now('Asia/Jakarta');
        $minCheckoutTime = Setting::get('check_out_time_min', '16:00');
        $minCheckoutDateTime = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($minCheckoutTime);
        
        $isEarlyCheckout = $currentTime->lessThan($minCheckoutDateTime);
        
        // Validasi berbeda untuk pulang cepat
        if ($isEarlyCheckout) {
            // Pulang cepat WAJIB isi reason DAN upload foto bukti
            if (!$request->filled('early_reason')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda pulang lebih awal. Harap isi alasan pulang cepat.',
                    'requires_reason' => true,
                    'min_time' => $minCheckoutTime,
                    'current_time' => $currentTime->format('H:i')
                ], 400);
            }

            // Cek foto bukti surat
            if (!$request->filled('early_photo')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload foto bukti surat izin pulang cepat.',
                    'requires_photo' => true
                ], 400);
            }
        }

        $validated = $request->validate([
            'face_descriptor' => 'required|array',
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'early_reason' => $isEarlyCheckout ? 'required|string|min:10|max:500' : 'nullable|string|max:500',
            'early_photo' => $isEarlyCheckout ? 'required|string' : 'nullable|string', // ✅ TAMBAHAN
        ]);

        // Validasi lokasi
        $schoolLat = -6.2706589;
        $schoolLng = 106.9593685;
        $maxDistance = 50000;
        
        $distance = $this->calculateDistance(
            $schoolLat, 
            $schoolLng, 
            $validated['latitude'], 
            $validated['longitude']
        );
        
        if ($distance > $maxDistance) {
            return response()->json([
                'success' => false,
                'message' => "Anda berada " . round($distance) . " meter dari sekolah. Maksimal $maxDistance meter."
            ], 400);
        }

        // Verify face
        $storedDescriptor = $user->face_descriptor;
        if (!is_array($storedDescriptor)) {
            $storedDescriptor = json_decode($storedDescriptor, true);
        }

        $threshold = Setting::get('face_match_threshold', 0.6);

        if (!FaceRecognitionService::isMatch(
            $validated['face_descriptor'],
            $storedDescriptor,
            $threshold
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak cocok. Silakan coba lagi.'
            ], 400);
        }

        // Save checkout photo
        try {
            $photoPath = $this->saveBase64Image($validated['photo'], 'checkout');
        } catch (\Exception $e) {
            Log::error('Photo save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan foto. Coba lagi.'
            ], 500);
        }

        $updateData = [
            'check_out' => $currentTime->toTimeString(),
            'check_out_photo' => $photoPath,
        ];

        // Simpan foto bukti surat dan notes
        if ($isEarlyCheckout && isset($validated['early_reason'])) {
            $updateData['notes'] = 'Pulang cepat (' . $currentTime->format('H:i') . '): ' . $validated['early_reason'];
            
            // Save early checkout photo bukti
            if (!empty($validated['early_photo'])) {
                try {
                    $earlyPhotoPath = $this->saveBase64Image($validated['early_photo'], 'early_letter');
                    $updateData['early_checkout_photo'] = $earlyPhotoPath;
                } catch (\Exception $e) {
                    Log::error('Early photo save failed: ' . $e->getMessage());
                    // Continue tanpa foto bukti jika gagal
                }
            }
        }

        $attendance->update($updateData);

        $message = $isEarlyCheckout 
            ? 'Absen pulang berhasil. Alasan dan bukti surat telah tercatat.'
            : 'Absen pulang berhasil! Hati-hati di jalan.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'time' => $currentTime->format('H:i'),
            'is_early' => $isEarlyCheckout
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $R = 6371000; // meter

    $phi1 = deg2rad($lat1);
    $phi2 = deg2rad($lat2);
    $deltaPhi = deg2rad($lat2 - $lat1);
    $deltaLambda = deg2rad($lon2 - $lon1);

    $a = sin($deltaPhi / 2) * sin($deltaPhi / 2) +
         cos($phi1) * cos($phi2) *
         sin($deltaLambda / 2) * sin($deltaLambda / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $R * $c;
}

    private function saveBase64Image($base64String, $prefix)
    {
        try {
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            $image = str_replace(' ', '+', $image);
            $imageName = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
            
            $path = 'attendance/' . $imageName;
            
            if (!Storage::disk('public')->put($path, base64_decode($image))) {
                throw new \Exception('Failed to save image to storage');
            }
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Image save error: ' . $e->getMessage());
            throw new \Exception('Failed to save image');
        }
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->attendances();
        
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        } else {
            $query->whereMonth('date', now()->month)
                  ->whereYear('date', now()->year);
        }
        
        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        
        return view('student.history', compact('attendances'));
    }
    
}