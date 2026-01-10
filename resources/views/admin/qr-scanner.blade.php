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

    <!--  MODE SELECTOR -->
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

            <!--  MODE 1: CAMERA SCAN -->
            <div id="cameraMode">
                <div class="bg-gray-900 rounded-lg overflow-hidden mb-4 relative" style="height: 400px;">
                    <video id="qrVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div id="scanIndicator" class="absolute inset-0 border-4 border-transparent pointer-events-none"></div>
                    
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
                </div>
            </div>

            <!--  MODE 2: UPLOAD IMAGE -->
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

                <!--  Processing Indicator -->
                <div id="processingUpload" class="hidden text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
                    <p class="text-gray-700 font-semibold">Membaca QR Code...</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <button id="btnCheckIn" onclick="setMode('checkin')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Absen Masuk
                </button>
                <button id="btnCheckOut" onclick="setMode('checkout')" 
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
<audio id="successSound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGWm98OScTgwOUKzn77RgGwU7k9r0yXosBiJ+z/PhlUEKFFuw7u+nVRQKRp/h8L50IAUsgc/y2Ik2CBlpvfDknE4MDlCr5vK1YRsGOpPa9Ml6LAYif9D04pVBChRbr+7wqFYUCkef4fC+dCAFLIHP8tmJNggZaLzw5JxODA5Qq+b0tWEbBjqT2vTJeiwGIX/R9OOVQQsUWq/u8KlXFApHn+Hwv3QgBSyB0fLaijYIGWi88OWcTgwOUKvm9LdhGwY6k9r0yXosBiF/0fTjlUELFFqv7vCpVxQKR5/h8L90IAUsgdDy2oo2CBlovPDlnE4MDlCr5vS3YRsGOpLa9Ml6LAYhftL05JVBCxRbr+7wqVcUCkef4e++dCAFLIDQ8tqKNggZaL3w5ZxODA5Qq+b0t2EbBjqS2vTJeiwGIX7S9OSVQQsUW6/u8KlXFApHn+Hvv3QgBSyA0PLaijYIGWi98OWcTgwOUKvm9LZhGwY6ktr0yXosBiF+0/TklUELFFqv7vCpVxQKR5/h7790IAUsgNDy2oo2CBlou/DlnE4MDlCr5vS2YRsGOpLa9Ml6LAYhftT05JVBCxRar+7wqVcUCkef4e+/dCAFLIDQ8tqKNggZaL3w5ZxODA5Qq+b0tmEbBjqS2vTJeiwGIX7U9OSVQQsUWq/u8KlXFApHn+Hvv3QgBSyA0PLaijYIGWi98OWcTgwOUKvm9LZhGwY6ktr0yXosBiF+1PTklUELFFqv7vCpVxQKR5/h7790IAUsgNDy2oo2CBlou/DlnE4MDlCr5vS2YRsGOpLa9Ml6LAYhftT05JVBCxRar+7wqVcUCkef4e+/dCAFLIDQ8tqKNggZaLzw5pxODA5Qq+b0tmEbBjqS2vTJeiwGIX7U9OSVQQsUWq/u8KlXFApHn+Hvv3QgBSyA0PLaijYIGWi88OacTgwOUKvm9LZhGwY6ktr0yXosBiF+1PTklUELFFqv7vCpVxQKR5/h7790IAUsgNDy2oo2CBlou/DlnE4MDlCr5vS2YRsGOpLa9Ml6LAYhftT05JVBCxRar+7wqVcUCkef4e+/dCAFLIDQ8tqKNggZaLzw5pxODA5Qq+b0tmEbBjqS2vTJeiwGIX7U9OSVQQsUWq/u8KlXFApHn+Hvv3QgBSyA0PLaijYIGWi88OacTgwOUKvm9LZhGwY6ktr0yXosBiF+1PTklUELFFqv7vCpVxQKR5/h7790IAUsgNDy2oo2CBlou/DlnE4MDlCr5vS2YRsGOpLa9Ml6LAYhftT05JVBCxRar+7wqVcUCkef4e+/dCAFLIDQ8tqKNggZaLzw5pxODA5Qq+b0tmEbBjqS2vTJeiwGIX7U9OSVQQsUWq/u8KlXFApHn+Hvv3QgBSyA0PLaijYIGWi88OacTgwOUKvm9LZhGwY6ktr0yXosBiF+1PTklUELFFqv7vCpVxQKR5/h7790IA" type="audio/wav">
</audio>
<audio id="errorSound" preload="auto">
    <source src="data:audio/wav;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAGDgYtAgAyN+QWaAAihwMWm0G8AoBAAAAA==" type="audio/wav">
</audio>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@zxing/library@latest/umd/index.min.js"></script>

