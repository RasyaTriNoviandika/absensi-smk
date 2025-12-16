@extends('layouts.admin')

@section('title', 'Laporan Absensi')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Absensi Bulanan</h1>
        <p class="text-gray-600">Rekap kehadiran siswa per bulan</p>
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

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="class" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-search mr-2"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hadir</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Alpha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Persentase</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reportData as $data)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $data['student']->nisn }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                {{ $data['student']->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $data['student']->class }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 font-semibold">
                                    {{ $data['hadir'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold">
                                    {{ $data['terlambat'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 font-semibold">
                                    {{ $data['alpha'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $data['percentage'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection