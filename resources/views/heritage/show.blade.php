@extends('layouts.app')

@section('title', $data['name'] . ' — HeritageFinder')

@section('content')
@php
    $mainImage = $images[0]['url'] ?? null;
    $gallery   = array_slice($images, 1);
    $facts = [
        'State Party'      => $data['country'] ?: '—',
        'Region'           => $data['region'] ?: '—',
        'Category'         => $data['category'] ?: '—',
        'Inscribed'        => $data['inscriptionYear'] ?? '—',
        'Area'             => $data['areaHectares'] !== null ? number_format($data['areaHectares'], 2) . ' ha' : '—',
        'Transboundary'    => $data['isTransboundary'] ? 'Yes' : 'No',
    ];

    $hasCoordinates = !empty($data['lat']) && !empty($data['lng']) &&
                      is_numeric($data['lat']) && is_numeric($data['lng']) &&
                      $data['lat'] >= -90 && $data['lat'] <= 90 &&
                      $data['lng'] >= -180 && $data['lng'] <= 180;

    $mapUrl = $hasCoordinates
        ? route('map', ['lat' => $data['lat'], 'lng' => $data['lng'], 'id' => $data['id']])
        : route('map');
@endphp

<div class="max-w-[1100px] mx-auto mt-5 px-4 md:px-8 py-8 mt-15">

    <a href="{{ url()->previous() !== url()->current() && str_contains(url()->previous(), '/search') ? url()->previous() : url('/search') }}"
       class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-gray-950 transition-colors">
        ← Back to Explore
    </a>

    {{-- Header --}}
    <header class="mt-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-gray-950 text-white">{{ $data['category'] ?: 'Heritage' }}</span>
                @if($data['isInDanger'])
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-red-100 text-red-700">In Danger</span>
                @endif
                <span class="text-[11px] font-mono text-gray-400">wh:{{ $data['id'] }}</span>
            </div>

            @if($hasCoordinates)
                <a href="{{ $mapUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 hover:border-gray-950 text-xs font-bold uppercase tracking-wider text-gray-950 hover:bg-gray-50 shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5 text-gray-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                        <line x1="8" y1="2" x2="8" y2="18"/>
                        <line x1="16" y1="6" x2="16" y2="22"/>
                    </svg>
                    <span>View on Map</span>
                </a>
            @endif
        </div>
        <h1 class="mt-3 text-3xl md:text-5xl font-extrabold tracking-tight text-gray-950 leading-tight">{{ $data['name'] }}</h1>
        <p class="mt-2 text-sm font-semibold text-gray-500">
            {{ $data['country'] }}@if($data['region']) · {{ $data['region'] }}@endif
        </p>
    </header>

    {{-- Hero image --}}
    @if($mainImage)
        <figure class="mt-8 rounded-[24px] overflow-hidden bg-gray-100 border border-gray-200 aspect-[16/9] max-h-[520px] relative">
            <img src="{{ $mainImage }}" alt="{{ $data['name'] }}" fetchpriority="high" decoding="async" referrerpolicy="no-referrer" class="w-full h-full object-cover">
        </figure>
    @endif

    {{-- Key facts --}}
    <dl class="mt-8 grid grid-cols-2 md:grid-cols-3 gap-3">
        @foreach($facts as $label => $value)
            <div class="bg-white border border-gray-200 rounded-2xl px-5 py-4">
                <dt class="text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ $label }}</dt>
                <dd class="mt-1 text-sm font-bold text-gray-950">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>

    {{-- Criteria --}}
    <section class="mt-8">
        <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">UNESCO Criteria</h2>
        <div class="flex flex-wrap gap-2 mt-3">
            @forelse($data['criteria'] as $criterion)
                <a href="{{ url('/about') }}"
                   class="text-xs font-bold tabular-nums text-gray-700 bg-gray-100 border border-gray-200 px-3 py-1 rounded-full hover:border-gray-950 transition-colors">{{ $criterion }}</a>
            @empty
                <span class="text-sm text-gray-400">No criteria recorded</span>
            @endforelse
        </div>
    </section>

    {{-- Description / justification --}}
    @if(!empty($data['description']))
        <section class="mt-10">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">Description</h2>
            <p class="mt-3 text-gray-700 leading-7">{{ $data['description'] }}</p>
        </section>
    @endif

    @if(!empty($data['justification']))
        <section class="mt-10">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">Justification for Inscription</h2>
            <div class="mt-3 text-gray-700 leading-7 whitespace-pre-line">{{ $data['justification'] }}</div>
        </section>
    @endif

    {{-- Gallery Carousel --}}
    @if(count($gallery))
        <section class="mt-12 pt-8 border-t border-gray-200"
                 style="content-visibility: auto; contain-intrinsic-size: 500px;"
                 x-data="{
                     active: 0,
                     total: {{ count($gallery) }},
                     images: {{ json_encode(array_values(array_map(fn($g) => $g['url'], $gallery))) }},
                     lightboxOpen: false,
                     loadedThumbs: [0, 1, 2, 3],
                     next() {
                         this.active = (this.active + 1) % this.total;
                         this.ensureThumbLoaded(this.active);
                     },
                     prev() {
                         this.active = (this.active - 1 + this.total) % this.total;
                         this.ensureThumbLoaded(this.active);
                     },
                     ensureThumbLoaded(index) {
                         if (!this.loadedThumbs.includes(index)) {
                             this.loadedThumbs.push(index);
                         }
                     }
                 }"
                 @keydown.arrow-right.window="if(lightboxOpen) next()"
                 @keydown.arrow-left.window="if(lightboxOpen) prev()"
                 @keydown.escape.window="lightboxOpen = false">

            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">ARCHIVAL GALLERY</span>
                    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-950 mt-1">
                        Gallery
                        <span class="text-xs font-semibold text-gray-500 ml-1.5 tabular-nums" x-text="'(' + (active + 1) + ' of ' + total + ')'"></span>
                    </h2>
                </div>

                {{-- Carousel Controls --}}
                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="prev()"
                            class="w-9 h-9 rounded-full border border-gray-200 bg-white hover:bg-gray-100 flex items-center justify-center text-gray-700 hover:text-gray-950 transition-colors shadow-sm focus:outline-none"
                            aria-label="Previous image">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button type="button"
                            @click="next()"
                            class="w-9 h-9 rounded-full border border-gray-200 bg-white hover:bg-gray-100 flex items-center justify-center text-gray-700 hover:text-gray-950 transition-colors shadow-sm focus:outline-none"
                            aria-label="Next image">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>

            {{-- Main Featured Slide --}}
            <div class="relative aspect-[16/9] md:aspect-[21/9] rounded-[24px] overflow-hidden bg-gray-100 border border-gray-200 shadow-sm group">
                <template x-for="(imgUrl, index) in images" :key="index">
                    <div x-show="active === index"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-98"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200 absolute inset-0"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-98"
                         class="w-full h-full">
                        {{-- Only load image when slide is active or adjacent --}}
                        <template x-if="Math.abs(active - index) <= 1 || (active === 0 && index === total - 1) || (active === total - 1 && index === 0)">
                            <img :src="imgUrl"
                                 alt="{{ $data['name'] }}"
                                 loading="lazy"
                                 decoding="async"
                                 referrerpolicy="no-referrer"
                                 class="w-full h-full object-cover">
                        </template>
                    </div>
                </template>

                {{-- Expand / Fullscreen Button --}}
                <button type="button"
                        @click="lightboxOpen = true"
                        class="absolute bottom-4 right-4 bg-gray-950/80 hover:bg-gray-950 text-white text-xs font-bold px-3.5 py-2 rounded-full backdrop-blur-md flex items-center gap-1.5 transition-all shadow-md">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                    <span>View Fullscreen</span>
                </button>
            </div>

            {{-- Thumbnail Strip with On-Demand Lazy Loading --}}
            <div class="flex items-center gap-2.5 mt-3 overflow-x-auto pb-2 scrollbar-none"
                 @scroll.passive="
                     const el = $el;
                     const scrollRight = el.scrollLeft + el.clientWidth;
                     const totalWidth = el.scrollWidth;
                     const approxVisibleCount = Math.min(total, Math.ceil((scrollRight / totalWidth) * total) + 2);
                     for (let i = 0; i < approxVisibleCount; i++) {
                         ensureThumbLoaded(i);
                     }
                 ">
                <template x-for="(imgUrl, index) in images" :key="'thumb-' + index">
                    <button type="button"
                            @click="active = index; ensureThumbLoaded(index)"
                            :class="active === index ? 'ring-2 ring-gray-950 border-transparent opacity-100' : 'border-gray-200 opacity-60 hover:opacity-100'"
                            class="relative flex-shrink-0 w-20 h-14 md:w-24 md:h-16 rounded-xl overflow-hidden border bg-gray-100 transition-all focus:outline-none">
                        <template x-if="loadedThumbs.includes(index)">
                            <img :src="imgUrl"
                                 loading="lazy"
                                 decoding="async"
                                 referrerpolicy="no-referrer"
                                 class="w-full h-full object-cover">
                        </template>
                        <template x-if="!loadedThumbs.includes(index)">
                            <div class="w-full h-full bg-gray-200 animate-pulse flex items-center justify-center">
                                <span class="text-[9px] font-mono text-gray-400" x-text="index + 1"></span>
                            </div>
                        </template>
                    </button>
                </template>
            </div>

            {{-- Lightbox Modal --}}
            <div x-show="lightboxOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 bg-black/90 backdrop-blur-sm flex flex-col justify-between p-4 md:p-8"
                 @click.self="lightboxOpen = false">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between text-white">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 tabular-nums"
                          x-text="(active + 1) + ' / ' + total"></span>
                    <button type="button"
                            @click="lightboxOpen = false"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                {{-- Modal Image Display --}}
                <div class="relative flex-1 flex items-center justify-center my-4 overflow-hidden">
                    <img :src="images[active]"
                         alt="{{ $data['name'] }}"
                         referrerpolicy="no-referrer"
                         class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl">

                    {{-- Next/Prev Overlay inside Modal --}}
                    <button type="button"
                            @click="prev()"
                            class="absolute left-2 md:left-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-colors">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button type="button"
                            @click="next()"
                            class="absolute right-2 md:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur-md transition-colors">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>

                {{-- Modal Footer Caption --}}
                <div class="text-center text-xs text-gray-400 font-medium">
                    Use arrow keys ← → or click buttons to navigate · Esc to close
                </div>
            </div>
        </section>
    @endif

    {{-- Location Section (DET-3, DESIGN.md Section 4.8) --}}
    <section class="mt-12 pt-8 border-t border-gray-200" id="location" aria-labelledby="location-heading">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">06 — LOCATION</span>
                <h2 id="location-heading" class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-950 mt-1">Location</h2>
            </div>

            @if($hasCoordinates)
                <a href="{{ $mapUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-950 text-white text-xs font-bold uppercase tracking-wider hover:bg-gray-800 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-950 focus:ring-offset-2">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                        <line x1="8" y1="2" x2="8" y2="18"/>
                        <line x1="16" y1="6" x2="16" y2="22"/>
                    </svg>
                    <span>View on Map</span>
                    <svg class="w-3 h-3 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="7" y1="17" x2="17" y2="7"/>
                        <polyline points="7 7 17 7 17 17"/>
                    </svg>
                </a>
            @endif
        </div>

        @if($hasCoordinates)
            <div x-data="heritageMiniMap({
                    lat: {{ json_encode($data['lat']) }},
                    lng: {{ json_encode($data['lng']) }},
                    name: {{ json_encode($data['name']) }},
                    category: {{ json_encode($data['category']) }},
                    inDanger: {{ json_encode($data['isInDanger']) }}
                 })"
                 class="bg-white border border-gray-200 rounded-[24px] p-4 sm:p-5 shadow-sm space-y-4">

                {{-- Map Container --}}
                <div class="relative w-full h-72 sm:h-80 md:h-96 rounded-[18px] overflow-hidden bg-gray-100 border border-gray-200"
                     aria-label="Location map for {{ $data['name'] }}"
                     role="region">
                    <div x-ref="miniMapContainer" class="w-full h-full z-10"></div>
                </div>

                {{-- Readable Coordinates Bar --}}
                <div class="flex items-center justify-between text-xs text-gray-500 flex-wrap gap-3 pt-1">
                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Coordinates:</span>
                            <span class="font-mono font-semibold tabular-nums text-gray-950">
                                {{ number_format($data['lat'], 5) }}°, {{ number_format($data['lng'], 5) }}°
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Latitude:</span>
                            <span class="font-mono tabular-nums text-gray-800">{{ $data['lat'] }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Longitude:</span>
                            <span class="font-mono tabular-nums text-gray-800">{{ $data['lng'] }}</span>
                        </div>
                    </div>
                    <span class="text-[11px] text-gray-400 italic">Geodetic reference: WGS 84</span>
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-[24px] p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mx-auto">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="1" y1="1" x2="23" y2="23"/>
                        <path d="M21 10c0 7-9 13-9 13s-1.87-1.45-3.83-4.17"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-950">Location Coordinates Unavailable</h3>
                <p class="text-xs text-gray-500 max-w-md mx-auto">
                    Location coordinates are not available for this heritage site in the semantic knowledge graph.
                </p>
                <div class="pt-2">
                    <a href="{{ route('map') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-300 hover:border-gray-950 text-xs font-bold uppercase tracking-wider text-gray-950 hover:bg-white transition-all">
                        <span>Browse World Map</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </section>

    {{-- Components (graph: wh:hasComponent) --}}
    @if(count($components))
        <section class="mt-10">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">Components ({{ count($components) }})</h2>
            <ul class="mt-3 grid md:grid-cols-2 gap-2">
                @foreach($components as $component)
                    <li class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-sm font-semibold text-gray-950">{{ $component['name'] }}</p>
                        <p class="text-[11px] font-mono text-gray-400 mt-0.5">{{ $component['id'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Connected knowledge --}}
    @foreach([
        ['Related Heritage — same country', $related],
        ['Recommended — same category & shared criteria', $recommendations],
    ] as [$title, $items])
        @if(!empty($items))
            <section class="mt-10">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ $title }}</h2>
                <div class="mt-3 grid md:grid-cols-3 gap-3">
                    @foreach($items as $item)
                        <a href="{{ url('/heritage/' . $item['id']) }}"
                           class="bg-white border border-gray-200 rounded-2xl px-5 py-4 hover:border-gray-950 hover:-translate-y-0.5 transition-all">
                            <p class="text-sm font-bold text-gray-950 leading-snug">{{ $item['name'] }}</p>
                            <p class="text-[11px] font-mono text-gray-400 mt-2">{{ $item['id'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

</div>
@endsection

