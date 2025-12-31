<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ✅ AMBIL DARI SETTINGS TABLE
    private function getSchoolCoordinates()
    {
        return [
            'lat' => (float) Setting::get('school_latitude', -6.2706589),
            'lng' => (float) Setting::get('school_longitude', 106.9593685),
        ];
    }

    private function getMaxDistance()
    {
        // ✅ DEFAULT 100 meter untuk production
        return (int) Setting::get('max_distance_meters', 100);
    }

    public function checkIn(Request $request)
    {
        // ✅ RATE LIMITING: Max 3 attempts per 5 minutes
        $key = 'checkin:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ], 429);
        }

        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            
            // ✅ CHECK: Sudah absen hari ini?
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
                'face_descriptor' => 'required|array|size:128', // Face-api.js standard
                'photo' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            // ✅ VALIDASI LOKASI
            $school = $this->getSchoolCoordinates();
            $maxDistance = $this->getMaxDistance();
            
            $distance = $this->calculateDistance(
                $school['lat'], 
                $school['lng'], 
                $validated['latitude'], 
                $validated['longitude']
            );
            
            if ($distance > $maxDistance) {
                RateLimiter::hit($key, 300); // 5 minutes
                
                return response()->json([
                    'success' => false,
                    'message' => "Lokasi terlalu jauh. Jarak: " . round($distance) . "m (Max: {$maxDistance}m)"
                ], 400);
            }

            // ✅ VALIDASI WAJAH
            $storedDescriptor = $user->face_descriptor;
            if (!is_array($storedDescriptor)) {
                $storedDescriptor = json_decode($storedDescriptor, true);
            }
            
            $threshold = (float) Setting::get('face_match_threshold', 0.6);
            
            if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
                RateLimiter::hit($key, 300);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok. Pastikan pencahayaan cukup dan wajah terlihat jelas.'
                ], 400);
            }

            // ✅ SIMPAN FOTO
            try {
                $photoPath = $this->saveBase64Image($validated['photo'], 'checkin');
            } catch (\Exception $e) {
                Log::error('Photo save failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan foto. Coba lagi.'
                ], 500);
            }

            // ✅ TENTUKAN STATUS
            $checkInTime = Carbon::now('Asia/Jakarta');
            $limitTime = Setting::get('check_in_time_limit', '07:30');
            $limitDateTime = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($limitTime);
            $status = $checkInTime->lessThanOrEqualTo($limitDateTime) ? 'hadir' : 'terlambat';

            // ✅ SIMPAN ATTENDANCE
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $checkInTime->toDateString(),
                'check_in' => $checkInTime->toTimeString(),
                'check_in_status' => $status,
                'check_in_photo' => $photoPath,
                'status' => $status,
            ]);

            // ✅ LOG ACTIVITY
            Log::info('Check-in success', [
                'user_id' => $user->id,
                'time' => $checkInTime->format('H:i:s'),
                'status' => $status,
                'distance' => round($distance, 2) . 'm'
            ]);

            // ✅ CLEAR RATE LIMIT ON SUCCESS
            RateLimiter::clear($key);

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
                'message' => 'Data tidak valid. Pastikan wajah terdeteksi dan GPS aktif.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('CheckIn Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi admin.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        // ✅ RATE LIMITING
        $key = 'checkout:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ], 429);
        }

        try {
            $user = auth()->user();
            
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
            
            $isEarly = $currentTime->lessThan($minCheckoutDateTime);
            
            // ✅ VALIDASI PULANG CEPAT
            if ($isEarly) {
                if (!$request->filled('early_reason')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda pulang lebih awal. Harap isi alasan pulang cepat.',
                        'requires_reason' => true,
                        'min_time' => $minCheckoutTime,
                        'current_time' => $currentTime->format('H:i')
                    ], 400);
                }

                if (!$request->filled('early_photo')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Upload foto bukti surat izin pulang cepat.',
                        'requires_photo' => true
                    ], 400);
                }
            }

            $validated = $request->validate([
                'face_descriptor' => 'required|array|size:128',
                'photo' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'early_reason' => $isEarly ? 'required|string|min:10|max:500' : 'nullable|string|max:500',
                'early_photo' => $isEarly ? 'required|string' : 'nullable|string',
            ]);

            // ✅ VALIDASI LOKASI
            $school = $this->getSchoolCoordinates();
            $maxDistance = $this->getMaxDistance();
            
            $distance = $this->calculateDistance(
                $school['lat'], 
                $school['lng'], 
                $validated['latitude'], 
                $validated['longitude']
            );
            
            if ($distance > $maxDistance) {
                RateLimiter::hit($key, 300);
                
                return response()->json([
                    'success' => false,
                    'message' => "Lokasi terlalu jauh. Jarak: " . round($distance) . "m (Max: {$maxDistance}m)"
                ], 400);
            }

            // ✅ VALIDASI WAJAH
            $storedDescriptor = $user->face_descriptor;
            if (!is_array($storedDescriptor)) {
                $storedDescriptor = json_decode($storedDescriptor, true);
            }

            $threshold = (float) Setting::get('face_match_threshold', 0.6);

            if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
                RateLimiter::hit($key, 300);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok. Silakan coba lagi.'
                ], 400);
            }

            // ✅ SIMPAN FOTO
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

            // ✅ SIMPAN FOTO BUKTI & ALASAN
            if ($isEarly && isset($validated['early_reason'])) {
                $updateData['notes'] = 'Pulang cepat (' . $currentTime->format('H:i') . '): ' . $validated['early_reason'];
                
                if (!empty($validated['early_photo'])) {
                    try {
                        $earlyPhotoPath = $this->saveBase64Image($validated['early_photo'], 'early_letter');
                        $updateData['early_checkout_photo'] = $earlyPhotoPath;
                    } catch (\Exception $e) {
                        Log::error('Early photo save failed: ' . $e->getMessage());
                    }
                }
            }

            $attendance->update($updateData);

            // ✅ LOG ACTIVITY
            Log::info('Check-out success', [
                'user_id' => $user->id,
                'time' => $currentTime->format('H:i:s'),
                'is_early' => $isEarly,
                'distance' => round($distance, 2) . 'm'
            ]);

            RateLimiter::clear($key);

            $message = $isEarly 
                ? 'Absen pulang berhasil. Alasan dan bukti surat telah tercatat.'
                : 'Absen pulang berhasil! Hati-hati di jalan.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'time' => $currentTime->format('H:i'),
                'is_early' => $isEarly
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('CheckOut Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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
            // ✅ VALIDATE IMAGE SIZE (Max 5MB)
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            $image = str_replace(' ', '+', $image);
            
            $decodedImage = base64_decode($image);
            $imageSize = strlen($decodedImage);
            
            if ($imageSize > 5 * 1024 * 1024) { // 5MB
                throw new \Exception('Image size too large (max 5MB)');
            }
            
            $imageName = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
            $path = 'attendance/' . $imageName;
            
            if (!Storage::disk('public')->put($path, $decodedImage)) {
                throw new \Exception('Failed to save image to storage');
            }
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Image save error: ' . $e->getMessage());
            throw new \Exception('Failed to save image: ' . $e->getMessage());
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