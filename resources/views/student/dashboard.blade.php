@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Absensi</h1>
            <p class="text-gray-600">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-clock mr-1"></i>Waktu sekarang: <span id="currentTime" class="font-semibold">{{ now()->format('H:i:s') }}</span>
            </p>
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
                        <p class="text-sm text-gray-600">Minimal: 16:00 WIB</p>
                    </div>
                </div>

                @if($todayAttendance && $todayAttendance->check_out)
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center text-purple-700">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold">Sudah Absen</p>
                                <p class="text-sm">{{ $todayAttendance->check_out->format('H:i') }} WIB</p>
                                @if($todayAttendance->notes)
                                    <p class="text-xs mt-1 text-purple-600">{{ $todayAttendance->notes }}</p>
                                @endif
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

<!-- Modal for Camera - IMPROVED UI -->
<div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 relative">
        <!-- Close Button -->
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <!-- Status Banner -->
        <div id="statusBanner" class="mb-4 p-4 rounded-lg bg-blue-50 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="animate-pulse mr-3">
                    <i class="fas fa-camera text-2xl text-blue-600"></i>
                </div>
                <div>
                    <p class="font-bold text-blue-900" id="modalTitle">Sedang Memproses Absen Masuk</p>
                    <p class="text-sm text-blue-700" id="modalSubtitle">Posisikan wajah Anda di depan kamera</p>
                </div>
            </div>
        </div>

        <!-- Camera Preview -->
        <div class="bg-gray-900 rounded-lg overflow-hidden mb-4 relative" style="height: 480px;">
            <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
            <canvas id="canvas" style="display: none;"></canvas>
            
            <!-- Face Detection Indicator -->
            <div id="faceDetected" class="absolute top-4 left-4 right-4 hidden">
                <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold">Wajah Terdeteksi!</p>
                            <p class="text-sm">Klik tombol di bawah untuk absen</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div id="loading" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-spinner fa-spin text-4xl mb-3"></i>
                    <p class="font-semibold">Memuat Model AI...</p>
                    <p class="text-sm text-gray-400 mt-1">Harap tunggu sebentar</p>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-yellow-800 font-semibold mb-2">
                <i class="fas fa-info-circle mr-2"></i>Petunjuk:
            </p>
            <ul class="text-xs text-yellow-700 space-y-1 ml-6 list-disc">
                <li>Pastikan telah aktifkan fitur kamera</li>
                <li>Pastikan wajah Anda terlihat jelas</li>
                <li>Pencahayaan harus cukup terang</li>
                <li>Lepas masker dan kacamata</li>
                <li>Tunggu hingga wajah terdeteksi (kotak hijau muncul)</li>
            </ul>
        </div>

        <!-- Early Checkout Notice (only shown on checkout) -->
        <div id="earlyCheckoutNotice" class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4 hidden">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-orange-500 text-2xl mr-3 mt-1"></i>
                <div class="flex-1">
                    <p class="font-bold text-orange-900 mb-2">Anda Pulang Lebih Awal</p>
                    <p class="text-sm text-orange-800 mb-3">Jam minimal pulang adalah <strong id="minCheckoutTime">16:00</strong>. Harap isi alasan pulang cepat:</p>
                    <textarea id="earlyReason" 
                        class="w-full px-3 py-2 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                        rows="3"
                        placeholder="Contoh: Izin sakit / Ada keperluan keluarga / Surat izin dokter"
                        minlength="10"
                        maxlength="500"></textarea>
                    <p class="text-xs text-orange-600 mt-1">
                        <span id="reasonLength">0</span>/500 karakter (minimal 10)
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <button id="captureBtn" onclick="captureAndSubmit()" disabled
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-lg transition disabled:bg-gray-300 disabled:cursor-not-allowed">
            <i class="fas fa-camera mr-2"></i>Ambil Foto & Absen
        </button>

        <!-- Progress Info -->
        <div id="progressInfo" class="mt-3 text-center text-sm text-gray-600 hidden">
            <i class="fas fa-spinner fa-spin mr-2"></i>Sedang memproses absensi...
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
<script>
// Update real-time clock
setInterval(() => {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID');
    document.getElementById('currentTime').textContent = timeStr;
}, 1000);

// Face recognition variables
const SCHOOL_LAT = -6.2706589; 
const SCHOOL_LNG = 106.9593685; 
const MAX_DISTANCE = 50000; // 50km untuk dev, ganti 100 untuk production

let modelsLoaded = false;
let stream = null;
let detectionInterval = null;
let currentType = null;
let userLat = null;
let userLng = null;

// Load models
(async function initModels() {
    try {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        modelsLoaded = true;
        console.log('✅ Models loaded');
    } catch (error) {
        console.error('❌ Model load error:', error);
        alert('Gagal memuat model AI. Refresh halaman.');
    }
})();

// Early reason character counter
document.addEventListener('DOMContentLoaded', function() {
    const earlyReason = document.getElementById('earlyReason');
    if (earlyReason) {
        earlyReason.addEventListener('input', function() {
            document.getElementById('reasonLength').textContent = this.value.length;
        });
    }
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
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

async function checkLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject('Browser tidak support GPS');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, userLat, userLng);
                
                document.getElementById('distanceInfo').textContent = Math.round(distance);
                
                if (distance <= MAX_DISTANCE) {
                    document.getElementById('locationWarning').classList.add('hidden');
                    resolve(true);
                } else {
                    document.getElementById('locationWarning').classList.remove('hidden');
                    reject(`Jarak ${Math.round(distance)}m. Maksimal ${MAX_DISTANCE}m.`);
                }
            },
            (error) => {
                reject('Tidak dapat mengakses lokasi. Aktifkan GPS.');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
}

