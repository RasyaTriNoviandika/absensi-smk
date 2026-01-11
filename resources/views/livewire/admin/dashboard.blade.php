<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-1">Ringkasan absensi hari ini</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Total Siswa</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Hadir Hari Ini</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['present_today'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Terlambat Hari Ini</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['late_today'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Alpha Hari Ini</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['alpha_today'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Pending Approval</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['pending_approval'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-clock text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Weekly Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik 7 Hari Terakhir</h3>
            <div class="h-64">
                <canvas id="weeklyChart" wire:ignore></canvas>
            </div>
        </div>

        <!-- Recent Attendances -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Absensi Terbaru</h3>
            </div>
            <div class="max-h-80 overflow-y-auto">
                @forelse($recentAttendances as $attendance)
                    <div class="px-6 py-3 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">{{ $attendance->user->name }}</p>
                                <p class="text-xs text-gray-600">{{ $attendance->user->class }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-600">{{ $attendance->created_at->diffForHumans() }}</p>
                                @if($attendance->status == 'hadir')
                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Hadir</span>
                                @elseif($attendance->status == 'terlambat')
                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Terlambat</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada absensi hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Menu Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('admin.approvals') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-user-check text-2xl text-purple-600"></i>
                </div>
                <p class="text-sm font-semibold text-gray-800 text-center">Approval</p>
            </a>

            <a href="{{ route('admin.monitoring') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-eye text-2xl text-blue-600"></i>
                </div>
                <p class="text-sm font-semibold text-gray-800 text-center">Monitoring</p>
            </a>

            <a href="{{ route('admin.history') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-all">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-history text-2xl text-green-600"></i>
                </div>
                <p class="text-sm font-semibold text-gray-800 text-center">History</p>
            </a>

            <a href="{{ route('admin.reports') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-all">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-chart-bar text-2xl text-orange-600"></i>
                </div>
                <p class="text-sm font-semibold text-gray-800 text-center">Laporan</p>
            </a>
        </div>
    </div>

<!-- Loading Overlay -->
<div wire:loading class="fixed inset-0 bg-white bg-opacity-95 flex items-center justify-center z-50">
    <div class="text-center">
        <div class="inline-block w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
        <p class="text-gray-800 font-semibold">Memuat data...</p>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('livewire:init', () => {
    const data = @json($weeklyData);
    const ctx = document.getElementById('weeklyChart');

    if (!ctx || !data.length) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Kehadiran',
                data: data.map(d => d.count),
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
