<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Siswa</h1>
        <p class="text-gray-600">Kelola data seluruh siswa</p>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama atau NISN..." 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <select wire:model.live="class" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Kelas</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Username</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">HP</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors">
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
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.students.edit', $student) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>

                                    {{-- Reset Password Button --}}
                                    <button wire:click="confirmResetPassword({{ $student->id }})"
                                            class="text-orange-600 hover:text-orange-800 transition-colors"
                                            title="Reset Password">
                                        <i class="fas fa-key text-lg"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button wire:click="confirmDelete({{ $student->id }})"
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-semibold">Tidak ada data siswa</p>
                                    @if($search || $class)
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

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">
                    Konfirmasi Hapus
                </h3>
                
                <p class="text-sm text-gray-600 text-center mb-6">
                    Apakah Anda yakin ingin menghapus siswa ini? 
                    <strong class="text-red-600">Data tidak dapat dikembalikan!</strong>
                </p>
                
                <div class="flex space-x-3">
                    <button wire:click="cancelDelete"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    
                    <button wire:click="deleteStudent"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors disabled:bg-gray-400">
                        <span wire:loading.remove wire:target="deleteStudent">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </span>
                        <span wire:loading wire:target="deleteStudent">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Reset Password Modal --}}
    @if($showResetPasswordModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-orange-100 rounded-full mb-4">
                    <i class="fas fa-key text-2xl text-orange-600"></i>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">
                    Reset Password
                </h3>
                
                <p class="text-sm text-gray-600 text-center mb-4">
                    Password baru akan di-generate secara otomatis.
                </p>
                
                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-6">
                    <p class="text-xs text-gray-600 mb-1">Password Baru:</p>
                    <p class="text-2xl font-bold text-gray-800 text-center font-mono tracking-wider">
                        {{ $newPassword }}
                    </p>
                    <p class="text-xs text-orange-600 text-center mt-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Catat password ini untuk diberikan ke siswa
                    </p>
                </div>
                
                <div class="flex space-x-3">
                    <button wire:click="cancelResetPassword"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    
                    <button wire:click="resetPassword"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors disabled:bg-gray-400">
                        <span wire:loading.remove wire:target="resetPassword">
                            <i class="fas fa-check mr-2"></i>Reset
                        </span>
                        <span wire:loading wire:target="resetPassword">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Proses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="deleteStudent,resetPassword" 
         class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-40">
        <div class="bg-white rounded-lg p-6 text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
            <p class="text-gray-800 font-semibold">Memproses...</p>
        </div>
    </div>
</div>