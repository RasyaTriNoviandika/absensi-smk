{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}</h1>
                    <p class="text-gray-600">{{ auth()->user()->class }} • NISN: {{ auth()->user()->nisn }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                    <p class="text-2xl font-bold text-blue-600" id="current-time"></p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Hadir Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['hadir'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Terlambat Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['terlambat'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Alpha Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['alpha'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Check In Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-sign-in-alt text-blue-600 mr-2"></i>Absen Masuk
                </h3>
                
                @if($todayAttendance && $todayAttendance->check_in)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 font-semibold">Sudah Absen Masuk</p>
                                <p class="text-2xl font-bold text-green-800">{{ $todayAttendance->check_in->format('H:i') }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Status: 
                                    <span class="font-semibold {{ $todayAttendance->check_in_status == 'hadir' ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ strtoupper($todayAttendance->check_in_status) }}
                                    </span>
                                </p>
                            </div>
                            <i class="fas fa-check-circle text-4xl text-green-500"></i>
                        </div>
                    </div>
                @else
                    <button onclick="openCheckInModal()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-200">
                        <i class="fas fa-camera mr-2"></i>Scan Wajah untuk Absen Masuk
                    </button>
                    <p class="text-sm text-gray-500 mt-3 text-center">
                        <i class="fas fa-info-circle mr-1"></i>Batas waktu: 07:30 WIB
                    </p>
                @endif
            </div>

            <!-- Check Out Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-sign-out-alt text-purple-600 mr-2"></i>Absen Pulang
                </h3>
                
                @if($todayAttendance && $todayAttendance->check_out)
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-600 font-semibold">Sudah Absen Pulang</p>
                                <p class="text-2xl font-bold text-purple-800">{{ $todayAttendance->check_out->format('H:i') }}</p>
                            </div>
                            <i class="fas fa-check-circle text-4xl text-purple-500"></i>
                        </div>
                    </div>
                @elseif($todayAttendance && $todayAttendance->check_in)
                    <button onclick="openCheckOutModal()" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-200">
                        <i class="fas fa-camera mr-2"></i>Scan Wajah untuk Absen Pulang
                    </button>
                @else
                    <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 text-center">
                        <i class="fas fa-lock text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">Absen masuk terlebih dahulu</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Attendance History -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-history text-gray-600 mr-2"></i>Riwayat Absensi (7 Hari Terakhir)
                </h3>
                <a href="{{ route('attendance.history') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
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
                        @forelse($recentAttendances as $attendance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $attendance->date->isoFormat('dddd, D MMM Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance->status == 'hadir')
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">HADIR</span>
                                    @elseif($attendance->status == 'terlambat')
                                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">TERLAMBAT</span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">ALPHA</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>Belum ada riwayat absensi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Face Recognition Modal -->
<div id="faceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Scan Wajah</h3>
            <button onclick="closeFaceModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div id="cameraSection">
            <div class="relative bg-gray-900 rounded-lg overflow-hidden mb-4" style="height: 480px;">
                <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                <canvas id="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
            </div>

            <div id="instructions" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Instruksi:</strong> Posisikan wajah Anda di tengah frame. Pastikan pencahayaan cukup dan wajah terlihat jelas.
                </p>
            </div>

            <div id="statusMessage" class="hidden mb-4"></div>

            <button id="captureBtn" onclick="captureAndRecognize()" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                <i class="fas fa-camera mr-2"></i>Capture & Verify
            </button>
        </div>

        <div id="loadingSection" class="hidden text-center py-8">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent mb-4"></div>
            <p class="text-gray-600">Memproses wajah Anda...</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/face-api.js/0.22.2/face-api.min.js"></script>
<script>
let video, canvas, currentAction;
let modelsLoaded = false;

// Load face-api models
async function loadModels() {
    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    modelsLoaded = true;
}

// Update current time
function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}
setInterval(updateTime, 1000);
updateTime();

// Open modal for check in/out
async function openCheckInModal() {
    currentAction = 'checkin';
    await openFaceModal('Absen Masuk');
}

async function openCheckOutModal() {
    currentAction = 'checkout';
    await openFaceModal('Absen Pulang');
}

async function openFaceModal(title) {
    if (!modelsLoaded) {
        alert('Loading face recognition models...');
        await loadModels();
    }

    document.getElementById('modalTitle').textContent = title;
    document.getElementById('faceModal').classList.remove('hidden');
    
    video = document.getElementById('video');
    canvas = document.getElementById('overlay');

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: 640, height: 480 } 
        });
        video.srcObject = stream;
        video.play();

        // Start face detection overlay
        detectFaces();
    } catch (err) {
        showError('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
    }
}

function closeFaceModal() {
    const video = document.getElementById('video');
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
    }
    document.getElementById('faceModal').classList.add('hidden');
}

// Real-time face detection overlay
async function detectFaces() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('overlay');
    
    if (!video || !canvas) return;

    const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
    faceapi.matchDimensions(canvas, displaySize);

    setInterval(async () => {
        if (video.paused || video.ended) return;

        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks();

        const resizedDetections = faceapi.resizeResults(detections, displaySize);
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
        faceapi.draw.drawDetections(canvas, resizedDetections);
    }, 100);
}

// Capture and recognize face
async function captureAndRecognize() {
    const video = document.getElementById('video');
    const captureBtn = document.getElementById('captureBtn');
    
    captureBtn.disabled = true;
    captureBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

    try {
        // Detect face
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detection) {
            showError('Wajah tidak terdeteksi. Pastikan wajah Anda berada di tengah frame.');
            captureBtn.disabled = false;
            captureBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture & Verify';
            return;
        }

        // Capture image
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        tempCanvas.getContext('2d').drawImage(video, 0, 0);
        const photoData = tempCanvas.toDataURL('image/png');

        // Send to server
        const endpoint = currentAction === 'checkin' ? '{{ route("attendance.checkin") }}' : '{{ route("attendance.checkout") }}';
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                face_descriptor: Array.from(detection.descriptor),
                photo: photoData
            })
        });

        const result = await response.json();

        if (result.success) {
            showSuccess(result.message);
            setTimeout(() => {
                closeFaceModal();
                window.location.reload();
            }, 2000);
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Terjadi kesalahan. Silakan coba lagi.');
    }

    captureBtn.disabled = false;
    captureBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture & Verify';
}

function showError(message) {
    const statusDiv = document.getElementById('statusMessage');
    statusDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-4';
    statusDiv.innerHTML = `<p class="text-sm text-red-800"><i class="fas fa-exclamation-circle mr-2"></i>${message}</p>`;
    statusDiv.classList.remove('hidden');
}

function showSuccess(message) {
    const statusDiv = document.getElementById('statusMessage');
    statusDiv.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-4';
    statusDiv.innerHTML = `<p class="text-sm text-green-800"><i class="fas fa-check-circle mr-2"></i>${message}</p>`;
    statusDiv.classList.remove('hidden');
}

// Load models on page load
window.addEventListener('load', () => {
    loadModels();
});
</script>
@endpush