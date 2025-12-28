@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Absensi</h1>
            <p class="text-gray-600">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Hadir Bulan Ini</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['hadir'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Terlambat Bulan Ini</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['terlambat'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Alpha Bulan Ini</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['alpha'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Warning -->
        <div id="locationWarning" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 hidden">
            <div class="flex">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 mt-1"></i>
                <div>
                    <p class="font-bold text-gray-800">Anda berada di luar radius sekolah!</p>
                    <p class="text-sm text-gray-700">Absensi hanya bisa dilakukan dalam radius 100 meter dari sekolah.</p>
                    <p class="text-xs text-gray-600 mt-1">Jarak Anda: <span id="distanceInfo">-</span> meter</p>
                </div>
            </div>
        </div>

        <!-- Attendance Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Check In Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-sign-in-alt text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Absen Masuk</h3>
                        <p class="text-sm text-gray-600">Batas: 07:30 WIB</p>
                    </div>
                </div>

                @if($todayAttendance && $todayAttendance->check_in)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold">Sudah Absen</p>
                                <p class="text-sm">{{ $todayAttendance->check_in->format('H:i') }} WIB</p>
                                @if($todayAttendance->check_in_status == 'terlambat')
                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                        TERLAMBAT
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <button onclick="openCheckInModal()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-camera mr-2"></i>Scan Wajah untuk Absen Masuk
                    </button>
                @endif
            </div>

            <!-- Check Out Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-sign-out-alt text-2xl text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Absen Pulang</h3>
                        <p class="text-sm text-gray-600">Minimal: 14:00 WIB</p>
                    </div>
                </div>

                @if($todayAttendance && $todayAttendance->check_out)
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center text-purple-700">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold">Sudah Absen</p>
                                <p class="text-sm">{{ $todayAttendance->check_out->format('H:i') }} WIB</p>
                            </div>
                        </div>
                    </div>
                @elseif($todayAttendance)
                    <button onclick="openCheckOutModal()" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-camera mr-2"></i>Scan Wajah untuk Absen Pulang
                    </button>
                @else
                    <button disabled 
                        class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>Absen Masuk Dulu
                    </button>
                @endif
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Riwayat 7 Hari Terakhir</h3>
                    <a href="{{ route('attendance.history') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentAttendances as $att)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $att->date->isoFormat('dddd, D MMM') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $att->check_in ? $att->check_in->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $att->check_out ? $att->check_out->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($att->status == 'hadir')
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">HADIR</span>
                                    @elseif($att->status == 'terlambat')
                                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">TERLAMBAT</span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">ALPHA</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada data absensi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Camera -->
<div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Scan Wajah</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div class="bg-gray-900 rounded-lg overflow-hidden mb-4 relative" style="height: 480px;">
            <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
            <canvas id="canvas" style="display: none;"></canvas>
            
            <div id="faceDetected" class="absolute bottom-4 left-4 right-4 hidden">
                <div class="bg-green-500 text-white px-4 py-2 rounded-lg text-center font-semibold">
                    <i class="fas fa-check-circle mr-2"></i>Wajah Terdeteksi!
                </div>
            </div>

            <div id="loading" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-spinner fa-spin text-4xl mb-3"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>

        <div id="locationCheck" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
            <p class="text-sm text-blue-800">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Checking your location...
            </p>
        </div>

        <button id="captureBtn" onclick="captureAndSubmit()" disabled
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:bg-gray-300 disabled:cursor-not-allowed">
            <i class="fas fa-camera mr-2"></i>Ambil Foto & Absen
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
<script>
// FIXED: KOORDINAT SEKOLAH - RADIUS 100 METER
const SCHOOL_LAT = -6.2706589; 
const SCHOOL_LNG = 106.9593685; 
const MAX_DISTANCE = 500000; // FIXED: 100 meter (bukan 50000!)

let modelsLoaded = false;
let stream = null;
let detectionInterval = null;
let currentType = null;
let userLat = null;
let userLng = null;

// Load face-api models
(async function initModels() {
    try {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
        console.log('Loading face-api models...');
        
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        
        modelsLoaded = true;
        console.log('✅ Models loaded successfully');
    } catch (error) {
        console.error('❌ Failed to load models:', error);
        alert('Gagal memuat model deteksi wajah. Refresh halaman dan pastikan koneksi internet stabil.');
    }
})();

function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) {
        console.error('❌ CSRF token not found in meta tag');
    }
    return token || '';
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3;
    const φ1 = lat1 * Math.PI/180;
    const φ2 = lat2 * Math.PI/180;
    const Δφ = (lat2-lat1) * Math.PI/180;
    const Δλ = (lon2-lon1) * Math.PI/180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c;
}

let locationCheckCache = null;
let locationCheckTime = null;
const LOCATION_CACHE_DURATION = 60000;

async function checkLocation() {
    if (locationCheckCache && locationCheckTime && 
        (Date.now() - locationCheckTime < LOCATION_CACHE_DURATION)) {
        return locationCheckCache;
    }

    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject('Geolocation tidak didukung browser Anda');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                
                const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, userLat, userLng);
                
                console.log(`📍 Distance from school: ${Math.round(distance)} meters`);
                document.getElementById('distanceInfo').textContent = Math.round(distance);
                
                if (distance <= MAX_DISTANCE) {
                    document.getElementById('locationWarning').classList.add('hidden');
                    locationCheckCache = true;
                    locationCheckTime = Date.now();
                    resolve(true);
                } else {
                    document.getElementById('locationWarning').classList.remove('hidden');
                    locationCheckCache = false;
                    locationCheckTime = Date.now();
                    reject(`Anda berada ${Math.round(distance)} meter dari sekolah. Jarak maksimal: ${MAX_DISTANCE} meter.`);
                }
            },
            (error) => {
                let errorMsg = 'Tidak dapat mengakses lokasi.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = 'Izin lokasi ditolak. Aktifkan GPS dan izinkan akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                        break;
                    case error.TIMEOUT:
                        errorMsg = 'Request lokasi timeout. Coba lagi.';
                        break;
                }
                console.error('❌ Geolocation error:', errorMsg);
                reject(errorMsg);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
}

