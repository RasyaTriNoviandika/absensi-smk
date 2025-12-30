<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Riwayat Absensi</h1>
            <p class="text-sm sm:text-base text-gray-600">Lihat seluruh history absensi Anda</p>
        </div>

        <!-- Filter - Mobile Friendly -->
        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 mb-4 sm:mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select wire:model.live="month" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select wire:model.live="year" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('student.dashboard') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold text-sm text-center">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Table - Mobile Friendly -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masuk</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pulang</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attendances as $index => $attendance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-800">
                                    {{ $attendances->firstItem() + $index }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-800">
                                    <!-- Mobile: Format pendek -->
                                    <span class="sm:hidden">{{ $attendance->date->format('d/m/y') }}</span>
                                    <!-- Desktop: Format lengkap -->
                                    <span class="hidden sm:inline">{{ $attendance->date->isoFormat('dddd, D MMM Y') }}</span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-800">
                                    {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-800">
                                    {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    @if($attendance->status == 'hadir')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">Hadir</span>
                                    @elseif($attendance->status == 'terlambat')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">Telat</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">Alpha</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    <i class="fas fa-inbox text-2xl sm:text-3xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada data absensi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-3 sm:px-6 py-4 border-t border-gray-200">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
