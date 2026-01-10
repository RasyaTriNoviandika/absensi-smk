<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <nav class="border-b border-gray-300 sticky top-0 bg-white z-50 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-14">
                <div class="flex items-center space-x-2">
                   <img 
                    src="{{ asset('img/logo.png') }}"
                    class="w-9 h-9 mr-3 object-contain" >
                    <span class="font-semibold text-gray-800">Absensi SMKN 9 Kota Bekasi</span>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm text-gray-700 hover:text-gray-900 transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-16 px-4 sm:py-24 fade-in">
        <div class="max-w-3xl mx-auto text-center">
            <div class="mb-6">
                <i class="fas fa-camera text-5xl sm:text-6xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 mb-4 leading-tight">
                Absensi dengan<br class="sm:hidden"> Pengenalan Wajah
            </h1>
            <p class="text-base font sm:text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
             Scan wajah, Absen tercatat otomatis.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="#panduan" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm sm:text-base transition-all hover-lift">
                    Cara Menggunakan
                </a>
                <a href="{{ route('register') }}" class="px-6 py-3 border border-gray-300 text-gray-800 rounded hover:bg-gray-50 text-sm sm:text-base transition-all hover-lift">
                    Mulai Daftar
                </a>
            </div>
        </div>
    </section>

    <!-- Panduan Section -->
    <section id="panduan" class="py-12 sm:py-16 bg-white fade-in">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-10 sm:mb-12">
                Cara Menggunakan
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Step 1 -->
                <div class="bg-gray-50 p-5 sm:p-6 rounded border border-gray-300 text-center hover-lift transition-all">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-lg font-bold text-gray-700">1</span>
                    </div>
                    <i class="fas fa-user-plus text-3xl text-blue-600 mb-3"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Daftar Akun</h3>
                    <p class="text-sm text-gray-600">Isi data diri dan registrasi wajah Anda</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-gray-50 p-5 sm:p-6 rounded border border-gray-300 text-center hover-lift transition-all">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-lg font-bold text-gray-700">2</span>
                    </div>
                    <i class="fas fa-check-circle text-3xl text-blue-600 mb-3"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Menunggu Approval</h3>
                    <p class="text-sm text-gray-600">Admin akan verifikasi dalam 1x24 jam</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-gray-50 p-5 sm:p-6 rounded border border-gray-300 text-center hover-lift transition-all">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-lg font-bold text-gray-700">3</span>
                    </div>
                    <i class="fas fa-sign-in-alt text-3xl text-blue-600 mb-3"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Login Sistem</h3>
                    <p class="text-sm text-gray-600">Masuk dengan username dan password</p>
                </div>

                <!-- Step 4 -->
                <div class="bg-gray-50 p-5 sm:p-6 rounded border border-gray-300 text-center hover-lift transition-all">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-lg font-bold text-gray-700">4</span>
                    </div>
                    <i class="fas fa-camera text-3xl text-blue-600 mb-3"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Mulai Absen</h3>
                    <p class="text-sm text-gray-600">Scan wajah untuk absen masuk/pulang</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Penting -->
    <section class="py-12 sm:py-16 fade-in">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="bg-gray-50 border border-gray-300 p-5 sm:p-6 rounded hover-lift transition-all">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center text-base sm:text-lg">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informasi Penting
                </h3>
                <div class="space-y-2.5 text-sm sm:text-base text-gray-700">
                    <div class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Batas absen masuk: <strong>07:30 WIB</strong> (lewat = terlambat)</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Minimal absen pulang: <strong>16:00 WIB</strong></span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Absen hanya bisa dalam radius <strong>100 meter dari sekolah</strong></span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Pastikan wajah terlihat jelas dan pencahayaan cukup</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Anda bisa absen menggunakan QR Code Apabila Absen wajah bermaslah </span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Aktifkan GPS dan izin kamera di browser Anda</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Singkat -->
    <section class="py-12 sm:py-16 bg-gray-50 fade-in">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-8 sm:mb-10">
                Pertanyaan Umum
            </h2>
            
            <div class="space-y-3">
                <details class="bg-white p-4 rounded border border-gray-300 cursor-pointer hover-lift transition-all">
                    <summary class="font-semibold text-gray-800 text-sm sm:text-base">
                        Apakah sistem ini gratis?
                    </summary>
                    <p class="mt-2 text-sm text-gray-600">
                        Ya, sistem ini 100% gratis untuk seluruh siswa SMKN 9 Kota Bekasi.
                    </p>
                </details>

                <details class="bg-white p-4 rounded border border-gray-300 cursor-pointer hover-lift transition-all">
                    <summary class="font-semibold text-gray-800 text-sm sm:text-base">
                        Bagaimana jika wajah tidak terdeteksi?
                    </summary>
                    <p class="mt-2 text-sm text-gray-600">
                        Pastikan pencahayaan cukup, wajah menghadap kamera, dan tidak ada masker/kacamata. Jika tetap gagal, hubungi guru.
                    </p>
                </details>
                
                <details class="bg-white p-4 rounded border border-gray-300 cursor-pointer hover-lift transition-all">
                    <summary class="font-semibold text-gray-800 text-sm sm:text-base">
                        Bagaimana jika sistem absen wajah bermasalah?
                    </summary>
                    <p class="mt-2 text-sm text-gray-600">
                        Anda bisa absen menggunakan QR Code yang disediakan di halaman absen dan pastikan sudah melapor ke guru.
                    </p>
                </details>

                <details class="bg-white p-4 rounded border border-gray-300 cursor-pointer hover-lift transition-all">
                    <summary class="font-semibold text-gray-800 text-sm sm:text-base">
                        Berapa lama proses approval?
                    </summary>
                    <p class="mt-2 text-sm text-gray-600">
                        Admin akan verifikasi maksimal 1x24 jam setelah pendaftaran.
                    </p>
                </details>

                <details class="bg-white p-4 rounded border border-gray-300 cursor-pointer hover-lift transition-all">
                    <summary class="font-semibold text-gray-800 text-sm sm:text-base">
                        Apakah harus di lokasi sekolah?
                    </summary>
                    <p class="mt-2 text-sm text-gray-600">
                        Ya, absen hanya bisa dilakukan dalam radius 100 meter dari lokasi sekolah.
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 fade-in">
        <div class="max-w-3xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">
                Siap Memulai?
            </h2>
            <p class="text-base sm:text-lg text-gray-600 mb-6">
                
            </p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm sm:text-base transition-all hover-lift">
                <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-300 py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="text-center text-sm text-gray-600">
                <p>&copy; Sistem Absen Wajah | SMKN 9 KOTA BEKASI</p>
            </div>
        </div>
    </footer>
</body>
</html>