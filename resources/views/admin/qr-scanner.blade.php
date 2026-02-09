@extends('layouts.admin')

@section('title', 'QR Scanner')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-qrcode text-blue-600 mr-2"></i>QR Code Scanner
        </h1>
        <p class="text-gray-600">Scan QR Code siswa - Pilih mode Camera atau Upload</p>
    </div>

    <!-- MODE SELECTOR -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-2 gap-3">
            <button onclick="switchMode('camera')" id="btnModeCamera"
                class="py-3 px-4 rounded-lg font-semibold transition border-2 border-blue-600 bg-blue-600 text-white">
                <i class="fas fa-camera mr-2"></i>Scan dengan Camera
            </button>
            <button onclick="switchMode('upload')" id="btnModeUpload"
                class="py-3 px-4 rounded-lg font-semibold transition border-2 border-gray-300 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-upload mr-2"></i>Upload Foto QR
            </button>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- LEFT: Scanner Area -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-camera text-blue-600 mr-2"></i>Scanner
            </h3>

            <!-- MODE 1: CAMERA SCAN -->
            <div id="cameraMode">
                {{-- <button onclick="enableSound()" 
                style="background:#16a34a;color:white;padding:10px 16px;border-radius:8px;margin-bottom:10px">
                🔊 Aktifkan Suara Scanner
            </button> --}}

                <div class="bg-gray-900 rounded-lg overflow-hidden mb-4 relative" style="height: 400px;">
                    <!-- Camera Video (Non-mirrored) -->
                    <video id="qrVideo" autoplay playsinline 
                        class="w-full h-full object-cover">
                    </video>
                    
                    <!-- Scan Frame Guide -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-64 h-64 border-4 border-blue-400 rounded-lg relative">
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-400"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-400"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-400"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-400"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <p class="text-white text-sm font-semibold bg-black bg-opacity-50 px-3 py-1 rounded">
                                    Arahkan QR ke area ini
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="scanIndicator" class="absolute inset-0 border-4 border-transparent pointer-events-none transition-all duration-300"></div>
                    
                    <div id="loading" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                            <p class="font-semibold">Initializing camera...</p>
                        </div>
                    </div>

                    <div id="pausedState" class="absolute inset-0 bg-black bg-opacity-75 hidden items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-pause-circle text-5xl mb-3"></i>
                            <p class="font-semibold text-lg mb-2">Scanner Paused</p>
                            <button onclick="resumeScanning()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                                <i class="fas fa-play mr-2"></i>Scan Lagi
                            </button>
                        </div>
                    </div>
                    
                    <!-- Error State -->
                    <div id="errorState" class="absolute inset-0 bg-black bg-opacity-75 hidden items-center justify-center">
                        <div class="text-center text-white px-4">
                            <i class="fas fa-exclamation-triangle text-5xl mb-3 text-yellow-500"></i>
                            <p class="font-semibold text-lg mb-2">Camera Error</p>
                            <p id="errorMessage" class="text-sm mb-4">Gagal mengakses kamera</p>
                            <button onclick="retryCamera()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                                <i class="fas fa-redo mr-2"></i>Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODE 2: UPLOAD IMAGE -->
            <div id="uploadMode" class="hidden">
                <div class="border-4 border-dashed border-gray-300 rounded-lg p-8 text-center mb-4 hover:border-blue-400 transition cursor-pointer"
                    onclick="document.getElementById('qrImageInput').click()">
                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                    <p class="text-gray-700 font-semibold mb-2">Upload Foto QR Code</p>
                    <p class="text-sm text-gray-500">Klik atau drag & drop foto QR dari siswa</p>
                    <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG (Max 5MB)</p>
                </div>

                <input type="file" id="qrImageInput" accept="image/*" class="hidden" onchange="handleImageUpload(event)">

                <!-- Preview Upload -->
                <div id="uploadPreview" class="hidden mb-4">
                    <img id="uploadedImage" class="w-full h-64 object-contain rounded border-2 border-gray-300 mb-2">
                    <button onclick="clearUpload()" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                        <i class="fas fa-trash mr-1"></i>Hapus & Upload Ulang
                    </button>
                </div>

                <!-- Processing Indicator -->
                <div id="processingUpload" class="hidden text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
                    <p class="text-gray-700 font-semibold">Membaca QR Code...</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <button id="btnCheckIn" onclick="unlockAudio(); setMode('checkin')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Absen Masuk
                </button>
                <button id="btnCheckOut" onclick="unlockAudio(); setMode('checkout')"
                    class="bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Absen Pulang
                </button>
            </div>

            <!-- Status Info -->
            <div class="bg-gray-50 border border-gray-300 rounded p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">
                            <strong>Mode:</strong> <span id="scanMode" class="text-blue-600 font-semibold">Standby</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Status:</strong> <span id="statusText" class="text-gray-800">Idle</span>
                        </p>
                    </div>
                    <button onclick="toggleScanner()" id="toggleBtn"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-semibold">
                        <i class="fas fa-stop mr-1"></i>Stop
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT: Recent Scans -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-history text-green-600 mr-2"></i>Scan Terakhir
            </h3>
            <div id="recentScans" class="space-y-3 max-h-96 overflow-y-auto">
                <p class="text-gray-500 text-center py-8">Belum ada scan</p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.monitoring') }}" 
           class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Monitoring
        </a>
    </div>
