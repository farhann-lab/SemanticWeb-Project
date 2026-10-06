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
@endphp

<div class="max-w-[1100px] mx-auto px-4 md:px-8 py-8">

    <a href="{{ url()->previous() !== url()->current() && str_contains(url()->previous(), '/search') ? url()->previous() : url('/search') }}"
       class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-gray-950 transition-colors">
        ← Back to Explore
    </a>

    {{-- Header --}}
    <header class="mt-6">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-gray-950 text-white">{{ $data['category'] ?: 'Heritage' }}</span>
            @if($data['isInDanger'])
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-red-100 text-red-700">In Danger</span>
            @endif
            <span class="text-[11px] font-mono text-gray-400">wh:{{ $data['id'] }}</span>
        </div>
        <h1 class="mt-3 text-3xl md:text-5xl font-extrabold tracking-tight text-gray-950 leading-tight">{{ $data['name'] }}</h1>
        <p class="mt-2 text-sm font-semibold text-gray-500">
            {{ $data['country'] }}@if($data['region']) · {{ $data['region'] }}@endif
        </p>
    </header>

    {{-- Hero image --}}
    @if($mainImage)
        <figure class="mt-8 rounded-[24px] overflow-hidden bg-gray-100 border border-gray-200">
            <img src="{{ $mainImage }}" alt="{{ $data['name'] }}" referrerpolicy="no-referrer" class="w-full max-h-[520px] object-cover">
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
                <a href="{{ url('/search?criterion=' . trim($criterion, '()')) }}"
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

    {{-- Gallery --}}
    @if(count($gallery))
        <section class="mt-10">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">Gallery</h2>
            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($gallery as $image)
                    <a href="{{ $image['url'] }}" target="_blank" rel="noopener" class="block aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                        <img src="{{ $image['url'] }}" alt="{{ $data['name'] }} — photo {{ $loop->iteration }}" loading="lazy" referrerpolicy="no-referrer"
                             onerror="this.parentElement.remove()"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    </a>
                @endforeach
            </div>
        </section>
    @endif

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
