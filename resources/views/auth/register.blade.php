<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Absensi SMK</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
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
                <form method="POST" action="{{ route('register') }}" id="registerForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Step 1: Personal Data -->
                    <div id="step1">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Data Pribadi</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NISN *</label>
                                <input type="text" name="nisn" maxlength="10" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">10 digit</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" name="name" required
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
                                        <option value="{{ $class }}">{{ $class }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                                <input type="tel" name="phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                                <input type="text" name="username" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <input type="password" name="password" required minlength="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Min. 8 karakter</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="button" onclick="goToStep2()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                            Lanjut ke Registrasi Wajah <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>

                    <!-- Step 2: Face Registration -->
                    <div id="step2" class="hidden">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Registrasi Wajah</h3>

                        <div class="bg-gray-900 rounded-lg overflow-hidden mb-4" style="height: 480px;">
                            <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Instruksi:</strong> Pastikan wajah Anda terlihat jelas, pencahayaan cukup, dan tidak ada objek yang menutupi wajah.
                            </p>
                        </div>

                        <input type="hidden" name="face_descriptor" id="faceDescriptor">

                        <div class="flex space-x-3">
                            <button type="button" onclick="goToStep1()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </button>

                            <button type="button" id="captureFaceBtn" onclick="captureFace()" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/face-api.js/0.22.2/face-api.min.js"></script>
    <script>
        let modelsLoaded = false;
        let faceDescriptor = null;

        async function loadModels() {
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            modelsLoaded = true;
        }

        function goToStep1() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
        }

        async function goToStep2() {
            // Validate step 1 fields
            const form = document.getElementById('registerForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');

            if (!modelsLoaded) {
                alert('Loading face recognition models...');
                await loadModels();
            }

            // Start camera
            const video = document.getElementById('video');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: 640, height: 480 } 
                });
                video.srcObject = stream;
            } catch (err) {
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.');
            }
        }

        async function captureFace() {
            const video = document.getElementById('video');
            const btn = document.getElementById('captureFaceBtn');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection) {
                    alert('Wajah tidak terdeteksi. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture Wajah';
                    return;
                }

                faceDescriptor = Array.from(detection.descriptor);
                document.getElementById('faceDescriptor').value = JSON.stringify(faceDescriptor);
                document.getElementById('submitBtn').disabled = false;

                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Wajah Berhasil Diambil!';
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-green-500');

                alert('Wajah berhasil didaftarkan! Klik "Daftar Sekarang" untuk menyelesaikan registrasi.');
            } catch (error) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-camera mr-2"></i>Capture Wajah';
            }
        }

        window.addEventListener('load', loadModels);
    </script>
</body>
</html>
