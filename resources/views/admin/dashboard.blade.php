@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-gray-600">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['present_today'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Terlambat Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['late_today'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Alpha Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['alpha_today'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Approval</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['pending_approval'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-user-clock text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Absensi 7 Hari Terakhir</h3>
            <canvas id="attendanceChart" height="100"></canvas>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.approvals') }}" 
                   class="flex items-center justify-between p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                    <div class="flex items-center">
                        <i class="fas fa-user-check text-purple-600 mr-3"></i>
                        <span class="text-sm font-semibold text-gray-700">Approval Siswa</span>
                    </div>
                    @if($stats['pending_approval'] > 0)
                        <span class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full">{{ $stats['pending_approval'] }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.monitoring') }}" 
                   class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    <i class="fas fa-eye text-blue-600 mr-3"></i>
                    <span class="text-sm font-semibold text-gray-700">Monitoring Real-time</span>
                </a>

                <a href="{{ route('admin.history') }}" 
                   class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition">
                    <i class="fas fa-history text-green-600 mr-3"></i>
                    <span class="text-sm font-semibold text-gray-700">History Absensi</span>
                </a>

                <a href="{{ route('admin.students') }}" 
                   class="flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                    <i class="fas fa-users text-orange-600 mr-3"></i>
                    <span class="text-sm font-semibold text-gray-700">Data Siswa</span>
                </a>

                <a href="{{ route('admin.reports') }}" 
                   class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                    <i class="fas fa-chart-bar text-indigo-600 mr-3"></i>
                    <span class="text-sm font-semibold text-gray-700">Laporan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Attendances -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Absensi Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentAttendances as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">{{ substr($attendance->user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-800">{{ $attendance->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $attendance->user->nisn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->user->class }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $attendance->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->status == 'hadir')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">HADIR</span>
                                @elseif($attendance->status == 'terlambat')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">TERLAMBAT</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada absensi hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
const weeklyData = @json($weeklyData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: weeklyData.map(d => d.date),
        datasets: [
            {
                label: 'Hadir',
                data: weeklyData.map(d => d.hadir),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            },
            {
                label: 'Terlambat',
                data: weeklyData.map(d => d.terlambat),
                borderColor: 'rgb(234, 179, 8)',
                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                tension: 0.4
            },
            {
                label: 'Alpha',
                data: weeklyData.map(d => d.alpha),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5
                }
            }
        }
    }
});
</script>
@endpush

{{-- resources/views/admin/approvals.blade.php --}}
@extends('layouts.admin')

@section('title', 'Approval Siswa')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Approval Siswa Baru</h1>
        <p class="text-gray-600">Verifikasi dan approve pendaftaran siswa</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm">
        @if($pendingUsers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($pendingUsers as $user)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-center mb-4">
                            @if($user->profile_photo)
                                <img src="{{ Storage::url($user->profile_photo) }}" 
                                     class="w-24 h-24 rounded-full mx-auto mb-3 object-cover">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-3 flex items-center justify-center">
                                    <i class="fas fa-user text-3xl text-gray-400"></i>
                                </div>
                            @endif
                            <h3 class="font-bold text-gray-800 text-lg">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $user->class }}</p>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">NISN:</span>
                                <span class="font-semibold">{{ $user->nisn }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Username:</span>
                                <span class="font-semibold">{{ $user->username }}</span>
                            </div>
                            @if($user->phone)
                            <div class="flex justify-between">
                                <span class="text-gray-600">HP:</span>
                                <span class="font-semibold">{{ $user->phone }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Daftar:</span>
                                <span class="font-semibold">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <form action="{{ route('admin.approve', $user) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                            </form>

                            <form action="{{ route('admin.reject', $user) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" 
                                    onclick="return confirm('Yakin reject siswa ini?')"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pendingUsers->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Tidak ada siswa yang menunggu approval</p>
            </div>
        @endif
    </div>
</div>
@endsection

{{-- resources/views/admin/monitoring.blade.php --}}
@extends('layouts.admin')

@section('title', 'Monitoring Absensi')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Monitoring Absensi Real-time</h1>
        <p class="text-gray-600">Pantau kehadiran siswa hari ini</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="class" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Student List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monitoringData as $data)
                        <tr class="{{ $data['status'] == 'alpha' ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800">{{ $data['student']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $data['student']->nisn }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $data['student']->class }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $data['check_in'] ? $data['check_in']->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ $data['check_out'] ? $data['check_out']->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($data['status'] == 'hadir')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">HADIR</span>
                                @elseif($data['status'] == 'terlambat')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">TERLAMBAT</span>
                                @else
                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">ALPHA</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection