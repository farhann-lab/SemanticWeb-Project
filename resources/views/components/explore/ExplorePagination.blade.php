{{-- resources/views/components/explore/ExplorePagination.blade.php --}}
@props([
    'result' => [],
])

@php
    $currentPage = $result['current_page'] ?? 1;
    $totalPages  = $result['total_pages']  ?? 1;
    $total       = $result['total']        ?? 0;
    $from        = $result['from']         ?? 1;
    $to          = $result['to']           ?? 0;
    $perPage     = $result['per_page']     ?? 6;
    $density     = request('density', 'default');

    // Build visible page range (show max 5 pages around current)
    $range = [];
    if ($totalPages <= 7) {
        $range = range(1, $totalPages);
    } else {
        $range[] = 1;
        if ($currentPage > 3) $range[] = '...';
        for ($i = max(2, $currentPage - 1); $i <= min($totalPages - 1, $currentPage + 1); $i++) {
            $range[] = $i;
        }
        if ($currentPage < $totalPages - 2) $range[] = '...';
        $range[] = $totalPages;
    }
@endphp

<div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-200 pt-6">

    {{-- Left: Page info --}}
    <p class="text-xs text-gray-500 tabular-nums">
        Page <strong class="text-gray-950">{{ $currentPage }}</strong> of <strong class="text-gray-950">{{ $totalPages }}</strong>
        &nbsp;·&nbsp;
        Records <strong class="text-gray-950">{{ number_format($from) }}</strong> to <strong class="text-gray-950">{{ number_format($to) }}</strong>
        of <strong class="text-gray-950">{{ number_format($total) }}</strong>
    </p>

    {{-- Center: Page numbers --}}
    <nav class="flex items-center gap-1" aria-label="Pagination">
        {{-- Previous --}}
        @if($currentPage > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
               class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-950 transition-colors">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @else
            <span class="w-8 h-8 rounded-full border border-gray-100 flex items-center justify-center text-gray-300 cursor-not-allowed">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @endif

        {{-- Page numbers --}}
        @foreach($range as $p)
            @if($p === '...')
                <span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">…</span>
            @elseif($p == $currentPage)
                <span class="w-8 h-8 rounded-full bg-gray-950 text-white text-xs font-bold flex items-center justify-center tabular-nums">{{ $p }}</span>
            @else
                <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}"
                   class="w-8 h-8 rounded-full border border-gray-200 text-xs font-medium text-gray-600 flex items-center justify-center hover:bg-gray-100 hover:text-gray-950 transition-colors tabular-nums">
                    {{ $p }}
                </a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($currentPage < $totalPages)
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
               class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-950 transition-colors">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <span class="w-8 h-8 rounded-full border border-gray-100 flex items-center justify-center text-gray-300 cursor-not-allowed">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        @endif
    </nav>

    {{-- Right: Density switch --}}
    <div class="flex items-center gap-2">
        <span class="text-xs text-gray-400 font-medium">Density:</span>
        <div class="flex items-center rounded-full border border-gray-200 overflow-hidden">
            <a href="{{ request()->fullUrlWithQuery(['density' => 'default', 'page' => 1]) }}"
               class="px-3 py-1.5 text-[11px] font-bold transition-colors
                      {{ $density !== 'compact' ? 'bg-gray-950 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                Default
            </a>
            <a href="{{ request()->fullUrlWithQuery(['density' => 'compact', 'page' => 1]) }}"
               class="px-3 py-1.5 text-[11px] font-bold transition-colors
                      {{ $density === 'compact' ? 'bg-gray-950 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                Compact
            </a>
        </div>
    </div>

</div>
