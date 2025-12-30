<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Approval Siswa</h1>
        <p class="text-sm sm:text-base text-gray-600">Approve atau reject pendaftaran siswa baru</p>
    </div>

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

    <!-- Summary Card -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-600 mb-1">Total Pending</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $pendingUsers->total() }}</p>
            </div>
            <div class="bg-purple-100 p-3 sm:p-4 rounded-lg">
                <i class="fas fa-user-clock text-2xl sm:text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NISN</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Kelas</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Email</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Tanggal Daftar</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingUsers as $index => $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800">
                                {{ $pendingUsers->firstItem() + $index }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800 font-mono">
                                {{ $user->nisn }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 sm:hidden">{{ $user->class }}</div>
                                <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-800 hidden sm:table-cell">
                                {{ $user->class }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-600 hidden md:table-cell">
                                {{ $user->email ?? '-' }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-600 hidden lg:table-cell">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button wire:click="approve({{ $user->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="approve({{ $user->id }})"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg font-semibold text-xs sm:text-sm transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="approve({{ $user->id }})">
                                            <i class="fas fa-check mr-1"></i>Approve
                                        </span>
                                        <span wire:loading wire:target="approve({{ $user->id }})">
                                            <i class="fas fa-spinner fa-spin mr-1"></i>Loading...
                                        </span>
                                    </button>
                                    
                                    <button wire:click="reject({{ $user->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="reject({{ $user->id }})"
                                            onclick="return confirm('Yakin reject siswa {{ $user->name }}?')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold text-xs sm:text-sm transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="reject({{ $user->id }})">
                                            <i class="fas fa-times mr-1"></i>Reject
                                        </span>
                                        <span wire:loading wire:target="reject({{ $user->id }})">
                                            <i class="fas fa-spinner fa-spin mr-1"></i>Loading...
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl sm:text-5xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 font-semibold text-sm sm:text-base">Tidak ada siswa pending</p>
                                    <p class="text-gray-400 text-xs sm:text-sm mt-1">Semua siswa sudah di-approve atau reject</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingUsers->hasPages())
            <div class="px-3 sm:px-6 py-4 border-t border-gray-200">
                {{ $pendingUsers->links() }}
            </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div wire:loading wire:target="approve,reject" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
            <p class="text-gray-800 font-semibold">Memproses...</p>
        </div>
    </div>
</div>