<script>
let video, canvas, canvasContext;
let scanning = false;
let scanType = 'checkin';
let isProcessing = false;
let currentMode = 'camera'; // 'camera' or 'upload'

//  Switch Mode
function switchMode(mode) {
    currentMode = mode;
    
    if (mode === 'camera') {
        document.getElementById('cameraMode').classList.remove('hidden');
        document.getElementById('uploadMode').classList.add('hidden');
        document.getElementById('btnModeCamera').className = 'py-3 px-4 rounded-lg font-semibold transition border-2 border-blue-600 bg-blue-600 text-white';
        document.getElementById('btnModeUpload').className = 'py-3 px-4 rounded-lg font-semibold transition border-2 border-gray-300 text-gray-700 hover:bg-gray-50';
        
        if (!video) initCamera();
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
    
    if (currentMode === 'camera') {
        startScanning();
    }
}

// ============ CAMERA MODE ============
async function initCamera() {
    video = document.getElementById('qrVideo');
    canvas = document.createElement('canvas');
    canvasContext = canvas.getContext('2d');
    
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        video.srcObject = stream;
        await new Promise(resolve => { video.onloadedmetadata = resolve; });
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('statusText').textContent = 'Ready';
    } catch (error) {
        alert('Gagal mengakses kamera: ' + error.message);
        document.getElementById('statusText').textContent = 'Error: No camera';
    }
}

function startScanning() {
    if (scanning) return;
    scanning = true;
    isProcessing = false;
    document.getElementById('pausedState').classList.add('hidden');
    document.getElementById('statusText').textContent = 'Scanning...';
    document.getElementById('scanIndicator').className = 'absolute inset-0 border-4 border-blue-500 animate-pulse pointer-events-none';
    requestAnimationFrame(tick);
}

function tick() {
    if (!scanning) return;
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code && !isProcessing) {
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
    }
}

function resumeScanning() {
    startScanning();
}

function toggleScanner() {
    if (scanning) {
        scanning = false;
        document.getElementById('pausedState').classList.remove('hidden');
        document.getElementById('pausedState').classList.add('flex');
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

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

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
    if (isProcessing) return;
    isProcessing = true;
    
    scanning = false;
    document.getElementById('scanIndicator').className = 'absolute inset-0 border-4 border-transparent pointer-events-none';
    document.getElementById('statusText').textContent = 'Processing...';
    
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

        if (!response.ok) {
            console.warn('SERVER ERROR:', result);

            showError(
                result.message ||
                result.error ||
                'QR tidak valid atau request ditolak'
            );
            playSound('error');
            return;
        }

        if (result.success === true) {
            showSuccess(result);
            playSound('success');
        } else {
            showError(result.message || 'QR tidak valid');
            playSound('error');
        }

    } catch (error) {
        console.error('NETWORK ERROR:', error);
        showError('Gagal menghubungi server');
        playSound('error');
    } finally {
        isProcessing = false;
    }

    
    document.getElementById('statusText').textContent = 'Scan completed';
    
    if (currentMode === 'camera') {
        document.getElementById('pausedState').classList.remove('hidden');
        document.getElementById('pausedState').classList.add('flex');
    } else {
        clearUpload();
    }
}

function showSuccess(result) {
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-green-50 border border-green-200 rounded p-3 hover:shadow-md transition';
    scanDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div>w
                <p class="font-semibold text-green-900">${result.student.name}</p>
                <p class="text-sm text-green-700">${result.student.nisn} - ${result.student.class}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>${result.time} - 
                    <span class="font-semibold">${scanType === 'checkin' ? 'MASUK' : 'PULANG'}</span>
                    ${result.status === 'terlambat' ? '<span class="text-yellow-600">(Terlambat)</span>' : ''}
                </p>
            </div>
            <i class="fas fa-check-circle text-2xl text-green-600"></i>
        </div>
    `;
    
    const container = document.getElementById('recentScans');
    if (container.firstChild.tagName === 'P') container.innerHTML = '';
    container.insertBefore(scanDiv, container.firstChild);
}

function showError(message) {
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-red-50 border border-red-200 rounded p-3';
    scanDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-red-900">Error</p>
                <p class="text-sm text-red-700">${message}</p>
            </div>
            <i class="fas fa-times-circle text-2xl text-red-600"></i>
        </div>
    `;
    
    const container = document.getElementById('recentScans');
    if (container.firstChild.tagName === 'P') container.innerHTML = '';
    container.insertBefore(scanDiv, container.firstChild);
}

function playSound(type) {
    const audio = document.getElementById(type === 'success' ? 'successSound' : 'errorSound');
    audio.currentTime = 0;
    audio.play().catch(() => {});
}

// Init
window.addEventListener('load', () => {
    initCamera();
});
</script>
@endsection