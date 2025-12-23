<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Location Coordinates
    |--------------------------------------------------------------------------
    | Koordinat lokasi sekolah untuk validasi radius absensi
    */
    'school_location' => [
        'latitude' => env('SCHOOL_LATITUDE', -6.2706589),
        'longitude' => env('SCHOOL_LONGITUDE', 106.9593685),
        'max_distance' => env('SCHOOL_MAX_DISTANCE', 100), // dalam meter
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance Time Limits
    |--------------------------------------------------------------------------
    | Batas waktu untuk absensi masuk dan pulang
    */
    'time_limits' => [
        'check_in_limit' => env('CHECK_IN_TIME_LIMIT', '07:30'),
        'check_out_min' => env('CHECK_OUT_TIME_MIN', '14:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Face Recognition Settings
    |--------------------------------------------------------------------------
    | Pengaturan untuk face recognition
    */
    'face_recognition' => [
        'threshold' => env('FACE_MATCH_THRESHOLD', 0.6),
        'model_url' => 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model',
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    | Pengaturan untuk optimasi performa
    */
    'performance' => [
        'location_cache_duration' => 60, // detik
        'detection_throttle' => 500, // milliseconds
        'image_quality' => 0.8, // JPEG quality 0-1
        'max_image_dimension' => 640, // pixels
    ],
];