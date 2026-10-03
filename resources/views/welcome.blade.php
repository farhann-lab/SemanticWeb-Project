@extends('layouts.app')

@section('content')

<section class="py-20">

    <div class="max-w-4xl mx-auto text-center">

        <p class="text-cyan-400 font-semibold tracking-widest mb-4">
            WORLD HERITAGE EXPLORER
        </p>

        <h1 class="text-5xl font-bold leading-tight">
            Explore the World's
            <span class="text-cyan-400">Heritage</span>
        </h1>

        <p class="mt-6 text-slate-400 text-lg max-w-2xl mx-auto">
            Explore World Heritage Sites from around the world
            using a semantic web knowledge graph powered by
            RDF, SPARQL, and Apache Jena Fuseki.
        </p>

        <div class="flex justify-center gap-4 mt-8">

            <a
                href="/heritage"
                class="px-6 py-3 rounded-xl bg-cyan-400 text-slate-950 font-semibold"
            >
                Explore Heritage
            </a>

            <a
                href="/search"
                class="px-6 py-3 rounded-xl bg-slate-800 border border-slate-700"
            >
                Search Heritage
            </a>

        </div>

    </div>

</section>


<section class="grid md:grid-cols-3 gap-5">

    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <p class="text-slate-400">
            Heritage Sites
        </p>

        <h2 class="text-4xl font-bold mt-2">
            {{ $stats['total'] }}
        </h2>
    </div>


    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <p class="text-slate-400">
            Countries
        </p>

        <h2 class="text-4xl font-bold mt-2">
            {{ $stats['countries'] }}
        </h2>
    </div>


    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <p class="text-slate-400">
            Regions
        </p>

        <h2 class="text-4xl font-bold mt-2">
            {{ $stats['regions'] }}
        </h2>
    </div>

</section>


<section class="mt-16">

    <h2 class="text-2xl font-bold mb-6">
        Explore Our Knowledge Graph
    </h2>

    <div class="grid md:grid-cols-3 gap-5">

        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-xl font-semibold">
                Heritage Sites
            </h3>

            <p class="text-slate-400 mt-2">
                Browse detailed information about World Heritage Sites.
            </p>
        </div>


        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-xl font-semibold">
                Search & Filter
            </h3>

            <p class="text-slate-400 mt-2">
                Find heritage sites based on name, country,
                region, category, and other criteria.
            </p>
        </div>


        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-xl font-semibold">
                Related Heritage
            </h3>

            <p class="text-slate-400 mt-2">
                Discover related and recommended heritage sites.
            </p>
        </div>

    </div>

</section>

@endsection