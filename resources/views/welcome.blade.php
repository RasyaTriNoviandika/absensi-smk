<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Wajah - SMK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-graduation-cap text-3xl text-blue-600"></i>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Absensi SMK</h1>
                        <p class="text-xs text-gray-500">Face Recognition System</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="#login" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="#register" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <div class="mb-8">
                <i class="fas fa-face-smile text-7xl text-blue-600 mb-4"></i>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Absensi Cerdas dengan<br>Teknologi Pengenalan Wajah
            </h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Sistem absensi modern yang praktis, cepat, dan akurat. Cukup scan wajah, absensi tercatat otomatis.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="#cara-kerja" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-lg">
                    Pelajari Cara Kerja
                </a>
                <a href="#register" class="px-8 py-3 bg-white text-blue-600 border-2 border-blue-600 rounded-lg hover:bg-blue-50 transition">
                    Mulai Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Cara Kerja Section -->
    <section id="cara-kerja" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Cara Menggunakan Sistem</h3>
            
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-blue-600">1</span>
                    </div>
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <i class="fas fa-user-plus text-4xl text-blue-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">Daftar Akun</h4>
                        <p class="text-sm text-gray-600">Isi data diri dan registrasi wajah Anda</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-green-600">2</span>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <i class="fas fa-check-circle text-4xl text-green-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">Approval Admin</h4>
                        <p class="text-sm text-gray-600">Tunggu admin verifikasi akun Anda</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <i class="fas fa-sign-in-alt text-4xl text-purple-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">Login Sistem</h4>
                        <p class="text-sm text-gray-600">Masuk dengan username dan password</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-orange-600">4</span>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-lg">
                        <i class="fas fa-camera text-4xl text-orange-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">Scan Wajah</h4>
                        <p class="text-sm text-gray-600">Absen masuk & pulang dengan scan wajah</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Fitur Unggulan</h3>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                    <i class="fas fa-bolt text-4xl text-yellow-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Cepat & Praktis</h4>
                    <p class="text-gray-600">Absensi hanya butuh 2 detik. Tidak perlu antri atau manual input.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                    <i class="fas fa-shield-alt text-4xl text-green-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Aman & Akurat</h4>
                    <p class="text-gray-600">Teknologi AI mendeteksi wajah dengan akurasi tinggi. Anti fraud.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                    <i class="fas fa-chart-line text-4xl text-blue-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Monitoring Real-time</h4>
                    <p class="text-gray-600">Admin dapat memantau absensi siswa secara langsung dan detail.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                    <i class="fas fa-clock text-4xl text-red-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Deteksi Keterlambatan</h4>
                    <p class="text-gray-600">Sistem otomatis menandai status terlambat jika absen lewat jam 07:30.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                    <i class="fas fa-history text-4xl text-purple-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Riwayat Lengkap</h4>
                    <p class="text-gray-600">Lihat history absensi harian, mingguan, hingga bulanan dengan mudah.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                    <i class="fas fa-file-export text-4xl text-indigo-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Export Data</h4>
                    <p class="text-gray-600">Download laporan absensi dalam format Excel atau PDF.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Pertanyaan Umum</h3>
            
            <div class="space-y-4">
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-gray-800 cursor-pointer">Apakah sistem ini gratis?</summary>
                    <p class="mt-3 text-gray-600">Ya, sistem ini gratis untuk seluruh siswa dan staff sekolah.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-gray-800 cursor-pointer">Bagaimana jika wajah saya tidak terdeteksi?</summary>
                    <p class="mt-3 text-gray-600">Pastikan pencahayaan cukup, wajah menghadap kamera, dan tidak ada objek yang menutupi wajah (masker/kacamata). Jika tetap gagal, hubungi admin untuk registrasi ulang.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-gray-800 cursor-pointer">Berapa lama proses approval akun?</summary>
                    <p class="mt-3 text-gray-600">Admin akan melakukan verifikasi maksimal 1x24 jam setelah pendaftaran.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-gray-800 cursor-pointer">Apakah data wajah saya aman?</summary>
                    <p class="mt-3 text-gray-600">Sangat aman. Data wajah disimpan dalam bentuk terenkripsi dan hanya digunakan untuk keperluan absensi sekolah.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-gray-800 cursor-pointer">Jam berapa batas absen masuk?</summary>
                    <p class="mt-3 text-gray-600">Batas absen masuk adalah jam 07:30 WIB. Lewat dari jam tersebut akan tercatat sebagai TERLAMBAT.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-gray-800 cursor-pointer">Bagaimana jika saya lupa password?</summary>
                    <p class="mt-3 text-gray-600">Hubungi admin sekolah untuk reset password. Admin akan memberikan password baru yang bisa Anda ubah setelah login.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-blue-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h3 class="text-3xl font-bold mb-4">Siap Memulai Absensi Digital?</h3>
            <p class="text-xl mb-8 opacity-90">Daftar sekarang dan rasakan kemudahan absensi dengan teknologi wajah</p>
            <div class="flex justify-center space-x-4">
                <a href="#register" class="px-8 py-3 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-semibold">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
                <a href="#login" class="px-8 py-3 border-2 border-white text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="font-bold text-white mb-4">Tentang Sistem</h4>
                    <p class="text-sm">Sistem Absensi Wajah berbasis AI untuk memudahkan proses absensi siswa SMK dengan teknologi modern.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Kelas Tersedia</h4>
                    <ul class="text-sm space-y-1">
                        <li>• DKV (Desain Komunikasi Visual)</li>
                        <li>• SIJA (Sistem Informatika Jaringan)</li>
                        <li>• PB (Pengembangan Perangkat Lunak)</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Kontak</h4>
                    <ul class="text-sm space-y-2">
                        <li><i class="fas fa-envelope mr-2"></i>it.support@sekolah.sch.id</li>
                        <li><i class="fas fa-phone mr-2"></i>0812-XXXX-XXXX</li>
                        <li><i class="fas fa-clock mr-2"></i>Senin - Jumat: 07:00 - 15:00</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm">
                <p>&copy; 2024 SMK [Nama Sekolah]. Dikembangkan dengan <i class="fas fa-heart text-red-500"></i> untuk pendidikan Indonesia.</p>
            </div>
        </div>
    </footer>
</body>
</html>