async function openCheckInModal() {
    currentType = 'checkin';
    document.getElementById('modalTitle').textContent = 'Sedang Memproses Absen Masuk';
    document.getElementById('modalSubtitle').textContent = 'Posisikan wajah Anda di depan kamera';
    document.getElementById('earlyCheckoutNotice').classList.add('hidden');
    await openModal();
}

async function openCheckOutModal() {
    currentType = 'checkout';
    
    // Check apakah pulang lebih awal
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const currentMinutes = hours * 60 + minutes;
    const minCheckoutMinutes = 16 * 60; // 16:00 = 960 menit
    
    const isEarly = currentMinutes < minCheckoutMinutes;
    
    if (isEarly) {
        document.getElementById('modalTitle').textContent = 'Absen Pulang (Lebih Awal)';
        document.getElementById('modalSubtitle').textContent = 'Anda pulang sebelum jam 16:00';
        document.getElementById('earlyCheckoutNotice').classList.remove('hidden');
        document.getElementById('statusBanner').classList.remove('bg-blue-50', 'border-blue-500');
        document.getElementById('statusBanner').classList.add('bg-orange-50', 'border-orange-500');
    } else {
        document.getElementById('modalTitle').textContent = 'Sedang Memproses Absen Pulang';
        document.getElementById('modalSubtitle').textContent = 'Posisikan wajah Anda di depan kamera';
        document.getElementById('earlyCheckoutNotice').classList.add('hidden');
    }
    
    await openModal();
}

async function openModal() {
    const modal = document.getElementById('cameraModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Check location
    try {
        await checkLocation();
    } catch (error) {
        alert(error);
        closeModal();
        return;
    }

    // Wait for models
    if (!modelsLoaded) {
        document.getElementById('loading').classList.remove('hidden');
        let attempts = 0;
        while (!modelsLoaded && attempts < 30) {
            await new Promise(resolve => setTimeout(resolve, 1000));
            attempts++;
        }
        if (!modelsLoaded) {
            alert('Model AI gagal dimuat. Refresh halaman.');
            closeModal();
            return;
        }
    }
    document.getElementById('loading').classList.add('hidden');

    // Start camera
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
        });
        document.getElementById('video').srcObject = stream;
        await new Promise(resolve => {
            document.getElementById('video').onloadedmetadata = resolve;
        });
        startFaceDetection();
    } catch (err) {
        alert('Tidak dapat mengakses kamera.');
        closeModal();
    }
}

let lastDetectionTime = 0;
const DETECTION_THROTTLE = 500;

function startFaceDetection() {
    const video = document.getElementById('video');
    const captureBtn = document.getElementById('captureBtn');
    const faceDetected = document.getElementById('faceDetected');

    detectionInterval = setInterval(async () => {
        const now = Date.now();
        if (now - lastDetectionTime < DETECTION_THROTTLE) return;
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
    const progressInfo = document.getElementById('progressInfo');

    // Check early reason jika checkout lebih awal
    if (currentType === 'checkout') {
        const notice = document.getElementById('earlyCheckoutNotice');
        if (!notice.classList.contains('hidden')) {
            const reason = document.getElementById('earlyReason').value.trim();
            if (reason.length < 10) {
                alert('Harap isi alasan pulang cepat minimal 10 karakter!');
                return;
            }
        }
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    progressInfo.classList.remove('hidden');

    try {
        const detection = await faceapi.detectSingleFace(
            video,
            new faceapi.TinyFaceDetectorOptions({ inputSize: 224 })
        ).withFaceLandmarks().withFaceDescriptor();

        if (!detection) {
            alert('Wajah tidak terdeteksi! Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
            progressInfo.classList.add('hidden');
            return;
        }

        canvas.width = 640;
        canvas.height = 480;
        canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
        
        const photo = canvas.toDataURL('image/jpeg', 0.8);
        const faceDescriptor = Array.from(detection.descriptor);
        
        const url = currentType === 'checkin' 
            ? '{{ route("attendance.checkin") }}' 
            : '{{ route("attendance.checkout") }}';

        const requestData = {
            face_descriptor: faceDescriptor,
            photo: photo,
            latitude: userLat,
            longitude: userLng
        };

        // Add early reason jika ada
        if (currentType === 'checkout' && !document.getElementById('earlyCheckoutNotice').classList.contains('hidden')) {
            requestData.early_reason = document.getElementById('earlyReason').value.trim();
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        });

        const result = await response.json();

        if (result.requires_reason) {
            // Show early checkout form
            document.getElementById('earlyCheckoutNotice').classList.remove('hidden');
            document.getElementById('minCheckoutTime').textContent = result.min_time;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
            progressInfo.classList.add('hidden');
            return;
        }

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Gagal absen');
        }
        
        alert(result.message);
        closeModal();
        window.location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
        progressInfo.classList.add('hidden');
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
    
    document.getElementById('captureBtn').disabled = true;
    document.getElementById('captureBtn').innerHTML = '<i class="fas fa-camera mr-2"></i>Ambil Foto & Absen';
    document.getElementById('progressInfo').classList.add('hidden');
    document.getElementById('earlyReason').value = '';
    document.getElementById('reasonLength').textContent = '0';
}

document.getElementById('cameraModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

window.addEventListener('load', () => {
    checkLocation().catch(() => {});
});
</script>
@endsection