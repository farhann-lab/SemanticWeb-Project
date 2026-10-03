@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6">World Heritage Statistics</h1>

<div class="grid md:grid-cols-3 gap-5">

    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <p class="text-slate-400">Total Heritage</p>
        <h2 class="text-3xl font-bold mt-2">
            {{ $stats['total'] }}
        </h2>
    </div>

    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <p class="text-slate-400">Countries</p>
        <h2 class="text-3xl font-bold mt-2">
            {{ $stats['countries'] }}
        </h2>
    </div>

    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <p class="text-slate-400">Regions</p>
        <h2 class="text-3xl font-bold mt-2">
            {{ $stats['regions'] }}
        </h2>
    </div>

</div>

@endsection