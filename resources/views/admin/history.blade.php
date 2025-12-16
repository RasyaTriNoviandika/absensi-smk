@extends('layouts.admin')

@section('title', 'History Absensi')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">History Absensi</h1>
        <p class="text-gray-600">Lihat riwayat absensi seluruh siswa</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ request('start_date') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ request('end_date') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="class" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>

        <div class="mt-4 flex space-x-2">
            <a href="{{ route('admin.export.excel', request()->all()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </a>

            <a href="{{ route('admin.export.pdf', request()->all()) }}" 
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
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
</div>
@endsection