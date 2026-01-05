<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GpsValidationService
{
    /**
     * SECURITY: Multi-layer GPS validation
     */
    public static function validate($latitude, $longitude, User $user)
    {
        // 1. Basic distance check
        $distance = self::calculateDistance($latitude, $longitude);
        $maxDistance = (int) Setting::get('max_distance_meters', 100);
        
        if ($distance > $maxDistance) {
            Log::warning('GPS: Outside radius', [
                'user_id' => $user->id,
                'distance' => round($distance, 2),
                'max' => $maxDistance,
            ]);
            
            throw new \Exception("Lokasi terlalu jauh dari sekolah ({$distance}m). Maksimal: {$maxDistance}m");
        }
        
        // 2. Coordinate precision check (prevent fake static coords)
        if (self::isSuspiciousStaticCoordinate($latitude, $longitude, $user)) {
            Log::warning('GPS: Suspicious static coordinate', [
                'user_id' => $user->id,
                'lat' => $latitude,
                'lng' => $longitude,
            ]);
            
            // Soft warning - tidak block tapi flag untuk review
            self::flagForManualReview($user, 'static_coordinate');
        }
        
        // 3. Velocity check (prevent teleportation)
        if (self::isSuspiciousVelocity($latitude, $longitude, $user)) {
            Log::warning('GPS: Suspicious velocity detected', [
                'user_id' => $user->id,
            ]);
            
            throw new \Exception('Pola pergerakan mencurigakan terdeteksi. Silakan coba lagi dalam 1 menit.');
        }
        
        // 4. Daily pattern check
        if (self::hasSuspiciousDailyPattern($latitude, $longitude, $user)) {
            Log::warning('GPS: Suspicious daily pattern', [
                'user_id' => $user->id,
            ]);
            
            self::flagForManualReview($user, 'suspicious_pattern');
        }
        
        return [
            'valid' => true,
            'distance' => round($distance, 2),
            'warnings' => [],
        ];
    }
    
    /**
     * Calculate distance dari koordinat sekolah
     */
    private static function calculateDistance($lat, $lng)
    {
        $schoolLat = (float) Setting::get('school_latitude', -6.2706589);
        $schoolLng = (float) Setting::get('school_longitude', 106.9593685);
        
        $R = 6371000; // meter
        $phi1 = deg2rad($schoolLat);
        $phi2 = deg2rad($lat);
        $deltaPhi = deg2rad($lat - $schoolLat);
        $deltaLambda = deg2rad($lng - $schoolLng);
        
        $a = sin($deltaPhi / 2) * sin($deltaPhi / 2) +
             cos($phi1) * cos($phi2) *
             sin($deltaLambda / 2) * sin($deltaLambda / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $R * $c;
    }
    
    /**
     * 🔒 SECURITY: Detect koordinat static yang mencurigakan
     * (Terlalu persis = kemungkinan fake GPS app)
     */
    private static function isSuspiciousStaticCoordinate($lat, $lng, User $user)
    {
        $recent = Attendance::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get(['latitude', 'longitude']);
        
        if ($recent->count() < 3) {
            return false; // Belum cukup data
        }
        
        $exactMatchCount = 0;
        foreach ($recent as $att) {
            // Jika koordinat PERSIS SAMA sampai 6 desimal = SUSPICIOUS
          if (abs($att->latitude - $lat) < 0.00001 && // ~1 meter tolerance
            abs($att->longitude - $lng) < 0.00001 &&
            $att->created_at->diffInMinutes(now()) < 10) { // dalam 10 menit
            $exactMatchCount++;
            }
        }
        
        // Jika 3+ dari 5 terakhir PERSIS SAMA = SANGAT MENCURIGAKAN
        return $exactMatchCount >= 3;
    }
    
    /**
     * 🔒 SECURITY: Velocity check
     * Prevent "teleportation" (coordinate jump yang tidak masuk akal)
     */
    private static function isSuspiciousVelocity($lat, $lng, User $user)
    {
        $lastAttendance = Attendance::where('user_id', $user->id)
            ->whereNotNull('latitude')
            ->latest()
            ->first();
        
        if (!$lastAttendance) {
            return false; // First time
        }
        
        $timeDiff = now()->diffInSeconds($lastAttendance->created_at);
        
        // Jika absen dalam < 60 detik, cek jarak
        if ($timeDiff < 60) {
            $distance = self::calculateDistanceBetween(
                $lastAttendance->latitude,
                $lastAttendance->longitude,
                $lat,
                $lng
            );
            
            // Jika pindah > 50m dalam < 60 detik = suspicious
            // (Kecepatan > 3 km/jam dalam area sekolah = tidak wajar)
            if ($distance > 50) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Calculate distance antara 2 koordinat
     */
    private static function calculateDistanceBetween($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000;
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $deltaPhi = deg2rad($lat2 - $lat1);
        $deltaLambda = deg2rad($lng2 - $lng1);
        
        $a = sin($deltaPhi / 2) * sin($deltaPhi / 2) +
             cos($phi1) * cos($phi2) *
             sin($deltaLambda / 2) * sin($deltaLambda / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $R * $c;
    }
    
    /**
     * 🔒 SECURITY: Detect pola harian yang mencurigakan
     */
    private static function hasSuspiciousDailyPattern($lat, $lng, User $user)
    {
        // Cek 10 absensi terakhir
        $recent = Attendance::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get(['latitude', 'longitude']);
        
        if ($recent->count() < 5) {
            return false;
        }
        
        // Hitung standard deviation (variasi koordinat)
        $latitudes = $recent->pluck('latitude')->toArray();
        $longitudes = $recent->pluck('longitude')->toArray();
        
        $latStdDev = self::standardDeviation($latitudes);
        $lngStdDev = self::standardDeviation($longitudes);
        
        // Jika std dev < 0.00001 = SANGAT TIDAK WAJAR
        // (Manusia tidak mungkin berdiri PERSIS di titik yang sama setiap hari)
        return ($latStdDev < 0.00001 && $lngStdDev < 0.00001);
    }
    
    /**
     * Calculate standard deviation
     */
    private static function standardDeviation(array $values)
    {
        $count = count($values);
        if ($count === 0) return 0;
        
        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / $count;
        
        return sqrt($variance);
    }
    
    /**
     * Flag user untuk manual review oleh admin
     */
    private static function flagForManualReview(User $user, $reason)
    {
        Cache::put(
            "gps_flag_{$user->id}_{$reason}",
            [
                'reason' => $reason,
                'timestamp' => now(),
                'count' => Cache::get("gps_flag_{$user->id}_{$reason}_count", 0) + 1,
            ],
            now()->addDays(7)
        );
        
        // Log untuk admin dashboard
        Log::channel('security')->warning('GPS anomaly flagged for review', [
            'user_id' => $user->id,
            'reason' => $reason,
            'count' => Cache::get("gps_flag_{$user->id}_{$reason}_count", 0),
        ]);
    }
}