async function openCheckInModal() {
    currentType = 'checkin';
    document.getElementById('modalTitle').textContent = 'Absen Masuk';
    await openModal();
}

async function openCheckOutModal() {
    currentType = 'checkout';
    document.getElementById('modalTitle').textContent = 'Absen Pulang';
    await openModal();
}

async function openModal() {
    const modal = document.getElementById('cameraModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('locationCheck').classList.remove('hidden');
    try {
        await checkLocation();
        document.getElementById('locationCheck').classList.add('hidden');
    } catch (error) {
        alert(error);
        closeModal();
        return;
    }

    if (!modelsLoaded) {
        document.getElementById('loading').classList.remove('hidden');
        let attempts = 0;
        while (!modelsLoaded && attempts < 30) {
            await new Promise(resolve => setTimeout(resolve, 1000));
            attempts++;
        }
        if (!modelsLoaded) {
            alert('Gagal memuat model. Refresh halaman.');
            closeModal();
            return;
        }
    }
    document.getElementById('loading').classList.add('hidden');

    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'user',
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        });
        document.getElementById('video').srcObject = stream;
        
        const video = document.getElementById('video');
        await new Promise(resolve => {
            video.onloadedmetadata = resolve;
        });
        
        startFaceDetection();
    } catch (err) {
        console.error('❌ Camera error:', err);
        alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
        closeModal();
    }
}

let lastDetectionTime = 0;
const DETECTION_THROTTLE = 1000; // FIXED: 1 detik (lebih hemat resource)

function startFaceDetection() {
    const video = document.getElementById('video');
    const captureBtn = document.getElementById('captureBtn');
    const faceDetected = document.getElementById('faceDetected');

    detectionInterval = setInterval(async () => {
        const now = Date.now();
        if (now - lastDetectionTime < DETECTION_THROTTLE) {
            return;
        }
        lastDetectionTime = now;

        if (!modelsLoaded || video.readyState !== 4) return;

        try {
            const detection = await faceapi.detectSingleFace(
                video,
                new faceapi.TinyFaceDetectorOptions({ inputSize: 224 })
            ).withFaceLandmarks().withFaceDescriptor();

            if (detection) {
                faceDetected.classList.remove('hidden');
                captureBtn.disabled = false;
            } else {
                faceDetected.classList.add('hidden');
                captureBtn.disabled = true;
            }
        } catch (error) {
            console.error('Detection error:', error);
        }
    }, DETECTION_THROTTLE);
}

async function captureAndSubmit() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const btn = document.getElementById('captureBtn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

    try {
        console.log('🎯 Starting capture process...');
        
        const detection = await faceapi.detectSingleFace(
            video,
            new faceapi.TinyFaceDetectorOptions({ inputSize: 224 })
        ).withFaceLandmarks().withFaceDescriptor();

        if (!detection) {
            alert('Wajah tidak terdeteksi. Pastikan wajah Anda menghadap kamera dengan pencahayaan cukup.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
            return;
        }

        console.log('✅ Face detected');

        canvas.width = 640;
        canvas.height = 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, 640, 480);
        
        const photo = canvas.toDataURL('image/jpeg', 0.8);
        const faceDescriptor = Array.from(detection.descriptor);
        
        const url = currentType === 'checkin' 
            ? '{{ route("attendance.checkin") }}' 
            : '{{ route("attendance.checkout") }}';

        console.log('📤 Submitting to:', url);
        console.log('📊 Data:', {
            type: currentType,
            face_descriptor_length: faceDescriptor.length,
            photo_size: photo.length,
            latitude: userLat,
            longitude: userLng
        });

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                face_descriptor: faceDescriptor,
                photo: photo,
                latitude: userLat,
                longitude: userLng
            })
        });

        console.log('📥 Response status:', response.status);

        let result;
        try {
            result = await response.json();
            console.log('📋 Server response:', result);
        } catch (e) {
            console.error('❌ Failed to parse JSON:', e);
            throw new Error('Server returned invalid response');
        }

        if (!response.ok) {
            throw new Error(result.message || `Server error (${response.status})`);
        }
        
        if (result.success) {
            alert(result.message);
            closeModal();
            window.location.reload();
        } else {
            alert(result.message || 'Gagal melakukan absensi');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
        }
    } catch (error) {
        console.error('❌ Submit error:', error);
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
    }
}

function closeModal() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }
    
    const modal = document.getElementById('cameraModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    const captureBtn = document.getElementById('captureBtn');
    captureBtn.disabled = true;
    captureBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
}

// FIXED: Modal close on outside click
document.getElementById('cameraModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

window.addEventListener('load', () => {
    checkLocation().catch(() => {
        // Silent fail on page load
    });
});
</script>
@endsection