</div>

<!-- Audio Feedback -->
{{-- <audio id="successSound" preload="auto">
    <source src="{{ asset('sounds/success.mp3') }}" type="audio/mpeg">
</audio>
<audio id="errorSound" preload="auto">
    <source src="{{ asset('sounds/error.mp3') }}" type="audio/mpeg">
</audio>
<audio id="scanningSound" preload="auto">
    <source src="{{ asset('sounds/scan.mp3') }}" type="audio/mpeg">
</audio> --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>

let audioUnlocked = false;

function unlockAudio() {
    if (audioUnlocked) return;

    const sounds = [
        document.getElementById('successSound'),
        document.getElementById('errorSound'),
        document.getElementById('scanningSound')
    ];

    sounds.forEach(audio => {
        if (!audio) return;
        audio.volume = 0.01;
        audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
        }).catch(() => {});
    });

    audioUnlocked = true;
    console.log("🔓 Audio unlocked by user interaction");
}

let soundEnabled = false;

function enableSound() {
    const success = document.getElementById('successSound');
    const error = document.getElementById('errorSound');
    const scan = document.getElementById('scanningSound');

    [success, error, scan].forEach(audio => {
        if (!audio) return;
        audio.volume = 1;
        audio.muted = false;
        audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
        }).catch(() => {});
    });

    soundEnabled = true;
    alert("Sound scanner aktif ✅");
}


let video, canvas, canvasContext;
let scanning = false;
let scanType = 'checkin';
let isProcessing = false;
let currentMode = 'camera';
let cameraInitialized = false;

function playBeep() {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();

    osc.type = "square";
    osc.frequency.value = 1200; // nada beep
    gain.gain.value = 0.1;

    osc.connect(gain);
    gain.connect(ctx.destination);

    osc.start();
    setTimeout(() => {
        osc.stop();
        ctx.close();
    }, 120);
}

function playSound(type) {
    if (!soundEnabled) return; // jangan play kalau belum diaktifkan

    let audio;
    if (type === 'success') audio = document.getElementById('successSound');
    if (type === 'error') audio = document.getElementById('errorSound');
    if (type === 'scanning') audio = document.getElementById('scanningSound');

    if (audio) {
        audio.currentTime = 0;
        audio.play().catch(() => {});
    }
}

function speak(text) {
    if (!('speechSynthesis' in window)) return;

    const doSpeak = () => {
        const voices = speechSynthesis.getVoices();
        let voice = voices.find(v => v.lang === 'id-ID') 
                 || voices.find(v => v.lang.startsWith('id')) 
                 || voices.find(v => v.lang === 'en-US');

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.voice = voice;
        utterance.lang = voice ? voice.lang : 'id-ID';
        utterance.rate = 1.15;   // standar segini
        utterance.pitch = 1;
        utterance.volume = 1;

        speechSynthesis.cancel(); // stop suara lama kalau numpuk
        speechSynthesis.speak(utterance);
    };

    if (speechSynthesis.getVoices().length === 0) {
        speechSynthesis.onvoiceschanged = doSpeak;
    } else {
        doSpeak();
    }
}

function randomSpeak(messages, nama) {
    if (!Array.isArray(messages) || messages.length === 0) return;

    // pilih kalimat acak
    const randomText = messages[Math.floor(Math.random() * messages.length)];

    // ganti {nama} dengan nama asli
    const finalText = randomText.replace('{nama}', nama);

    speak(finalText);
}

