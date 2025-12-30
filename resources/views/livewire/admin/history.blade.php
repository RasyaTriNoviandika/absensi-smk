<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">History Absensi</h1>
        <p class="text-gray-600">Lihat riwayat absensi seluruh siswa</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" 
                       wire:model="start_date"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" 
                       wire:model="end_date"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select wire:model="class" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button wire:click="$refresh" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </div>

        <div class="mt-4 flex space-x-2">
            <a href="{{ route('admin.export.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'class' => $class, 'status' => $status]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </a>

            <a href="{{ route('admin.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'class' => $class, 'status' => $status]) }}" 
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti Pulang Cepat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->user->nisn }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                {{ $attendance->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->user->class }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->status == 'hadir')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">HADIR</span>
                                @elseif($attendance->status == 'terlambat')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">TERLAMBAT</span>
                                @else
                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">ALPHA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($attendance->early_checkout_photo)
                                    <button onclick="openPhotoModal('{{ asset('storage/' . $attendance->early_checkout_photo) }}')" 
                                            class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-image text-lg"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data absensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $attendances->links() }}
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
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
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