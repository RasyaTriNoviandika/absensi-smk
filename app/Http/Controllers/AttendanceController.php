<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
public function checkIn(Request $request)
{
    $user = auth()->user();
    
    // OPTIMASI: Check dengan exists() lebih cepat dari hasCheckedInToday()
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

    // PERBAIKAN: Validasi lokasi di server side
    $schoolLat = -6.2706589;
    $schoolLng = 106.9593685;
    $maxDistance = 50000; // meter
    
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

    // Verify face match
    $storedDescriptor = json_decode($user->face_descriptor, true);
    $threshold = Setting::get('face_match_threshold', 0.6);
    
    if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
        return response()->json([
            'success' => false,
            'message' => 'Wajah tidak cocok. Silakan coba lagi.'
        ], 400);
    }

    // Save photo - OPTIMASI: Async storage
    try {
        $photoPath = $this->saveBase64Image($validated['photo'], 'checkin');
    } catch (\Exception $e) {
        \Log::error('Photo save failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan foto. Coba lagi.'
        ], 500);
    }

    // Determine status
    $checkInTime = now();
    $limitTime = Setting::get('check_in_time_limit', '07:30');
    $status = $checkInTime->format('H:i') <= $limitTime ? 'hadir' : 'terlambat';

    // Create attendance record
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => today(),
        'check_in' => $checkInTime,
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
    
    // Check if hasn't checked in yet
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

    $validated = $request->validate([
        'face_descriptor' => 'required|array',
        'photo' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    // PERBAIKAN: Validasi lokasi di server side
    $schoolLat = -6.2706589;
    $schoolLng = 106.9593685;
    $maxDistance = 500000;
    
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
    $storedDescriptor = json_decode($user->face_descriptor, true);
    $threshold = Setting::get('face_match_threshold', 0.6);
    
    if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
        return response()->json([
            'success' => false,
            'message' => 'Wajah tidak cocok. Silakan coba lagi.'
        ], 400);
    }

    // Save photo
    try {
        $photoPath = $this->saveBase64Image($validated['photo'], 'checkout');
    } catch (\Exception $e) {
        \Log::error('Photo save failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan foto. Coba lagi.'
        ], 500);
    }

    // Update attendance
    $attendance->update([
        'check_out' => now(),
        'check_out_photo' => $photoPath,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Absen pulang berhasil! Hati-hati di jalan.',
        'time' => now()->format('H:i'),
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

    return $R * $c; // Distance in meters
}

private function saveBase64Image($base64String, $prefix)
{
    // Remove data:image/png;base64, or data:image/jpeg;base64, part
    $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
    $image = str_replace(' ', '+', $image);
    $imageName = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
    
    // OPTIMASI: Gunakan Storage facade
    $path = 'attendance/' . $imageName;
    \Storage::disk('public')->put($path, base64_decode($image));
    
    return $path;
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