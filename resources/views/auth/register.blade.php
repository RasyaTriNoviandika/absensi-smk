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
                                        <option value="{{ $class }}">{{ $class }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                    <p class="text-sm">Loading...</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4 text-sm">
                            <p class="text-blue-800"><strong>Instruksi:</strong></p>
                            <ul class="text-blue-800 mt-1 ml-4 list-disc">
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
                console.log('Models loaded');
            } catch (error) {
                console.error('Error loading models:', error);
                alert('Gagal memuat model. Refresh halaman.');
            }
        }

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
            // Validate
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('#step1 [required]');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value) {
                    valid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            const password = document.getElementById('password').value;
            const confirmPassword = document.querySelector('[name="password_confirmation"]').value;
            
            if (password !== confirmPassword) {
                alert('Password tidak cocok!');
                return;
            }

            if (!valid) {
                alert('Lengkapi semua field!');
                return;
            }

            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');

            if (!modelsLoaded) {
                await loadModels();
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: 640, height: 480 } 
                });
                document.getElementById('video').srcObject = stream;
                startFaceDetection();
            } catch (err) {
                console.error('Camera error:', err);
                alert('Tidak bisa akses kamera. Izinkan kamera di browser.');
                goToStep1();
            }
        }

        function startFaceDetection() {
            const video = document.getElementById('video');
            const captureBtn = document.getElementById('captureFaceBtn');
            const faceIndicator = document.getElementById('faceIndicator');

            detectionInterval = setInterval(async () => {
                if (!modelsLoaded) return;

                try {
                    const detection = await faceapi.detectSingleFace(
                        video,
                        new faceapi.TinyFaceDetectorOptions()
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
            }, 500);
        }

        async function captureFace() {
            const video = document.getElementById('video');
            const btn = document.getElementById('captureFaceBtn');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Proses...';

            try {
                const detection = await faceapi.detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions()
                ).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    alert('Wajah tidak terdeteksi. Coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture';
                    return;
                }

                faceDescriptor = Array.from(detection.descriptor);
                document.getElementById('faceDescriptor').value = JSON.stringify(faceDescriptor);
                document.getElementById('submitBtn').disabled = false;

                if (detectionInterval) {
                    clearInterval(detectionInterval);
                }

                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Berhasil!';
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-green-500');

                alert('Wajah berhasil! Klik Daftar untuk selesai.');
            } catch (error) {
                console.error('Capture error:', error);
                alert('Error. Coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture';
            }
        }
    </script>
</body>
</html>