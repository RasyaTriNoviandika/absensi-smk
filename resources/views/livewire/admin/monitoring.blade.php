<div class="p-4 sm:p-6 lg:p-8">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Monitoring Absensi Real-time</h1>
        <p class="text-sm sm:text-base text-gray-600">Pantau kehadiran siswa hari ini</p>
        <a href="{{ route('admin.qr-scanner') }}" 
        class="flex items-center px-4 py-3 rounded-lg mt-2
        {{ request()->routeIs('admin.qr-scanner') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-qrcode w-5"></i>
            <span class="ml-3 font-medium">QR Scanner</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" 
                       wire:model.live="date"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select wire:model.live="class" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" wire:click="loadData" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors">
                    <i class="fas fa-sync mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 border-l-4 border-gray-500">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Total</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $total }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 border-l-4 border-green-500">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Hadir</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $hadir }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 border-l-4 border-yellow-500">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Terlambat</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $terlambat }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 border-l-4 border-red-500">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Alpha</p>
            <p class="text-xl sm:text-2xl font-bold text-red-600">{{ $alpha }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Kelas</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masuk</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pulang</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Notes</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($monitoringData as $index => $data)
                        <tr class="{{ $data['status'] == 'alpha' ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                <div class="font-semibold text-gray-800">{{ $data['student']->name }}</div>
                                <div class="text-xs text-gray-500 sm:hidden">{{ $data['student']->class }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800 hidden sm:table-cell">
                                {{ $data['student']->class }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800">
                                {{ $data['check_in'] ? $data['check_in']->format('H:i') : '-' }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800">
                                {{ $data['check_out'] ? $data['check_out']->format('H:i') : '-' }}
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                @if($data['status'] == 'hadir')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">Hadir</span>
                                @elseif($data['status'] == 'terlambat')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">Telat</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">Alpha</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-600 hidden lg:table-cell max-w-xs">
                                @if($data['attendance'] && $data['attendance']->notes)
                                    <div class="truncate" title="{{ $data['attendance']->notes }}">
                                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                        {{ $data['attendance']->notes }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                @if($data['attendance'] && $data['attendance']->early_checkout_photo)
                                    @php
                                        $photoPath = $data['attendance']->early_checkout_photo;
                                        
                                        // Generate secure URL untuk modal (full size)
                                        $secureUrl = url('/secure-photo/' . $photoPath);
                                        
                                        // Thumbnail URL (langsung dari storage public)
                                        $thumbnailUrl = asset('storage/' . $photoPath);
                                    @endphp
                                    
                                    <img
                                        src="{{ $thumbnailUrl }}"
                                        alt="Bukti surat"
                                        title="Klik untuk melihat foto"
                                        class="w-16 h-16 object-cover rounded-lg cursor-pointer border-2 border-gray-200 shadow-sm hover:scale-105 hover:border-blue-400 hover:shadow-md transition-all duration-200"
                                        onclick="viewPhoto('{{ $secureUrl }}', '{{ addslashes($data['student']->name) }}')"
                                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3ENo Image%3C/text%3E%3C/svg%3E'"
                                    >
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                        
                        @if($data['attendance'] && $data['attendance']->notes)
                            <tr class="lg:hidden bg-blue-50">
                                <td colspan="8" class="px-3 py-2 text-xs text-gray-700">
                                    <i class="fas fa-sticky-note text-blue-500 mr-1"></i>
                                    <strong>Notes:</strong> {{ $data['attendance']->notes }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 text-sm">
                                <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                                <p>Tidak ada data siswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photoModal" 
         class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50 p-4"
         onclick="closePhotoModal()">
        <div class="relative w-full max-w-4xl" onclick="event.stopPropagation()">
            <button type="button" onclick="closePhotoModal()"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl z-10 transition-colors">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="bg-white bg-opacity-95 rounded-t px-4 py-3 text-center">
                <p class="text-base font-bold text-gray-800">
                    Bukti Surat Izin Pulang Cepat
                </p>
                <p class="text-sm text-gray-600" id="studentName"></p>
            </div>
            
            <div class="bg-white rounded-b overflow-hidden">
                <img id="modalPhoto" src="" alt="Bukti Pulang Cepat" class="w-full h-auto max-h-[70vh] object-contain">
            </div>
            
            <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-center">
                <a id="downloadPhoto" href="" download
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold text-center transition-colors">
                    <i class="fas fa-download mr-2"></i>Download Foto
                </a>
                <button type="button" onclick="closePhotoModal()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-white bg-opacity-95 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="inline-block w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
            <p class="text-gray-800 font-semibold">Memuat data...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewPhoto(photoUrl, studentName) {
    const modal = document.getElementById('photoModal');
    const img = document.getElementById('modalPhoto');
    const nameEl = document.getElementById('studentName');
    const downloadLink = document.getElementById('downloadPhoto');
    
    // Validasi URL
    if (!photoUrl || photoUrl === '') {
        console.error('URL foto tidak valid');
        alert('URL foto tidak valid');
        return;
    }
    
    // Set image dan info
    img.src = photoUrl;
    nameEl.textContent = studentName || 'Tidak diketahui';
    downloadLink.href = photoUrl;
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal dengan Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
    }
});

// Pastikan modal tertutup saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    closePhotoModal();
});
</script>
@endpush