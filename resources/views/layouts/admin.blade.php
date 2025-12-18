<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - SMK</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-2xl text-blue-400 mr-3"></i>
                    <div>
                        <h2 class="font-bold text-lg">Admin Panel</h2>
                        <p class="text-xs text-gray-400">Absensi SMK</p>
                    </div>
                </div>
            </div>

            <nav class="p-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-home mr-3"></i>Dashboard
                </a>

                <a href="{{ route('admin.approvals') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.approvals') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-user-check mr-3"></i>Approval Siswa
                </a>

                <a href="{{ route('admin.monitoring') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.monitoring') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-eye mr-3"></i>Monitoring
                </a>

                <a href="{{ route('admin.history') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.history') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-history mr-3"></i>History Absensi
                </a>

                <a href="{{ route('admin.students') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.students*') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-users mr-3"></i>Data Siswa
                </a>

                <a href="{{ route('admin.reports') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.reports') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-chart-bar mr-3"></i>Laporan
                </a>

                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                    <i class="fas fa-cog mr-3"></i>Pengaturan
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-8" id ="logoutForm">
                    @csrf
                    <button type="submit" onclick="confirmLogout()" 
                        class="w-full flex items-center px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    </button>
                    
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 py-4 px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">@yield('title')</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')


</body>

@push('scripts')
<script>

function confirmLogout() {
    if (confirm('Yakin ingin logout?')) {
        document.getElementById('logoutForm').submit();
    }
}
</script>
@endpush
</html>