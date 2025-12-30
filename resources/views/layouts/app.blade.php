<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sistem Absensi - SMK' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="{{ asset('img/logo.png') }}" class="w-9 h-9 mr-3 object-contain">
                    <h1 class="text-lg font-bold text-gray-800">NineFace</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right mr-4">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->class }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            <i class="fas fa-sign-out-alt text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- LIVEWIRE CONTENT -->
    <main class="p-6">
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