function speakAttendance(result, scanType) {
    let nama = result.student?.name || "siswa";
    let status = result.status; // dari backend Laravel

    if (scanType === 'checkin') {

        if (status === 'terlambat') {
            randomSpeak([
                "Absensi masuk tercatat, namun kamu terlambat hari ini, {nama}.",
                "Perhatian {nama}, kamu datang terlambat hari ini.",
                "Kehadiran diterima dengan status terlambat, {nama}.",
                "Absensi berhasil, tapi kamu terlambat. Besok jangan ya, {nama}."
            ], nama);

        } else {
            randomSpeak([
                "Absensi masuk berhasil. Selamat datang, {nama}.",
                "Kehadiran tercatat. Selamat belajar, {nama}.",
                "Scan berhasil. Semoga harimu menyenangkan, {nama}.",
                "Selamat pagi {nama}, absensi kamu sudah masuk."
            ], nama);
        }

    } else if (scanType === 'checkout') {

        if (result.is_early) {
            randomSpeak([
                "Absensi pulang tercatat lebih awal. Semoga urusannya lancar, {nama}.",
                "Kamu pulang lebih cepat hari ini, {nama}. Hati-hati di jalan.",
                "Izin pulang lebih awal diterima. Jaga kesehatan ya, {nama}."
            ], nama);
        } else {
            randomSpeak([
                "Absensi pulang berhasil. Hati-hati di jalan, {nama}.",
                "Sampai jumpa besok, {nama}.",
                "Kepulangan dicatat. Semoga selamat sampai rumah, {nama}."
            ], nama);
        }
    }
}

// Switch Mode
function switchMode(mode) {
    currentMode = mode;
    
    if (mode === 'camera') {
        document.getElementById('cameraMode').classList.remove('hidden');
        document.getElementById('uploadMode').classList.add('hidden');
        document.getElementById('btnModeCamera').className = 'py-3 px-4 rounded-lg font-semibold transition border-2 border-blue-600 bg-blue-600 text-white';
        document.getElementById('btnModeUpload').className = 'py-3 px-4 rounded-lg font-semibold transition border-2 border-gray-300 text-gray-700 hover:bg-gray-50';
        
        if (!cameraInitialized) {
            initCamera();
        } else {
            startScanning();
        }
    } else {
        document.getElementById('cameraMode').classList.add('hidden');
        document.getElementById('uploadMode').classList.remove('hidden');
        document.getElementById('btnModeUpload').className = 'py-3 px-4 rounded-lg font-semibold transition border-2 border-blue-600 bg-blue-600 text-white';
        document.getElementById('btnModeCamera').className = 'py-3 px-4 rounded-lg font-semibold transition border-2 border-gray-300 text-gray-700 hover:bg-gray-50';
        
        stopCameraScanning();
    }
}

function setMode(type) {
    scanType = type;
    document.getElementById('scanMode').textContent = type === 'checkin' ? 'Absen Masuk' : 'Absen Pulang';
    
    if (type === 'checkin') {
        document.getElementById('btnCheckIn').classList.add('ring-4', 'ring-blue-300');
        document.getElementById('btnCheckOut').classList.remove('ring-4', 'ring-purple-300');
    } else {
        document.getElementById('btnCheckOut').classList.add('ring-4', 'ring-purple-300');
        document.getElementById('btnCheckIn').classList.remove('ring-4', 'ring-blue-300');
    }
    
    if (currentMode === 'camera' && cameraInitialized) {
        startScanning();
    }
}

// ============ CAMERA MODE ============
async function initCamera() {
    video = document.getElementById('qrVideo');
    canvas = document.createElement('canvas');
    canvasContext = canvas.getContext('2d');
    
    // Show loading
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('errorState').classList.add('hidden');
    
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });
        
        video.srcObject = stream;
        const track = stream.getVideoTracks()[0];
        const settings = track.getSettings();

        // Kalau kamera depan → mirror
        if (settings.facingMode === "user") {
            video.style.transform = "scaleX(-1)";
        } else {
            video.style.transform = "scaleX(1)";
        }

        speak("Scanner aktif. Silakan arahkan kode QR ke kamera");

        await new Promise((resolve, reject) => {
            video.onloadedmetadata = resolve;
            video.onerror = reject;
            setTimeout(() => reject(new Error('Timeout')), 10000);
        });
        
        cameraInitialized = true;
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('statusText').textContent = 'Ready';
        console.log('Camera initialized successfully');
        
        // Auto start scanning
        startScanning();
        
    } catch (error) {
        console.error('Camera init error:', error);
        cameraInitialized = false;
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('errorState').classList.remove('hidden');
        document.getElementById('errorState').classList.add('flex');
        document.getElementById('errorMessage').textContent = 'Gagal mengakses kamera: ' + error.message;
        document.getElementById('statusText').textContent = 'Error: No camera';
    }
}

