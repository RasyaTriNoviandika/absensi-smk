<!-- Global Loading  -->
<div {{ $attributes->merge(['class' => 'fixed inset-0 bg-white flex items-center justify-center z-[9999]']) }} 
     style="display: none;" 
     x-data="{ show: false }"
     x-show="show"
     x-cloak>
    <div class="text-center">
        <div class="inline-block w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
        <p class="text-gray-700 font-medium text-sm">{{ $slot ?? 'Memuat...' }}</p>
    </div>
</div>