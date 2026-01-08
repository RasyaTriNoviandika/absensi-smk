<main class="flex-1 min-h-screen">
    @isset($slot)
        {{ $slot }}
    @else
        @yield('content')
    @endisset
</main>
