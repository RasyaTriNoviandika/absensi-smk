@extends('layouts.admin')

@section('title', 'Approval Siswa')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Approval Siswa</h1>
        <p class="text-gray-600 mt-1">Verifikasi pendaftaran siswa baru</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-start">
            <i class="fas fa-check-circle mr-3 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-start">
            <i class="fas fa-exclamation-circle mr-3 mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($pendingUsers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Kontak</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Tanggal Daftar</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingUsers as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $user->nisn }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 sm:hidden">{{ $user->email ?? '-' }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $user->class }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $user->username }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800 hidden sm:table-cell">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-800 hidden md:table-cell">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                                        <form action="{{ route('admin.approve', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                onclick="return confirm('Approve siswa {{ $user->name }}?')"
                                                class="w-full sm:w-auto px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold transition-colors">
                                                <i class="fas fa-check mr-1"></i>Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.reject', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                onclick="return confirm('Tolak pendaftaran {{ $user->name }}?')"
                                                class="w-full sm:w-auto px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold transition-colors">
                                                <i class="fas fa-times mr-1"></i>Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                {{ $pendingUsers->links() }}
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-4xl text-green-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Semua Sudah Diverifikasi</h3>
                <p class="text-gray-600">Tidak ada siswa yang menunggu approval</p>
            </div>
        @endif
    </div>
</div>
@endsection