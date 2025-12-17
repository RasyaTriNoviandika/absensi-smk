<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Absensi SMK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                </a>
            </div>

            <div class="text-center mb-8">
                <i class="fas fa-user-plus text-6xl text-blue-600 mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-800">Registrasi Siswa Baru</h2>
                <p class="text-gray-600 mt-2">Lengkapi data diri dan registrasi wajah Anda</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-8">
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- Step 1: Personal Data -->
                    <div id="step1">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm mr-2">Step 1</span>
                            Data Pribadi
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NISN *</label>
                                <input type="text" name="nisn" maxlength="10" required value="{{ old('nisn') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">10 digit</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
                                <select name="class" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Kelas</option>
                                    @foreach(['10 DKV 1', '10 DKV 2', '10 DKV 3', '11 DKV 1', '11 DKV 2', '11 DKV 3', '12 DKV 1', '12 DKV 2', '12 DKV 3', '10 SIJA 1', '10 SIJA 2', '10 SIJA 3', '11 SIJA 1', '11 SIJA 2', '11 SIJA 3', '12 SIJA 1', '12 SIJA 2', '12 SIJA 3', '10 PB 1', '10 PB 2', '10 PB 3', '11 PB 1', '11 PB 2', '11 PB 3', '12 PB 1', '12 PB 2', '12 PB 3'] as $class)
                                        <option value="{{ $class }}" {{ old('class') == $class ? 'selected' : '' }}>{{ $class }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                                <input type="text" name="username" required value="{{ old('username') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <input type="password" name="password" id="password" required minlength="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Min. 8 karakter</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="button" onclick="goToStep2()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                            Lanjut ke Registrasi Wajah <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>

                    <!-- Step 2: Face Registration -->
                    <div id="step2" class="hidden">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm mr-2">Step 2</span>
                            Registrasi Wajah
                        </h3>

                        <div class="bg-gray-900 rounded-lg overflow-hidden mb-4 relative" style="height: 480px;">
                            <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                            
                            <!-- Face Detection Indicator -->
                            <div id="faceIndicator" class="absolute bottom-4 left-4 right-4 hidden">
                                <div class="bg-green-500 text-white px-4 py-2 rounded-lg text-center font-semibold">
                                    <i class="fas fa-check-circle mr-2"></i>Wajah Terdeteksi!
                                </div>
                            </div>

                            <div id="noFaceIndicator" class="absolute bottom-4 left-4 right-4 hidden">
                                <div class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-center font-semibold">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Posisikan wajah Anda
                                </div>
                            </div>

                            <!-- Loading Indicator -->
                            <div id="loadingModels" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i class="fas fa-spinner fa-spin text-4xl mb-3"></i>
                                    <p>Loading face detection models...</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Instruksi:</strong>
                            </p>
                            <ul class="text-sm text-blue-800 mt-2 space-y-1 ml-6 list-disc">
                                <li>Pastikan wajah Anda terlihat jelas</li>
                                <li>Pencahayaan harus cukup terang</li>
                                <li>Jangan gunakan masker atau kacamata hitam</li>
                                <li>Tunggu hingga wajah terdeteksi (indikator hijau)</li>
                            </ul>
                        </div>

                        <input type="hidden" name="face_descriptor" id="faceDescriptor">

                        <div class="flex space-x-3">
                            <button type="button" onclick="goToStep1()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </button>

                            <button type="button" id="captureFaceBtn" onclick="captureFace()" disabled
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                                <i class="fas fa-camera mr-2"></i>Capture Wajah
                            </button>
                        </div>

                        <button type="submit" id="submitBtn" disabled
                            class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                            <i class="fas fa-check mr-2"></i>Daftar Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Sudah punya akun?</p>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        Login <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
    <script>
        let modelsLoaded = false;
        let faceDescriptor = null;
        let detectionInterval = null;
        let stream = null;

        async function loadModels() {
            try {
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);
                modelsLoaded = true;
                document.getElementById('loadingModels').classList.add('hidden');
                console.log('Face detection models loaded successfully');
            } catch (error) {
                console.error('Error loading models:', error);
                alert('Gagal memuat model deteksi wajah. Silakan refresh halaman.');
            }
        }

        function goToStep1() {
            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
            
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
        }

        async function goToStep2() {
            // Validate step 1
            const form = document.getElementById('registerForm');
            const step1Inputs = form.querySelectorAll('#step1 [required]');
            let valid = true;
            
            step1Inputs.forEach(input => {
                if (!input.value) {
                    valid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            // Check password match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok!');
                return;
            }

            if (!valid) {
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }

            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');

            // Load models if not loaded
            if (!modelsLoaded) {
                await loadModels();
            }

            // Start camera
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                const video = document.getElementById('video');
                video.srcObject = stream;
                
                // Start face detection loop
                startFaceDetection();
            } catch (err) {
                console.error('Camera error:', err);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera.');
            }
        }

        function startFaceDetection() {
            const video = document.getElementById('video');
            const captureBtn = document.getElementById('captureFaceBtn');
            const faceIndicator = document.getElementById('faceIndicator');
            const noFaceIndicator = document.getElementById('noFaceIndicator');

            detectionInterval = setInterval(async () => {
                if (!modelsLoaded) return;

                try {
                    const detection = await faceapi.detectSingleFace(
                        video,
                        new faceapi.TinyFaceDetectorOptions()
                    ).withFaceLandmarks().withFaceDescriptor();

                    if (detection) {
                        faceIndicator.classList.remove('hidden');
                        noFaceIndicator.classList.add('hidden');
                        captureBtn.disabled = false;
                    } else {
                        faceIndicator.classList.add('hidden');
                        noFaceIndicator.classList.remove('hidden');
                        captureBtn.disabled = true;
                    }
                } catch (error) {
                    console.error('Detection error:', error);
                }
            }, 500);
        }

        async function captureFace() {
            const video = document.getElementById('video');
            const btn = document.getElementById('captureFaceBtn');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const detection = await faceapi.detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions()
                ).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    alert('Wajah tidak terdeteksi. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture Wajah';
                    return;
                }

                faceDescriptor = Array.from(detection.descriptor);
                document.getElementById('faceDescriptor').value = JSON.stringify(faceDescriptor);
                document.getElementById('submitBtn').disabled = false;

                // Stop detection interval
                if (detectionInterval) {
                    clearInterval(detectionInterval);
                }

                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Wajah Berhasil Diambil!';
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-green-500');

                alert('Wajah berhasil didaftarkan! Klik "Daftar Sekarang" untuk menyelesaikan registrasi.');
            } catch (error) {
                console.error('Capture error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture Wajah';
            }
        }

        // Load models when page loads
        window.addEventListener('load', () => {
            // Preload models in background
            setTimeout(loadModels, 1000);
        });
    </script>
</body>
</html>