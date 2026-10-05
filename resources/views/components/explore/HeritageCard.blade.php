{{-- resources/views/components/explore/HeritageCard.blade.php --}}
@props([
    'heritage' => [],
])

@php
    $category     = $heritage['category']     ?? '';
    $isInDanger   = $heritage['is_in_danger'] ?? false;
    $country      = $heritage['country']      ?? '';
    $region       = $heritage['region']       ?? '';
    $year         = $heritage['year']         ?? null;
    $criteria     = $heritage['criteria']     ?? [];
    $connections  = $heritage['connections']  ?? 0;
    $name         = $heritage['name']         ?? 'Unknown Heritage';
    $id           = $heritage['id']           ?? '';

    $categoryUpper = strtoupper($category);

    // Category badge styling
    $badgeClass = match(true) {
        str_contains(strtolower($category), 'cultural') => 'bg-gray-950 text-white',
        str_contains(strtolower($category), 'natural')  => 'bg-white text-gray-950 border border-gray-300',
        str_contains(strtolower($category), 'mixed')    => 'bg-gray-200 text-gray-950',
        default => 'bg-gray-100 text-gray-600',
    };

    // Country & region display
    $countryDisplay = str_replace(['country_', '_'], ['', ' '], $country);
    $regionDisplay  = str_replace(['region_', '_'], ['', ' '], $region);
    $meta = collect([$countryDisplay, $regionDisplay])->filter()->join(' · ');
@endphp

<article class="group bg-white border border-gray-200 rounded-[24px] overflow-hidden transition-all duration-350 ease-out hover:-translate-y-1.5 hover:shadow-md flex flex-col">

    {{-- Image Area --}}
    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden rounded-t-[23px]">
        {{-- Placeholder image (replace with real source when available) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center group-hover:scale-105 transition-transform duration-350 ease-out">
            <svg class="w-12 h-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>

        {{-- Overlay gradient --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>

        {{-- Badge: In Danger (top-left) --}}
        @if($isInDanger)
            <div class="absolute top-3 left-3 flex items-center gap-1 bg-gray-950 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                IN DANGER
            </div>
        @endif

        {{-- Badge: Category (top-right) --}}
        @if($category)
            <div class="absolute top-3 right-3 {{ $badgeClass }} text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                {{ $categoryUpper }}
            </div>
        @endif
    </div>

    {{-- Card Body --}}
    <div class="flex flex-col flex-1 p-4 gap-2">

        {{-- Meta: Country · Region --}}
        @if($meta)
            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 leading-none flex items-center gap-1">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ strtoupper($meta) }}
            </p>
        @endif

        {{-- Heritage Name --}}
        <h3 class="text-[15px] font-bold text-gray-950 leading-tight line-clamp-2 flex-1">
            <a href="{{ url('/heritage/' . $id) }}" class="hover:underline underline-offset-2">
                {{ $name }}
            </a>
        </h3>

        {{-- Inscribed year + Connections --}}
        <div class="flex items-center gap-2 flex-wrap mt-0.5">
            @if($year)
                <span class="text-[11px] text-gray-500 font-medium">
                    Inscribed {{ $year }}
                </span>
            @endif
            @if($connections > 0)
                <span class="flex items-center gap-1 text-[11px] font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    {{ $connections }} connections
                </span>
            @endif
        </div>

        {{-- UNESCO Criteria chips --}}
        @if(!empty($criteria))
            <div class="flex flex-wrap gap-1 mt-1">
                @foreach(array_slice($criteria, 0, 5) as $c)
                    <span class="text-[10px] font-bold text-gray-600 bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded-md tabular-nums">
                        {{ $c }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Footer: View detail button --}}
        <div class="flex items-center justify-end mt-2">
            <a href="{{ url('/heritage/' . $id) }}"
               class="w-9 h-9 rounded-full bg-gray-950 text-white flex items-center justify-center hover:bg-gray-800 transition-colors shadow-sm group/btn"
               aria-label="View {{ $name }}">
                <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="7" y1="17" x2="17" y2="7"/>
                    <polyline points="7 7 17 7 17 17"/>
                </svg>
            </a>
        </div>
    </div>
</article>
