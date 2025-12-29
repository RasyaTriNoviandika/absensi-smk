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
        $user = auth()->user();
        
        // Check apakah sudah check-in hari ini
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

        // Validasi lokasi
        $schoolLat = -6.2706589;
        $schoolLng = 106.9593685;
        $maxDistance = 50000; // 50km untuk development, ganti ke 100 untuk production
        
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
        
        if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak cocok. Silakan coba lagi.'
            ], 400);
        }

        // Save photo
        try {
            $photoPath = $this->saveBase64Image($validated['photo'], 'checkin');
        } catch (\Exception $e) {
            Log::error('Photo save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan foto. Coba lagi.'
            ], 500);
        }

        // FIX: Gunakan Carbon::now() untuk waktu real-time yang akurat
        $checkInTime = Carbon::now('Asia/Jakarta');
        $limitTime = Setting::get('check_in_time_limit', '07:30');
        
        // Parse limit time ke Carbon untuk hari ini
        $limitDateTime = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($limitTime);
        
        // Bandingkan waktu check-in dengan batas waktu
        $status = $checkInTime->lessThanOrEqualTo($limitDateTime) ? 'hadir' : 'terlambat';

        // Create attendance record dengan format datetime yang benar
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $checkInTime->toDateString(), // Format: Y-m-d
            'check_in' => $checkInTime->toTimeString(), // Format: H:i:s
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

        // FIX: Gunakan Carbon untuk waktu real-time
        $currentTime = Carbon::now('Asia/Jakarta');
        $minCheckoutTime = Setting::get('check_out_time_min', '16:00');
        $minCheckoutDateTime = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($minCheckoutTime);
        
        // Jika pulang lebih awal, wajib isi alasan
        $isEarlyCheckout = $currentTime->lessThan($minCheckoutDateTime);
        
        if ($isEarlyCheckout && !$request->filled('early_reason')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda pulang lebih awal dari jam ' . $minCheckoutTime . '. Harap isi alasan pulang cepat.',
                'requires_reason' => true,
                'min_time' => $minCheckoutTime,
                'current_time' => $currentTime->format('H:i')
            ], 400);
        }

        $validated = $request->validate([
            'face_descriptor' => 'required|array',
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'early_reason' => $isEarlyCheckout ? 'required|string|min:10|max:500' : 'nullable|string|max:500',
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

        // Save photo
        try {
            $photoPath = $this->saveBase64Image($validated['photo'], 'checkout');
        } catch (\Exception $e) {
            Log::error('Photo save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan foto. Coba lagi.'
            ], 500);
        }

        // FIX: Update attendance dengan waktu real-time dan notes
        $updateData = [
            'check_out' => $currentTime->toTimeString(), // Format: H:i:s
            'check_out_photo' => $photoPath,
        ];

        // FIX: Simpan notes untuk early checkout
        if ($isEarlyCheckout && isset($validated['early_reason'])) {
            $updateData['notes'] = 'Pulang cepat (' . $currentTime->format('H:i') . '): ' . $validated['early_reason'];
        }

        $attendance->update($updateData);

        $message = $isEarlyCheckout 
            ? 'Absen pulang berhasil. Alasan pulang cepat telah tercatat.'
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
        $R = 6371e3; // Earth radius in meters
        $φ1 = deg2rad($lat1);
        $φ2 = deg2rad($lat2);
        $Δφ = deg2rad($lat2 - $lat1);
        $Δλ = deg2rad($lon2 - $lon1);

        $a = sin($Δφ/2) * sin($Δφ/2) +
             cos($φ1) * cos($φ2) *
             sin($Δλ/2) * sin($Δλ/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

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