<div>
    @if($count > 0)
    <span
        class="ml-auto bg-red-500 text-white text-[11px] font-semibold
        px-1.5 py-0.5 rounded-full
        {{ request()->routeIs('admin.approvals') ? 'opacity-70' : 'animate-pulse' }}"
    >
        {{ $count }}
    </span>
@endif

</div>