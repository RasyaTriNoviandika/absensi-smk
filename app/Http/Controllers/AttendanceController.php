<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use App\Services\FaceRecognitionService;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $user = auth()->user();
        
        // Check if already checked in today
        if ($user->hasCheckedInToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini.'
            ], 400);
        }

        $validated = $request->validate([
            'face_descriptor' => 'required|array',
            'photo' => 'required|string', // Base64 image
        ]);

        // Verify face match
        $storedDescriptor = json_decode($user->face_descriptor, true);
        $threshold = Setting::get('face_match_threshold', 0.6);
        
        if (!FaceRecognitionService::isMatch($validated['face_descriptor'], $storedDescriptor, $threshold)) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak cocok. Silakan coba lagi.'
            ], 400);
        }

        // Save photo
        $photoPath = $this->saveBase64Image($validated['photo'], 'checkin');

        // Determine status (hadir or terlambat)
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
            'message' => 'Absen masuk berhasil!',
            'status' => $status,
            'time' => $checkInTime->format('H:i'),
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        
        // Check if hasn't checked in yet
        if (!$user->hasCheckedInToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus absen masuk terlebih dahulu.'
            ], 400);
        }

        // Check if already checked out
        if ($user->hasCheckedOutToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini.'
            ], 400);
        }

        $validated = $request->validate([
            'face_descriptor' => 'required|array',
            'photo' => 'required|string',
        ]);

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
        $photoPath = $this->saveBase64Image($validated['photo'], 'checkout');

        // Update attendance
        $attendance = $user->todayAttendance();
        $attendance->update([
            'check_out' => now(),
            'check_out_photo' => $photoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil!',
            'time' => now()->format('H:i'),
        ]);
    }

    public function history()
    {
        $user = auth()->user();
        $attendances = $user->attendances()
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('student.history', compact('attendances'));
    }

    private function saveBase64Image($base64String, $prefix)
    {
        // Remove data:image/png;base64, part
        $image = str_replace('data:image/png;base64,', '', $base64String);
        $image = str_replace(' ', '+', $image);
        $imageName = $prefix . '_' . time() . '_' . uniqid() . '.png';
        
        \Storage::disk('public')->put('attendance/' . $imageName, base64_decode($image));
        
        return 'attendance/' . $imageName;
    }
}
