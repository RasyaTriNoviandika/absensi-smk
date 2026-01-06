@extends('layouts.admin')

@section('title', 'Import Siswa')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Import Data Siswa</h1>
        <p class="text-gray-600">Upload file Excel untuk import banyak siswa sekaligus</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            
            @if(session('import_results'))
                <div class="mt-3 p-3 bg-white rounded border border-green-200">
                    <p class="font-semibold mb-2">Hasil Import:</p>
                    <ul class="text-sm space-y-1">
                        <li>✅ Total: {{ session('import_results')['total'] }} baris</li>
                        <li>✅ Berhasil: {{ session('import_results')['success'] }}</li>
                        <li>❌ Gagal: {{ session('import_results')['failed'] }}</li>
                    </ul>
                    <a href="{{ asset('storage/' . session('import_results')['file']) }}" 
                       class="mt-3 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-download mr-2"></i>Download Hasil Detail
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Error:</p>
            <ul class="text-sm list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-upload text-blue-600 mr-2"></i>Upload File Excel
            </h3>

            <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" 
                           name="file" 
                           accept=".xlsx,.xls,.csv"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Max 5MB</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4 text-sm">
                    <p class="text-blue-800 font-semibold mb-2">
                        <i class="fas fa-info-circle mr-1"></i>Informasi:
                    </p>
                    <ul class="text-blue-700 space-y-1 ml-4 list-disc">
                        <li>Username & password akan di-generate otomatis</li>
                        <li>Status awal: <strong>Pending</strong> (perlu approval)</li>
                        <li>Siswa belum bisa login sampai di-approve</li>
                        <li>Hasil import bisa didownload untuk dibagikan ke siswa</li>
                    </ul>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-upload mr-2"></i>Upload & Import
                </button>
            </form>
        </div>

        <!-- Template & Instruksi -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-file-download text-green-600 mr-2"></i>Download Template
            </h3>

            <p class="text-sm text-gray-600 mb-4">
                Download template Excel terlebih dahulu, lalu isi dengan data siswa.
            </p>

            <a href="{{ route('admin.students.download-template') }}" 
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold mb-6">
                <i class="fas fa-download mr-2"></i>Download Template
            </a>

            <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                <p class="text-yellow-800 font-semibold mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Format Excel:
                </p>
                <table class="w-full text-xs border-collapse border border-yellow-300">
                    <thead>
                        <tr class="bg-yellow-100">
                            <th class="border border-yellow-300 px-2 py-1">NISN</th>
                            <th class="border border-yellow-300 px-2 py-1">Nama</th>
                            <th class="border border-yellow-300 px-2 py-1">Kelas</th>
                            <th class="border border-yellow-300 px-2 py-1">No HP</th>
                            <th class="border border-yellow-300 px-2 py-1">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-yellow-300 px-2 py-1">1234567890</td>
                            <td class="border border-yellow-300 px-2 py-1">John Doe</td>
                            <td class="border border-yellow-300 px-2 py-1">10 DKV 1</td>
                            <td class="border border-yellow-300 px-2 py-1">081234567890</td>
                            <td class="border border-yellow-300 px-2 py-1">john@mail.com</td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-xs text-yellow-700 mt-2">
                    <strong>Wajib:</strong> NISN, Nama, Kelas<br>
                    <strong>Opsional:</strong> No HP, Email
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.students') }}" 
           class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Data Siswa
        </a>
    </div>
</div>
@endsection