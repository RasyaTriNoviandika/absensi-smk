<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\GpsValidationService;
use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    //  FIXED: Private method untuk get coordinates dari Settings
    private function getSchoolCoordinates()
    {
        return [
            'lat' => (float) Setting::get('school_latitude', -6.2706589),
            'lng' => (float) Setting::get('school_longitude', 106.9593685),
        ];
    }

    private function getMaxDistance()
    {
        return (int) Setting::get('max_distance_meters', 100);
    }

public function checkIn(Request $request)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Anda belum login. Silakan login terlebih dahulu.'
        ], 401);
    }

    $lock = Cache::lock(
        "attendance:checkin:{$user->id}:" . now('Asia/Jakarta')->toDateString(),
        10
    );

    if (!$lock->get()) {
        return response()->json([
            'success' => false,
            'message' => 'Proses absen sedang berlangsung. Tunggu beberapa detik lagi.'
        ], 429);
    }

    $key = 'checkin:' . $user->id;

    try {
        // RATE LIMIT
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan absen. Tunggu {$seconds} detik lagi."
            ], 429);
        }

        DB::beginTransaction();

        // LOCK ATTENDANCE ROW
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now('Asia/Jakarta')->toDateString())
            ->lockForUpdate()
            ->first();

        if ($existingAttendance && $existingAttendance->check_in) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini pada pukul ' . Carbon::parse($existingAttendance->check_in)->format('H:i')
            ], 400);
        }

        $validated = $request->validate([
            'face_descriptor' => 'required|array|size:128',
            'photo' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // ✅ FIXED: Set default distance untuk testing
        $distance = 0;

        // GPS VALIDATION - NONAKTIFKAN UNTUK TESTING
        // Uncomment kode dibawah untuk aktifkan GPS validation
        /*
        try {
            $distance = GpsValidationService::validate(
                $validated['latitude'],
                $validated['longitude'],
                $user
            );
        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::hit($key, 60);
            
            Log::warning('GPS: Outside radius', [
                'user_id' => $user->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
        */

        // ✅ FIXED: Cek face descriptor dengan pesan yang jelas
        if (!$user->face_descriptor) {
            DB::rollBack();
            
            Log::warning('Face descriptor not found', [
                'user_id' => $user->id,
                'name' => $user->name
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Data wajah Anda belum terdaftar. Silakan daftar wajah terlebih dahulu di menu Profil.'
            ], 400);
        }

        // ✅ FIXED: FACE MATCH dengan error handling lengkap
        try {
            $storedDescriptor = is_array($user->face_descriptor)
                ? $user->face_descriptor
                : json_decode($user->face_descriptor, true);

            if (!is_array($storedDescriptor) || count($storedDescriptor) !== 128) {
                throw new \Exception('Data wajah tersimpan tidak valid');
            }

            $threshold = (float) Setting::get('face_match_threshold', 0.5);

            if (!FaceRecognitionService::isMatch(
                $validated['face_descriptor'],
                $storedDescriptor,
                $threshold
            )) {
                RateLimiter::hit($key, 60);
                DB::rollBack();

                Log::warning('Face mismatch on check-in', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok dengan data yang terdaftar. Pastikan pencahayaan cukup dan wajah terlihat jelas.'
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Face recognition error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi wajah: ' . $e->getMessage()
            ], 500);
        }

        // ✅ FIXED: Save photo dengan error handling
        try {
            $photoPath = $this->saveBase64ImageSecure(
                $validated['photo'],
                'checkin',
                $user->id
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Photo save failed on check-in: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan foto absen. Silakan coba lagi.'
            ], 500);
        }

        // STATUS
        $now = Carbon::now('Asia/Jakarta');
        $limit = Carbon::today('Asia/Jakarta')
            ->setTimeFromTimeString(
                Setting::get('check_in_time_limit', '07:30')
            );

        $status = $now->lessThanOrEqualTo($limit) ? 'hadir' : 'terlambat';

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $now->toDateString(),
            ],
            [
                'check_in' => $now->toTimeString(),
                'check_in_status' => $status,
                'check_in_photo' => $photoPath,
                'status' => $status,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        DB::commit();
        RateLimiter::clear($key);

        $user->update([
            'qr_token_used_at' => now(),
        ]);

        Log::info('Check-in success', [
            'user_id' => $user->id,
            'status' => $status,
            'time' => $now->format('H:i:s')
        ]);

        $statusMessage = $status === 'hadir' 
            ? 'Absen masuk berhasil! Selamat belajar.' 
            : 'Absen masuk berhasil (Terlambat). Jangan diulangi ya!';

        return response()->json([
            'success' => true,
            'message' => $statusMessage,
            'status' => $status,
            'time' => $now->format('H:i')
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        
        $errors = $e->errors();
        $firstError = collect($errors)->flatten()->first();
        
        return response()->json([
            'success' => false,
            'message' => 'Data tidak lengkap: ' . $firstError,
            'errors' => $errors
        ], 422);
        
    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('CheckIn Error', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi dalam beberapa saat.',
            'error_detail' => config('app.debug') ? $e->getMessage() : null
        ], 500);

    } finally {
        optional($lock)->release();
    }
}

   
    public function checkOut(Request $request)
    {
        $key = 'checkout:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ], 429);
        }

        DB::beginTransaction();

        try {
            $user = auth()->user();
            
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', now('Asia/Jakarta')->toDateString())
                ->lockForUpdate()
                ->first();
            
            if (!$attendance || !$attendance->check_in) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus absen masuk terlebih dahulu.'
                ], 400);
            }

            if ($attendance->check_out) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah absen pulang hari ini.'
                ], 400);
            }

            $currentTime = Carbon::now('Asia/Jakarta');
            $minCheckoutTime = Setting::get('check_out_time_min', '16:00');
            $minCheckoutDateTime = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($minCheckoutTime);
            
            $isEarly = $currentTime->lessThan($minCheckoutDateTime);
            
            // VALIDASI PULANG CEPAT
            if ($isEarly) {
                if (!$request->filled('early_reason')) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda pulang lebih awal. Harap isi alasan pulang cepat.',
                        'requires_reason' => true,
                        'min_time' => $minCheckoutTime,
                        'current_time' => $currentTime->format('H:i')
                    ], 400);
                }

                if (!$request->filled('early_photo')) {
                    DB::rollBack();
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

            // ✅ FIXED: GPS validation dengan default value
            $distance = 0;
            
            // GPS VALIDATION - NONAKTIFKAN UNTUK TESTING
            // Uncomment untuk aktifkan GPS validation
            /*
            try {
                $distance = GpsValidationService::validate(
                    $validated['latitude'],
                    $validated['longitude'],
                    $user
                );
            } catch (\Exception $e) {
                DB::rollBack();
                RateLimiter::hit($key, 60);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            */

           // CEK APAKAH USER SUDAH TERDAFTAR WAJAH
if (!$user->face_descriptor) {
    DB::rollBack();
    return response()->json([
        'success' => false,
        'message' => 'Data wajah belum terdaftar. Silakan daftar wajah terlebih dahulu.'
    ], 400);
}

// ✅ FIX: ambil descriptor dari user
$storedDescriptor = $user->face_descriptor;

if (!is_array($storedDescriptor)) {
    $storedDescriptor = json_decode($storedDescriptor, true);
}

if (!is_array($storedDescriptor) || count($storedDescriptor) !== 128) {
    DB::rollBack();
    return response()->json([
        'success' => false,
        'message' => 'Data wajah tidak valid. Hubungi admin.'
    ], 400);
}



            $threshold = (float) Setting::get('face_match_threshold', 0.5);

            if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
                DB::rollBack();
                RateLimiter::hit($key, 60);

                Log::channel('security')->warning('Face recognition failed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'timestamp' => now(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok. Silakan coba lagi.'
                ], 400);
            }

            //  FIXED: Simpan foto ke PRIVATE storage
            try {
                $photoPath = $this->saveBase64ImageSecure($validated['photo'], 'checkout', $user->id);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Photo save failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan foto. Coba lagi.'
                ], 500);
            }

            $updateData = [
                'check_out' => $currentTime->toTimeString(),
                'check_out_photo' => $photoPath,
                'notes' => 'pulang(' . $currentTime->format('H:i') . ')',
            ];

            // SIMPAN ALASAN & FOTO JIKA ADA (TANPA TERGANTUNG JAM)
           if ($request->filled('early_reason')) {
    $updateData['notes'] =
        'Pulang Cepat (' . $currentTime->format('H:i') . '): ' .
        $request->early_reason;

    if ($request->filled('early_photo')) {
        $earlyPhotoPath = $this->saveBase64ImageSecure(
            $request->early_photo,
            'early_letter',
            $user->id
        );

        $updateData['early_checkout_photo'] = $earlyPhotoPath;
    }
}

            $attendance->update($updateData);

            DB::commit();

            $user->update([
                'qr_token_used_at' => now(),
            ]);

            Log::info('Check-out success', [
                'user_id' => $user->id,
                'time' => $currentTime->format('H:i:s'),
                'is_early' => $isEarly
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
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
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

    // FIXED: Simpan ke PRIVATE storage dengan struktur folder per user
   private function saveBase64ImageSecure($base64String, $prefix, $userId)
{
    try {
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
        $image = str_replace(' ', '+', $image);
        $decodedImage = base64_decode($image, true);
        
        if ($decodedImage === false) {
            throw new \Exception('Invalid base64 image');
        }
        
        //  SECURITY: Verify it's actually an image
        $imageInfo = @getimagesizefromstring($decodedImage);
        if ($imageInfo === false) {
            throw new \Exception('Not a valid image');
        }
        
        //  SECURITY: Whitelist MIME types
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($imageInfo['mime'], $allowedMimes)) {
            throw new \Exception('Invalid image type. Only JPEG/PNG allowed');
        }
        
        //  SECURITY: Check dimensions (prevent DoS)
        if ($imageInfo[0] > 4000 || $imageInfo[1] > 4000) {
            throw new \Exception('Image dimensions too large (max 4000x4000)');
        }
        
        //  SECURITY: Check file size
        $imageSize = strlen($decodedImage);
        if ($imageSize > 5 * 1024 * 1024) {
            throw new \Exception('Image size too large (max 5MB)');
        }
        
        // SECURITY: Re-encode to strip potential malware/metadata
        $image = imagecreatefromstring($decodedImage);
        if ($image === false) {
            throw new \Exception('Failed to process image');
        }
        
        ob_start();
        imagejpeg($image, null, 85);
        $cleanImage = ob_get_clean();
        imagedestroy($image);
        
        // Save with secure path
        $date = now()->format('Y-m-d');
$imageName = $prefix . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.jpg';

// ❌ JANGAN SIMPAN attendance/ DI DB
$relativePath = "user_{$userId}/{$date}/{$imageName}";

// SIMPAN KE storage/app/attendance/...
Storage::disk('local')->put(
    "attendance/{$relativePath}",
    $cleanImage
);

// DB SIMPAN PATH RELATIF
return $relativePath;

        
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