@extends('layouts.app')

@section('title', 'Statistics — HeritageFinder')

@section('content')
<div class="max-w-[1100px] mx-auto px-4 md:px-8 py-8">

    <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Knowledge Graph</p>
    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-gray-950 mt-1">World Heritage Statistics</h1>

    <div class="grid md:grid-cols-3 gap-4 mt-8">
        @foreach(['Total Heritage' => $stats['total'], 'Countries' => $stats['countries'], 'Regions' => $stats['regions']] as $label => $value)
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                <p class="text-4xl font-black text-gray-950 mt-2 tabular-nums">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    @if(count($categories))
        @php $max = max(array_column($categories, 'total')) ?: 1; @endphp
        <section class="mt-10 bg-white border border-gray-200 rounded-2xl p-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400">Sites by category</h2>
            <ul class="mt-4 space-y-4">
                @foreach($categories as $row)
                    <li>
                        <a href="{{ url('/search?category=' . $row['category']) }}" class="block group">
                            <div class="flex justify-between text-sm font-bold text-gray-950">
                                <span class="group-hover:underline">{{ $row['category'] }}</span>
                                <span class="tabular-nums">{{ number_format($row['total']) }}</span>
                            </div>
                            <div class="mt-1.5 h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full bg-gray-950 rounded-full" style="width: {{ round($row['total'] / $max * 100) }}%"></div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
