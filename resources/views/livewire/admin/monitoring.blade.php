

{{-- ROOT LIVEWIRE (WAJIB 1) --}}
<div>

    {{-- CONTENT UTAMA --}}
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Monitoring Absensi Real-time</h1>
            <p class="text-sm sm:text-base text-gray-600">Pantau kehadiran siswa hari ini</p>
        </div>

        {{-- ... SELURUH KODE KAMU TIDAK BERUBAH ... --}}
    </div>

    {{-- MODAL (MASIH DI DALAM ROOT) --}}
    <div id="photoModal"
        class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50 p-4"
        onclick="closePhotoModal()">

        <div class="relative w-full max-w-4xl" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button onclick="closePhotoModal()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl z-10">
                <i class="fas fa-times"></i>
            </button>

            <!-- Student Name -->
            <div class="bg-white bg-opacity-90 rounded-t px-4 py-2 text-center">
                <p class="text-sm font-semibold text-gray-800">
                    Bukti Surat - <span id="studentName"></span>
                </p>
            </div>

            <!-- Image -->
            <div class="bg-white rounded-b overflow-hidden">
                <img id="modalPhoto" class="w-full h-auto max-h-[70vh] object-contain">
            </div>

            <!-- Buttons -->
            <div class="mt-3 flex flex-col sm:flex-row gap-2 justify-center">
                <a id="downloadPhoto" download
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm text-center">
                    <i class="fas fa-download mr-2"></i>Download
                </a>
                <button onclick="closePhotoModal()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function viewPhoto(photoUrl, studentName) {
    document.getElementById('modalPhoto').src = photoUrl;
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('downloadPhoto').href = photoUrl;
    
    const modal = document.getElementById('photoModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Prevent body scroll when modal open
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
    }
});
</script>
@endpush