function retryCamera() {
    document.getElementById('errorState').classList.add('hidden');
    initCamera();
}

function startScanning() {
    if (scanning || !cameraInitialized) return;
    
    scanning = true;
    isProcessing = false;
    document.getElementById('pausedState').classList.add('hidden');
    document.getElementById('pausedState').classList.remove('flex');
    document.getElementById('statusText').textContent = 'Scanning...';
    document.getElementById('scanIndicator').className = 'absolute inset-0 border-4 border-blue-500 animate-pulse pointer-events-none';
    
    console.log('Scanning started, mode:', scanType);
    requestAnimationFrame(tick);
}

function tick() {
    if (!scanning) return;
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: "attemptBoth"
        });
        
       if (code && !isProcessing) {

            playBeep(); // bunyi beep dulu
            speak("Kode terdeteksi. Memproses data");

            document.getElementById('scanIndicator').className =
                'absolute inset-0 border-4 border-green-500 pointer-events-none animate-pulse';

            processQRCode(code.data);
            return;
}

    }
    
    requestAnimationFrame(tick);
}

function stopCameraScanning() {
    scanning = false;
    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        cameraInitialized = false;
    }
}

function resumeScanning() {
    document.getElementById('pausedState').classList.add('hidden');
    document.getElementById('pausedState').classList.remove('flex');
    startScanning();
}

function toggleScanner() {
    if (scanning) {
        scanning = false;
        document.getElementById('pausedState').classList.remove('hidden');
        document.getElementById('pausedState').classList.add('flex');
        document.getElementById('statusText').textContent = 'Paused';
    } else {
        resumeScanning();
    }
}

// ============ UPLOAD MODE ============
async function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        alert('File terlalu besar! Maksimal 5MB');
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        const img = new Image();
        img.src = e.target.result;

        img.onload = () => {
            document.getElementById('processingUpload').classList.remove('hidden');

            const tempCanvas = document.createElement('canvas');
            const ctx = tempCanvas.getContext('2d');

            tempCanvas.width = img.width;
            tempCanvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            const imageData = ctx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);

            const code = jsQR(
                imageData.data,
                imageData.width,
                imageData.height,
                { inversionAttempts: "attemptBoth" }
            );

            document.getElementById('processingUpload').classList.add('hidden');

            if (code) {
                console.log('UPLOAD QR DATA:', code.data);
                processQRCode(code.data);
            } else {
                showError('QR Code tidak terdeteksi di gambar. Pastikan QR jelas & tidak blur.');
                playSound('error');
            }
        };
    };

    reader.readAsDataURL(file);
}

function clearUpload() {
    document.getElementById('qrImageInput').value = '';
    document.getElementById('uploadPreview').classList.add('hidden');
    document.getElementById('processingUpload').classList.add('hidden');
}

