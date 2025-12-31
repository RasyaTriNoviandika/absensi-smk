@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
        <p class="text-gray-600">Konfigurasi sistem absensi</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <h3 class="text-lg font-bold text-gray-800 mb-4">Waktu Absensi</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>Batas Waktu Absen Masuk
                    </label>
                    <input type="time" 
                           name="check_in_time_limit" 
                           value="{{ $settings['check_in_time_limit']->value ?? '07:30' }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Siswa yang absen setelah waktu ini akan ditandai TERLAMBAT</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>Jam Minimal Absen Pulang
                    </label>
                    <input type="time" 
                           name="check_out_time_min" 
                           value="{{ $settings['check_out_time_min']->value ?? '14:00' }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Waktu minimal untuk melakukan absen pulang</p>
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Sekolah</h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-school text-green-600 mr-2"></i>Nama Sekolah
                    </label>
                    <input type="text" 
                           name="school_name" 
                           value="{{ $settings['school_name']->value ?? 'SMK Negeri 1' }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <hr class="my-6">

                <!-- ✅ TAMBAHAN: Koordinat & Radius -->
                <h3 class="text-lg font-bold text-gray-800 mb-4">Lokasi Sekolah</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>Latitude
                    </label>
                    <input type="text" 
                           name="school_latitude" 
                           value="{{ $settings['school_latitude']->value ?? '-6.2706589' }}"
                           required
                           pattern="-?\d+\.?\d*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Contoh: -6.2706589 (Gunakan Google Maps untuk mendapatkan koordinat)</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>Longitude
                    </label>
                    <input type="text" 
                           name="school_longitude" 
                           value="{{ $settings['school_longitude']->value ?? '106.9593685' }}"
                           required
                           pattern="-?\d+\.?\d*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Contoh: 106.9593685</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-ruler text-blue-600 mr-2"></i>Radius Maksimal (meter)
                    </label>
                    <input type="number" 
                           name="max_distance_meters" 
                           value="{{ $settings['max_distance_meters']->value ?? '100' }}"
                           required
                           min="10"
                           max="1000"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Jarak maksimal siswa dari sekolah untuk bisa absen (Recommended: 100 meter)
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-6">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Cara mendapatkan koordinat:</strong><br>
                        1. Buka Google Maps<br>
                        2. Klik kanan pada lokasi sekolah<br>
                        3. Pilih koordinat yang muncul (format: -6.2706589, 106.9593685)<br>
                        4. Copy dan paste ke form ini
                    </p>
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-bold text-gray-800 mb-4">Face Recognition</h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sliders-h text-orange-600 mr-2"></i>Threshold Pencocokan Wajah
                    </label>
                    <input type="number" 
                           name="face_match_threshold" 
                           value="{{ $settings['face_match_threshold']->value ?? '0.6' }}"
                           step="0.01"
                           min="0.3"
                           max="0.9"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Nilai 0.3-0.9. Semakin kecil = semakin strict. <strong>Recommended: 0.6</strong>
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection