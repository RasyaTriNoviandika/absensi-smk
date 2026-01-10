<div>
    @if ($status === 'pending')
        <div class="p-4 mb-4 text-yellow-800 bg-yellow-100 border border-yellow-300 rounded">
            Akun Anda sedang menunggu persetujuan admin.
        </div>

    @elseif ($status === 'rejected')
        <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-300 rounded">
            Pendaftaran Anda ditolak. Silakan hubungi admin.
        </div>

    @elseif ($status === 'approved')
        <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded">
            Akun Anda telah disetujui. Silakan lanjutkan.
        </div>
    @endif
</div>
