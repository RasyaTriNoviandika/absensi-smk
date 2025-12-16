@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Absensi</h1>
            <p class="text-gray-600">Lihat seluruh history absensi Anda</p>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="month" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="year" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attendances as $index => $attendance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $attendances->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $attendance->date->isoFormat('dddd, D MMMM Y') }}
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
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>Tidak ada data absensi</p>
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
</div>
@endsection
