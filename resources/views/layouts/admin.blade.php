<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Sistem Absensi</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navbar Admin -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                        <div>
                            <h1 class="font-bold text-gray-800">Admin Panel</h1>
                            <p class="text-xs text-gray-500">NineFace</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden sm:block text-sm text-gray-600">
                        {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:sticky top-0 left-0 h-screen w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50 overflow-y-auto">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Menu Navigasi</h2>
                
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.approvals') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.approvals') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-user-check w-5"></i>
                        <span class="ml-3 font-medium">Approval Siswa</span>
                    </a>

                    <a href="{{ route('admin.monitoring') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.monitoring') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-eye w-5"></i>
                        <span class="ml-3 font-medium">Monitoring</span>
                    </a>

                    <a href="{{ route('admin.history') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.history') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-history w-5"></i>
                        <span class="ml-3 font-medium">History Absensi</span>
                    </a>

                    <a href="{{ route('admin.students') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.students*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-users w-5"></i>
                        <span class="ml-3 font-medium">Data Siswa</span>
                    </a>

                    <a href="{{ route('admin.reports') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span class="ml-3 font-medium">Laporan</span>
                    </a>

                    <a href="{{ route('admin.settings') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                        <i class="fas fa-cog w-5"></i>
                        <span class="ml-3 font-medium">Pengaturan</span>
                    </a>
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="mt-8" id="logoutForm">
                    @csrf
                    <button type="button" onclick="confirmLogout()" 
                        class="w-full flex items-center px-4 py-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3 font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
    function confirmLogout() {
        if (confirm('Yakin ingin logout?')) {
            document.getElementById('logoutForm').submit();
        }
    }

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
    }
    </script>
</body>
</html>