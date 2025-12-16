@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Siswa</h1>
        <p class="text-gray-600">Kelola data seluruh siswa</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       placeholder="Cari nama atau NISN..." 
                       value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>

            <div class="flex-1 min-w-[200px]">
                <select name="class" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">HP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $student->nisn }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $student->class }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $student->username }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $student->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.students.edit', $student) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.students.reset-password', $student) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Reset password siswa ini?')">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.students.delete', $student) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Yakin hapus siswa ini? Data tidak dapat dikembalikan!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data siswa
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection