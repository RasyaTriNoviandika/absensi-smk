<div class="p-4 sm:p-6 lg:p-8" wire:poll.60s>
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Monitoring Absensi Real-time</h1>
        <p class="text-sm sm:text-base text-gray-600">Pantau kehadiran siswa hari ini</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" 
                       wire:model="date"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select wire:model="class" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="loadData" 
                        wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm disabled:bg-gray-300">
                    <span wire:loading.remove wire:target="loadData">
                        <i class="fas fa-search mr-2"></i>Filter
                    </span>
                    <span wire:loading wire:target="loadData">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                    </span>
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
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden xl:table-cell">Bukti Pulang Cepat</th>
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
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm hidden xl:table-cell">
                                @if($data['attendance'] && $data['attendance']->early_checkout_photo)
                                    <button onclick="openPhotoModal('{{ asset('storage/' . $data['attendance']->early_checkout_photo) }}')" 
                                            class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-image text-lg"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Mobile Notes Row -->
                        @if($data['attendance'] && $data['attendance']->notes)
                            <tr class="lg:hidden bg-blue-50">
                                <td colspan="7" class="px-3 py-2 text-xs text-gray-700">
                                    <i class="fas fa-sticky-note text-blue-500 mr-1"></i>
                                    <strong>Notes:</strong> {{ $data['attendance']->notes }}
                                </td>
                            </tr>
                        @endif
                        
                        <!-- Mobile Bukti Pulang Cepat Row -->
                        @if($data['attendance'] && $data['attendance']->early_checkout_photo)
                            <tr class="xl:hidden bg-orange-50">
                                <td colspan="7" class="px-3 py-2 text-xs">
                                    <button onclick="openPhotoModal('{{ asset('storage/' . $data['attendance']->early_checkout_photo) }}')" 
                                            class="text-orange-600 hover:text-orange-800">
                                        <i class="fas fa-image mr-1"></i>
                                        <strong>Bukti Pulang Cepat</strong> - Klik untuk lihat
                                    </button>
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
    <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-4 max-w-2xl w-full relative">
            <button onclick="closePhotoModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 z-10">
                <i class="fas fa-times text-xl"></i>
            </button>
            <img id="photoModalImage" src="" alt="Bukti Pulang Cepat" class="w-full h-auto rounded-lg">
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading wire:target="loadData,date,class,status" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
            <p class="text-gray-800 font-semibold">Memuat data...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openPhotoModal(imageUrl) {
    document.getElementById('photoModalImage').src = imageUrl;
    document.getElementById('photoModal').classList.remove('hidden');
    document.getElementById('photoModal').classList.add('flex');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
    document.getElementById('photoModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('photoModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePhotoModal();
    }
});
</script>
@endpush