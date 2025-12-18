@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['present_today'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Terlambat Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['late_today'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Alpha Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['alpha_today'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_approval'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-user-clock text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Weekly Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik 7 Hari Terakhir</h3>
            <canvas id="weeklyChart" height="200"></canvas>
        </div>

        <!-- Recent Attendances -->
        <div class="bg-white rounded-lg shadow-sm">
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
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada absensi hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.approvals') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
            <i class="fas fa-user-check text-2xl text-purple-600 mb-2"></i>
            <p class="text-sm font-semibold text-gray-800">Approval</p>
        </a>

        <a href="{{ route('admin.monitoring') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
            <i class="fas fa-eye text-2xl text-blue-600 mb-2"></i>
            <p class="text-sm font-semibold text-gray-800">Monitoring</p>
        </a>

        <a href="{{ route('admin.history') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
            <i class="fas fa-history text-2xl text-green-600 mb-2"></i>
            <p class="text-sm font-semibold text-gray-800">History</p>
        </a>

        <a href="{{ route('admin.reports') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition text-center">
            <i class="fas fa-chart-bar text-2xl text-orange-600 mb-2"></i>
            <p class="text-sm font-semibold text-gray-800">Laporan</p>
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('weeklyChart');
const weeklyData = @json($weeklyData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: weeklyData.map(d => d.date),
        datasets: [{
            label: 'Kehadiran',
            data: weeklyData.map(d => d.count),
            borderColor: 'rgb(37, 99, 235)',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 10
                }
            }
        }
    }
});
</script>
@endpush
@endsection