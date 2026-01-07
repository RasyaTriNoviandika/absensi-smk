@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Siswa</h1>
                <p class="text-gray-600">Kelola data seluruh siswa</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.students.import') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm text-center">
                    <i class="fas fa-file-import mr-2"></i>Import Excel
                </a>
                <a href="{{ route('admin.students.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm text-center">
                    <i class="fas fa-plus mr-2"></i>Tambah Siswa
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.students') }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama atau NISN..." 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <select name="class" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Kelas</option>
                        @php
                            $classes = ['10 DKV 1', '10 DKV 2', '10 DKV 3', '11 DKV 1', '11 DKV 2', '11 DKV 3', '12 DKV 1', '12 DKV 2', '12 DKV 3', '10 SIJA 1', '10 SIJA 2', '10 SIJA 3', '11 SIJA 1', '11 SIJA 2', '11 SIJA 3', '12 SIJA 1', '12 SIJA 2', '12 SIJA 3', '10 PB 1', '10 PB 2', '10 PB 3', '11 PB 1', '11 PB 2', '11 PB 3', '12 PB 1', '12 PB 2', '12 PB 3'];
                        @endphp
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-4 flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="{{ route('admin.students') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Students Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Kelas</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Username</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">HP</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $students->firstItem() + $index }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                                {{ $student->nisn }}
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->email ?? '-' }}</div>
                                <div class="text-xs text-gray-500 md:hidden">{{ $student->class }}</div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800 hidden md:table-cell">
                                {{ $student->class }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800 hidden lg:table-cell">
                                {{ $student->username }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800 hidden xl:table-cell">
                                {{ $student->phone ?? '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @if($student->status == 'approved')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">Aktif</span>
                                @elseif($student->status == 'pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">Pending</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.students.edit', $student) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>

                                    {{-- Reset Password Button --}}
                                    <button onclick="confirmResetPassword({{ $student->id }}, '{{ $student->name }}')"
                                            class="text-orange-600 hover:text-orange-800 transition-colors"
                                            title="Reset Password">
                                        <i class="fas fa-key text-lg"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button onclick="confirmDelete({{ $student->id }}, '{{ $student->name }}')"
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-semibold">Tidak ada data siswa</p>
                                    @if(request('search') || request('class'))
                                        <p class="text-gray-400 text-sm mt-1">Coba ubah filter pencarian</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($students->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
        </div>
        
        <h3 class="text-lg font-bold text-gray-900 text-center mb-2">
            Konfirmasi Hapus
        </h3>
        
        <p class="text-sm text-gray-600 text-center mb-6">
            Apakah Anda yakin ingin menghapus siswa <strong id="deleteStudentName"></strong>? 
            <strong class="text-red-600">Data tidak dapat dikembalikan!</strong>
        </p>
        
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reset Password Modal --}}
<div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-orange-100 rounded-full mb-4">
            <i class="fas fa-key text-2xl text-orange-600"></i>
        </div>
        
        <h3 class="text-lg font-bold text-gray-900 text-center mb-2">
            Reset Password
        </h3>
        
        <p class="text-sm text-gray-600 text-center mb-4">
            Reset password untuk <strong id="resetStudentName"></strong>?
        </p>
        
        <form id="resetPasswordForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="flex space-x-3">
                <button type="button" onclick="closeResetPasswordModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                
                <button type="submit"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>Reset
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(studentId, studentName) {
    document.getElementById('deleteStudentName').textContent = studentName;
    document.getElementById('deleteForm').action = `/admin/students/${studentId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function confirmResetPassword(studentId, studentName) {
    document.getElementById('resetStudentName').textContent = studentName;
    document.getElementById('resetPasswordForm').action = `/admin/students/${studentId}/reset-password`;
    document.getElementById('resetPasswordModal').classList.remove('hidden');
    document.getElementById('resetPasswordModal').classList.add('flex');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    document.getElementById('resetPasswordModal').classList.remove('flex');
}

// Close modals when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.getElementById('resetPasswordModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeResetPasswordModal();
});
</script>
@endpush
@endsection