
<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">QR Code</h1>
            <p class="text-gray-600">Gunakan QR Code jika kamera wajah bermasalah</p>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-blue-900 mb-2">Cara Menggunakan:</p>
                    <ul class="text-sm text-blue-800 space-y-1 ml-4 list-disc">
                        <li>Tunjukkan QR Code ke guru untuk absen manual</li>
                        <li>QR Code berlaku selama 30 hari</li>
                        <li>Generate ulang jika expired</li>
                        <li>Simpan screenshot QR Code untuk backup</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- QR Code Display -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="inline-block p-4 bg-white border-4 border-gray-200 rounded-lg">
                    <img id="qrCodeImage" src="{{ $qrCode }}" alt="QR Code" class="w-64 h-64">
                </div>
                
                <div class="mt-4">
                    <p class="text-sm text-gray-600">NISN: <strong>{{ $user->nisn }}</strong></p>
                    <p class="text-sm text-gray-600">Nama: <strong>{{ $user->name }}</strong></p>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-clock mr-1"></i>
                        Berlaku hingga: {{ $user->qr_generated_at?->addDays(30)->isoFormat('D MMMM Y') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <button onclick="downloadQR()" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold">
                    <i class="fas fa-download mr-2"></i>Download QR Code
                </button>

                <button onclick="regenerateQR()" 
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg font-semibold">
                    <i class="fas fa-sync mr-2"></i>Generate Ulang
                </button>

                <a href="{{ route('student.dashboard') }}" 
                    class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 text-center py-3 px-4 rounded-lg font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Warning -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-red-900 mb-1">Peringatan Keamanan!</p>
                    <p class="text-sm text-red-800">
                        Jangan bagikan QR Code ini ke orang lain. 
                        QR Code ini setara dengan identitas Anda untuk absensi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadQR() {
    const link = document.createElement('a');
    link.href = '{{ route("student.qr-code.download") }}';
    link.download = 'qr-code-{{ $user->nisn }}.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

async function regenerateQR() {
    if (!confirm('Generate ulang QR Code? QR Code lama akan tidak berlaku.')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("student.qr-code.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('qrCodeImage').src = result.qr_code;
            alert('QR Code berhasil di-generate ulang!');
            window.location.reload();
        } else {
            alert('Gagal generate QR Code: ' + result.message);
        }
    } catch (error) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>
</x-layouts.app>