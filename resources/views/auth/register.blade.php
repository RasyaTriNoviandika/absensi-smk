<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Siswa</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

       <!-- Global Loading Screen -->
    <x-loading id="globalLoading">Memuat halaman login...</x-loading>

    <div class="min-h-screen py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

            <div class="text-center mb-6">
                <i class="fas fa-user-plus text-5xl text-blue-600 mb-3"></i>
                <h2 class="text-2xl font-bold text-gray-800">Registrasi Siswa</h2>
                <p class="text-gray-600">Isi data dan registrasi wajah</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded shadow-sm p-6">
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- Step 1: Data -->
                    <div id="step1">
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NISN *</label>
                                <input type="text" name="nisn" maxlength="10" required value="{{ old('nisn') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas *</label>
                                <select name="class" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Kelas</option>
                                    @foreach(['10 DKV 1', '10 DKV 2', '10 DKV 3', '11 DKV 1', '11 DKV 2', '11 DKV 3', '12 DKV 1', '12 DKV 2', '12 DKV 3', '10 SIJA 1', '10 SIJA 2', '10 SIJA 3', '11 SIJA 1', '11 SIJA 2', '11 SIJA 3', '12 SIJA 1', '12 SIJA 2', '12 SIJA 3', '10 PB 1', '10 PB 2', '10 PB 3', '11 PB 1', '11 PB 2', '11 PB 3', '12 PB 1', '12 PB 2', '12 PB 3'] as $class)
                                        <option value="{{ $class }}" {{ old('class') == $class ? 'selected' : '' }}>{{ $class }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    id="phone" 
                                    pattern="[0-9+]*" 
                                    inputmode="numeric" 
                                    value="{{ old('phone') }}" 
                                    minlength="10" 
                                    maxlength="15" 
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">
                                        Format: 08xxx / 628xxx / +628xxx (12-15 digit)
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                                <input type="text" name="username" required value="{{ old('username') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                                <input type="password" name="password" id="password" required minlength="8"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <button type="button" onclick="goToStep2()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded transition">
                            Lanjut ke Registrasi Wajah <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>

                    <!-- Step 2: Face -->
                    <div id="step2" class="hidden">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Registrasi Wajah</h3>

                        <div class="bg-gray-900 rounded overflow-hidden mb-4 relative" style="height: 400px;">
                            <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                            
                            <div id="faceIndicator" class="absolute bottom-4 left-4 right-4 hidden">
                                <div class="bg-green-500 text-white px-4 py-2 rounded text-center font-semibold text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>Wajah Terdeteksi
                                </div>
                            </div>

                            <div id="loadingModels" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                                    <p class="text-sm">Loading model AI...</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4 text-sm">
                            <p class="text-blue-800"><strong>Instruksi:</strong></p>
                            <ul class="text-blue-800 mt-1 ml-4 list-disc">
                                <li class="text-red-50">Pastikan Kamera Aktif</li>
                                <li>Wajah menghadap kamera</li>
                                <li>Pencahayaan cukup terang</li>
                                <li>Tidak pakai masker/kacamata</li>
                            </ul>
                        </div>

                        <input type="hidden" name="face_descriptor" id="faceDescriptor">

                        <div class="flex gap-3 mb-3">
                            <button type="button" onclick="goToStep1()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded transition">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </button>

                            <button type="button" id="captureFaceBtn" onclick="captureFace()" disabled
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded transition disabled:bg-gray-300">
                                <i class="fas fa-camera mr-2"></i>Capture
                            </button>
                        </div>

                        <button type="submit" id="submitBtn" disabled
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded transition disabled:bg-gray-300">
                            <i class="fas fa-check mr-2"></i>Daftar
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center text-sm">
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

        window.addEventListener('load', function() {
            document.getElementById('globalLoading').style.display = 'none';
        });

        let modelsLoaded = false;
        let faceDescriptor = null;
        let detectionInterval = null;
        let stream = null;

        // VALIDASI PHONE NUMBER REAL-TIME
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9+]/g, '');
                    if (this.value.indexOf('+') > 0) {
                        this.value = this.value.replace(/\+/g, '');
                    }
                    validatePhoneNumber(this);
                });
                
                phoneInput.addEventListener('blur', function(e) {
                    validatePhoneNumber(this);
                });
            }
        });

        function validatePhoneNumber(input) {
            const value = input.value.trim();
            
            // Jika kosong, tidak perlu validasi
            if (value === '') {
                input.classList.remove('border-red-500', 'border-green-500');
                removePhoneError();
                return true;
            }
            
            // Jika sedang mengetik awalan yang benar (08, 62, +62), jangan tampilkan error
            if (value === '0' || value === '08' || 
                value === '6' || value === '62' || 
                value === '+' || value === '+6' || value === '+62') {
                input.classList.remove('border-red-500', 'border-green-500');
                removePhoneError();
                return true;
            }
            
            // Validasi format lengkap
            const phoneRegex = /^(\+62|62|0)8[0-9]{8,13}$/;
            
            // Cek apakah awalan benar
            if (!value.startsWith('08') && !value.startsWith('62') && !value.startsWith('+62')) {
                input.classList.add('border-red-500');
                input.classList.remove('border-green-500');
                showPhoneError('Nomor harus diawali 08, atau 62');
                return false;
            }
            
            // Jika sudah mencapai panjang minimal, validasi lengkap
            if (value.length >= 10) {
                if (!phoneRegex.test(value)) {
                    input.classList.add('border-red-500');
                    input.classList.remove('border-green-500');
                    showPhoneError('Format nomor HP tidak valid');
                    return false;
                }
                
                if (value.length > 15) {
                    input.classList.add('border-red-500');
                    input.classList.remove('border-green-500');
                    showPhoneError('Nomor HP maksimal 15 digit');
                    return false;
                }
                
                // Valid!
                input.classList.remove('border-red-500');
                input.classList.add('border-green-500');
                removePhoneError();
                return true;
            }
            
            // Masih dalam proses mengetik (kurang dari 10 digit tapi awalan benar)
            input.classList.remove('border-red-500', 'border-green-500');
            removePhoneError();
            return true;
        }

        function showPhoneError(message) {
            removePhoneError();
            const phoneInput = document.getElementById('phone');
            const errorDiv = document.createElement('p');
            errorDiv.className = 'text-sm text-red-600 mt-1 phone-error';
            errorDiv.textContent = message;
            phoneInput.parentNode.appendChild(errorDiv);
        }

        function removePhoneError() {
            const existingError = document.querySelector('.phone-error');
            if (existingError) existingError.remove();
        }

        // LOAD MODELS
        async function loadModels() {
            try {
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
                console.log('Loading face recognition models...');
                
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);
                
                modelsLoaded = true;
                document.getElementById('loadingModels').classList.add('hidden');
                console.log('Models loaded successfully');
            } catch (error) {
                console.error('Error loading models:', error);
                alert('Gagal memuat model AI. Refresh halaman dan pastikan koneksi internet stabil.');
            }
        }

        loadModels();

        function goToStep1() {
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
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('#step1 [required]');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            const phoneInput = document.getElementById('phone');
            if (phoneInput && phoneInput.value.trim() !== '') {
                if (!validatePhoneNumber(phoneInput)) {
                    alert('Format nomor HP tidak valid!');
                    return;
                }
            }

            const password = document.getElementById('password').value;
            const confirmPassword = document.querySelector('[name="password_confirmation"]').value;
            
            if (password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok!');
                return;
            }

            if (password.length < 8) {
                alert('Password minimal 8 karakter!');
                return;
            }

            if (!valid) {
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }

            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');

            if (!modelsLoaded) {
                document.getElementById('loadingModels').classList.remove('hidden');
                let attempts = 0;
                while (!modelsLoaded && attempts < 30) {
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    attempts++;
                }
                if (!modelsLoaded) {
                    alert('Model AI gagal dimuat. Refresh halaman.');
                    goToStep1();
                    return;
                }
            }

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
                
                await new Promise(resolve => {
                    video.onloadedmetadata = resolve;
                });
                
                startFaceDetection();
            } catch (err) {
                console.error('Camera error:', err);
                alert('Tidak bisa akses kamera. Pastikan:\n1. Kamera tidak dipakai aplikasi lain\n2. Izin kamera sudah diberikan di browser');
                goToStep1();
            }
        }

        let lastDetectionTime = 0;
        const DETECTION_INTERVAL = 500;

        function startFaceDetection() {
            const video = document.getElementById('video');
            const captureBtn = document.getElementById('captureFaceBtn');
            const faceIndicator = document.getElementById('faceIndicator');

            detectionInterval = setInterval(async () => {
                const now = Date.now();
                if (now - lastDetectionTime < DETECTION_INTERVAL) return;
                lastDetectionTime = now;

                if (!modelsLoaded || video.readyState !== 4) return;

                try {
                    const detection = await faceapi.detectSingleFace(
                        video,
                        new faceapi.TinyFaceDetectorOptions({ inputSize: 224 })
                    ).withFaceLandmarks().withFaceDescriptor();

                    if (detection) {
                        faceIndicator.classList.remove('hidden');
                        captureBtn.disabled = false;
                    } else {
                        faceIndicator.classList.add('hidden');
                        captureBtn.disabled = true;
                    }
                } catch (error) {
                    console.error('Detection error:', error);
                }
            }, DETECTION_INTERVAL);
        }

        async function captureFace() {
            const video = document.getElementById('video');
            const btn = document.getElementById('captureFaceBtn');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const detection = await faceapi.detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions({ inputSize: 224 })
                ).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    alert('Wajah tidak terdeteksi!\n\nPastikan:\n- Wajah menghadap kamera\n- Pencahayaan cukup terang\n- Tidak pakai masker/kacamata');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture';
                    return;
                }

                faceDescriptor = Array.from(detection.descriptor);
                document.getElementById('faceDescriptor').value = JSON.stringify(faceDescriptor);
                document.getElementById('submitBtn').disabled = false;

                if (detectionInterval) {
                    clearInterval(detectionInterval);
                    detectionInterval = null;
                }

                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Wajah Terekam!';
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-green-500');

                alert('✅ Wajah berhasil direkam!\n\nSekarang klik tombol "Daftar" untuk menyelesaikan registrasi.');
            } catch (error) {
                console.error('Capture error:', error);
                alert('Terjadi kesalahan saat merekam wajah. Coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture';
            }
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if (!faceDescriptor) {
                e.preventDefault();
                alert('Anda harus merekam wajah terlebih dahulu!');
                return false;
            }
        });
    </script>
</body>
</html>