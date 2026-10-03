@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6">Search Heritage</h1>

<form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

    <input
        name="q"
        value="{{ request('q') }}"
        placeholder="Search heritage..."
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <input
        name="country"
        value="{{ request('country') }}"
        placeholder="Country code, contoh: ID"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <input
        name="region"
        value="{{ request('region') }}"
        placeholder="Region code, contoh: APA"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <input
        name="category"
        value="{{ request('category') }}"
        placeholder="Category"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <input
        name="criterion"
        value="{{ request('criterion') }}"
        placeholder="Criterion, contoh: (i)"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <input
        type="number"
        name="year_min"
        value="{{ request('year_min') }}"
        placeholder="Minimum inscription year"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <input
        type="number"
        name="year_max"
        value="{{ request('year_max') }}"
        placeholder="Maximum inscription year"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >

    <select
        name="danger"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >
        <option value="">Danger status</option>
        <option value="true" @selected(request('danger') === 'true')>
            In Danger
        </option>
        <option value="false" @selected(request('danger') === 'false')>
            Not In Danger
        </option>
    </select>

    <select
        name="transboundary"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >
        <option value="">Transboundary</option>
        <option value="true" @selected(request('transboundary') === 'true')>
            Yes
        </option>
        <option value="false" @selected(request('transboundary') === 'false')>
            No
        </option>
    </select>

    <select
        name="sort"
        class="p-3 rounded-xl bg-slate-900 border border-slate-700"
    >
        <option value="name_asc">Name A-Z</option>
        <option value="name_desc">Name Z-A</option>
        <option value="year_asc">Oldest inscription</option>
        <option value="year_desc">Newest inscription</option>
    </select>

    <button
        type="submit"
        class="md:col-span-2 p-3 rounded-xl bg-cyan-400 text-slate-950 font-semibold"
    >
        Search
    </button>

</form>

<div class="space-y-3">

    @forelse(($result['data'] ?? []) as $h)

        <a
            href="/heritage/{{ $h['id'] }}"
            class="block p-4 bg-slate-900 border border-slate-800 rounded-xl"
        >
            <b>{{ $h['name'] }}</b>

            <span class="float-right text-xs text-slate-500">
                {{ $h['year'] ?? '-' }}
            </span>
        </a>

    @empty

        <div class="p-5 bg-slate-900 rounded-xl">
            No heritage found.
        </div>

    @endforelse

</div>

@if(($result['total'] ?? 0) >= ($result['per_page'] ?? 20))
    <div class="flex justify-center gap-3 mt-8">

        @if(($result['current_page'] ?? 1) > 1)
            <a
                href="{{ request()->fullUrlWithQuery(['page' => $result['current_page'] - 1]) }}"
                class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700"
            >
                ← Previous
            </a>
        @endif

        <span class="px-4 py-2">
            Page {{ $result['current_page'] ?? 1 }}
        </span>

        <a
            href="{{ request()->fullUrlWithQuery(['page' => $result['current_page'] + 1]) }}"
            class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700"
        >
            Next →
        </a>

    </div>
@endif

@endsection