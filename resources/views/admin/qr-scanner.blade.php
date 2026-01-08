@extends('layouts.admin')

@section('title', 'QR Scanner')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">QR Code Scanner</h1>
        <p class="text-gray-600">Scan QR Code siswa untuk absen manual</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Scanner -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-qrcode text-blue-600 mr-2"></i>Scanner
            </h3>

            <!-- Camera Preview -->
            <div class="bg-gray-900 rounded-lg overflow-hidden mb-4 relative" style="height: 400px;">
                <video id="qrVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                
                <!-- Scanning Indicator -->
                <div id="scanIndicator" class="absolute inset-0 border-4 border-transparent pointer-events-none"></div>
                
                <!-- Loading -->
                <div id="loading" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                    <div class="text-center text-white">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <p class="font-semibold">Initializing camera...</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <button onclick="scanForCheckIn()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold">
                    <i class="fas fa-sign-in-alt mr-2"></i>Absen Masuk
                </button>

                <button onclick="scanForCheckOut()" 
                    class="bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold">
                    <i class="fas fa-sign-out-alt mr-2"></i>Absen Pulang
                </button>
            </div>

            <div class="mt-4 text-sm text-gray-600 bg-gray-50 p-3 rounded">
                <p><strong>Status:</strong> <span id="statusText">Idle</span></p>
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-history text-green-600 mr-2"></i>Scan Terakhir
            </h3>

            <div id="recentScans" class="space-y-3 max-h-96 overflow-y-auto">
                <p class="text-gray-500 text-center py-8">Belum ada scan</p>
            </div>
        </div>
    </div>

     <div class="flex space-x-3 right">
                    <a href="{{ route('admin.monitoring') }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white text-center font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
</div>
   

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let video;
let canvas;
let canvasContext;
let scanning = false;
let scanType = null;

async function initCamera() {
    video = document.getElementById('qrVideo');
    canvas = document.createElement('canvas');
    canvasContext = canvas.getContext('2d');
    
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        video.srcObject = stream;
        
        await new Promise(resolve => {
            video.onloadedmetadata = resolve;
        });
        
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('statusText').textContent = 'Ready';
    } catch (error) {
        alert('Gagal mengakses kamera: ' + error.message);
        document.getElementById('statusText').textContent = 'Error: No camera';
    }
}

function scanForCheckIn() {
    scanType = 'checkin';
    startScanning();
}

function scanForCheckOut() {
    scanType = 'checkout';
    startScanning();
}

function startScanning() {
    if (scanning) return;
    
    scanning = true;
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
        
        if (code) {
            processQRCode(code.data);
            return;
        }
    }
    
    requestAnimationFrame(tick);
}

async function processQRCode(qrData) {
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
        
        if (result.success) {
            showSuccess(result);
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Gagal memproses QR Code');
    }
    
    document.getElementById('statusText').textContent = 'Ready';
}

function showSuccess(result) {
    // Play success sound (optional)
    const audio = new Audio('data:audio/wav;base64,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');
    audio.play();
    
    // Add to recent scans
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-green-50 border border-green-200 rounded p-3';
    scanDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
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
    if (container.firstChild.tagName === 'P') {
        container.innerHTML = '';
    }
    container.insertBefore(scanDiv, container.firstChild);
    
    alert(` ${result.message}`);
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
    if (container.firstChild.tagName === 'P') {
        container.innerHTML = '';
    }
    container.insertBefore(scanDiv, container.firstChild);
    
    alert(`❌ ${message}`);
}

// Initialize on page load
window.addEventListener('load', initCamera);
</script>
@endsection