// ============ PROCESS QR ============
async function processQRCode(qrData) {
    if (isProcessing) {
        console.log('Already processing, skip');
        return;
    }
    
    isProcessing = true;
    scanning = false;
    
    document.getElementById('scanIndicator').className = 'absolute inset-0 border-4 border-yellow-500 pointer-events-none';
    document.getElementById('statusText').textContent = 'Processing...';
    
    console.log('Processing QR:', qrData.substring(0, 30) + '...');
    
    try {
        const response = await fetch('{{ route("admin.qr-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_data: qrData,
                type: scanType
            })
        });

        const result = await response.json();
        console.log('Server response:', result);

        if (!response.ok) {

    const msg = result.message || result.error || 'QR tidak valid atau request ditolak';

    document.getElementById('scanIndicator').className =
        'absolute inset-0 border-4 border-red-500 pointer-events-none';

    playSound('error');
    showError(msg);
    speak("Absensi ditolak. " + msg);

} else if (result.success === true) {

    document.getElementById('scanIndicator').className =
        'absolute inset-0 border-4 border-green-500 pointer-events-none';

    playSound('success');
    showSuccess(result)
    speakAttendance(result, scanType);

    // let successText = scanType === 'checkin'
    //     ? `Absensi masuk berhasil. Selamat datang ${result.student.name}`
    //     : `Absensi pulang berhasil. Hati-hati di jalan ${result.student.name}`;

    // speak(successText);

} else {

    const msg = result.message || 'QR tidak valid';

    document.getElementById('scanIndicator').className =
        'absolute inset-0 border-4 border-red-500 pointer-events-none';

    playSound('error');
    showError(msg);
    speak("Absensi gagal. " + msg);
}

        
//         let successText = scanType === 'checkin'
//     ? `Absensi masuk berhasil. Selamat datang ${result.student.name}`
//     : `Absensi pulang berhasil. Hati-hati di jalan ${result.student.name}`;

// speak(successText);


    } catch (error) {
        console.error('NETWORK ERROR:', error);
        
        // NETWORK ERROR FEEDBACK
        document.getElementById('scanIndicator').className = 'absolute inset-0 border-4 border-red-500 pointer-events-none';
        playSound('error');
        
        showError('Gagal menghubungi server: ' + error.message);
        
    } finally {
        isProcessing = false;
        
        // Reset indicator after 1 second
        setTimeout(() => {
            document.getElementById('scanIndicator').className = 'absolute inset-0 border-4 border-transparent pointer-events-none transition-all duration-300';
        }, 1000);
        
        document.getElementById('statusText').textContent = 'Scan completed';
        
        if (currentMode === 'camera') {
            document.getElementById('pausedState').classList.remove('hidden');
            document.getElementById('pausedState').classList.add('flex');
        } else {
            clearUpload();
        }
    }
}

function showSuccess(result) {
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-green-50 border-2 border-green-400 rounded-lg p-3 hover:shadow-md transition-all duration-300 animate-slideIn';
    scanDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-green-900">${result.student.name}</p>
                <p class="text-sm text-green-700">${result.student.nisn} - ${result.student.class}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>${result.time} - 
                    <span class="font-semibold">${scanType === 'checkin' ? 'MASUK' : 'PULANG'}</span>
                    ${result.status === 'terlambat' ? '<span class="text-yellow-600 font-bold">(⚠ Terlambat)</span>' : '<span class="text-green-600">✓</span>'}
                </p>
            </div>
            <i class="fas fa-check-circle text-3xl text-green-600 animate-bounce"></i>
        </div>
    `;
    
    const container = document.getElementById('recentScans');
    if (container.firstChild && container.firstChild.tagName === 'P') {
        container.innerHTML = '';
    }
    container.insertBefore(scanDiv, container.firstChild);
}

function showError(message) {
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-red-50 border-2 border-red-400 rounded-lg p-3 animate-shake';
    scanDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-red-900">❌ Error</p>
                <p class="text-sm text-red-700">${message}</p>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Silakan coba lagi atau hubungi admin
                </p>
            </div>
            <i class="fas fa-times-circle text-3xl text-red-600"></i>
        </div>
    `;
    
    const container = document.getElementById('recentScans');
    if (container.firstChild && container.firstChild.tagName === 'P') {
        container.innerHTML = '';
    }
    container.insertBefore(scanDiv, container.firstChild);
}

// Add custom animations via style tag
const style = document.createElement('style');
style.textContent = `
    /* FIX MIRROR CAMERA */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    .animate-slideIn {
        animation: slideIn 0.3s ease-out;
    }
    .animate-shake {
        animation: shake 0.4s ease-in-out;
    }
`;
document.head.appendChild(style);

// Init on page load

window.addEventListener('load', () => {
    console.log('Page loaded, initializing...');
    setMode('checkin');
    initCamera();
});
// UNLOCK AUDIO SAAT USER TEKAN TOMBOL CAMERA
document.getElementById('btnModeCamera')?.addEventListener('click', () => {
    unlockAudio();
    speak("...");

    // TEST SOUND LANGSUNG SAAT DIKLIK
    setTimeout(() => {
        playSound('success');
    }, 500);
});


</script>
@endsection