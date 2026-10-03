@extends('layouts.app')

@section('content')

<a href="/heritage" class="text-cyan-400 hover:text-cyan-300">
    ← Kembali
</a>

<div class="mt-6 p-7 rounded-2xl bg-slate-900 border border-slate-800">

    <p class="text-cyan-400 text-xs">
        {{ $data['id'] }}
    </p>

    <h1 class="text-3xl font-bold mt-2">
        {{ $data['name'] }}
    </h1>

    @if(!empty($data['description']))

    <div class="mt-6">
        <p class="text-slate-400 text-sm mb-2">
            Description
        </p>

        <p class="text-slate-300 leading-7">
            {{ $data['description'] }}
        </p>
    </div>

    @endif

    {{-- IMAGES --}}
    @if(!empty($images))
        <div class="mt-8">
            <p class="text-slate-400 mb-3">
                Images
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($images as $image)
                    <a href="{{ $image['url'] }}" target="_blank">
                        <img
                            src="{{ $image['url'] }}"
                            alt="{{ $data['name'] }}"
                            class="w-full h-48 object-cover rounded-xl border border-slate-800 hover:opacity-80 transition"
                        >
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-4 mt-8">

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">Country</p>
            <p>{{ $data['country'] ?? '-' }}</p>
        </div>

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">Region</p>
            <p>{{ $data['region'] ?? '-' }}</p>
        </div>

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">Category</p>
            <p>{{ $data['category'] ?? '-' }}</p>
        </div>

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">Inscription Year</p>
            <p>{{ $data['inscriptionYear'] ?? '-' }}</p>
        </div>

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">Area (Hectares)</p>
            <p>{{ $data['areaHectares'] ?? '-' }}</p>
        </div>

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">In Danger</p>
            <p>{{ $data['isInDanger'] === 'true' ? 'Yes' : 'No' }}</p>
        </div>

        <div class="p-4 bg-slate-800 rounded-xl">
            <p class="text-xs text-slate-400">Transboundary</p>
            <p>{{ $data['isTransboundary'] === 'true' ? 'Yes' : 'No' }}</p>
        </div>

    </div>

    @if(count($components) > 0)

    <section class="mt-8">

        <h2 class="text-2xl font-bold mb-4">
            Heritage Components
        </h2>

        <div class="grid md:grid-cols-2 gap-4">

            @foreach($components as $component)

                <div class="p-5 rounded-xl bg-slate-900 border border-slate-800">

                    <p class="font-semibold">
                        {{ $component['name'] }}
                    </p>

                    <p class="text-xs text-slate-500 mt-2">
                        {{ $component['id'] }}
                    </p>

                </div>

            @endforeach

        </div>

    </section>

    @endif

    <div class="mt-8">

        <p class="text-slate-400">
            Criteria
        </p>

        <div class="flex flex-wrap gap-2 mt-3">

            @forelse($data['criteria'] ?? [] as $criterion)

                <span class="px-3 py-1 mr-2 mb-2 inline-block bg-cyan-500/10 text-cyan-300 rounded-full">
                    {{ $criterion }}
                </span>

            @empty

                <span class="text-slate-500">
                    No criteria
                </span>

            @endforelse

        </div>

    </div>
    @if(!empty($data['justification']))

    <div class="mt-8">

        <p class="text-slate-400 text-sm mb-2">
            Justification
        </p>

        <div class="text-slate-300 leading-7 whitespace-pre-line">
            {{ $data['justification'] }}
        </div>

    </div>

    @endif

        @if(!empty($related))

    <div class="mt-10">

        <p class="text-slate-400">
            Related Heritage
        </p>

        <div class="grid md:grid-cols-3 gap-4 mt-4">

            @foreach($related as $item)

                <a
                    href="/heritage/{{ $item['id'] }}"
                    class="p-4 bg-slate-800 rounded-xl hover:bg-slate-700 transition"
                >

                    <p class="font-semibold">
                        {{ $item['name'] }}
                    </p>

                    <p class="text-xs text-slate-500 mt-2">
                        {{ $item['id'] }}
                    </p>

                </a>

            @endforeach

        </div>


        @if(!empty($recommendations))

    <div class="mt-10">

        <p class="text-slate-400">
            Recommended Heritage
        </p>

        <div class="grid md:grid-cols-3 gap-4 mt-4">

            @foreach($recommendations as $item)

                <a
                    href="/heritage/{{ $item['id'] }}"
                    class="p-4 bg-slate-800 rounded-xl hover:bg-slate-700 transition"
                >

                    <p class="font-semibold">
                        {{ $item['name'] }}
                    </p>

                    <p class="text-xs text-slate-500 mt-2">
                        {{ $item['id'] }}
                    </p>

                </a>

            @endforeach

        </div>

    </div>

@endif



    </div>

 @endif


</div>

@endsection