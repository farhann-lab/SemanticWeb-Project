{{--
    resources/views/components/semantic-workflow.blade.php
    Diagram alur pemrosesan data Semantic Web — tampil horizontal di desktop,
    vertikal di mobile. Digunakan di section 03 halaman About.
--}}

@php
$steps = [
    [
        'index'    => '01',
        'title'    => 'Raw Source',
        'subtitle' => 'UNESCO XML / CSV',
        'desc'     => 'Data mentah warisan dunia diunduh dari UNESCO World Heritage Centre dalam format XML & CSV.',
        'tech'     => 'Sumber: whc.unesco.org',
        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>',
    ],
    [
        'index'    => '02',
        'title'    => 'RDF Mapping & Ontologi',
        'subtitle' => 'Turtle Syntax · RDFS/OWL',
        'desc'     => 'Setiap entitas dipetakan ke ontologi kustom <code class="text-gray-300 font-mono text-[10px]">wh:</code> yang mendefinisikan hubungan antar situs, kriteria, dan wilayah.',
        'tech'     => 'Turtle · RDFS · OWL 2',
        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>',
    ],
    [
        'index'    => '03',
        'title'    => 'Apache Jena Fuseki',
        'subtitle' => 'Triple Store · SPARQL Server',
        'desc'     => 'Dataset RDF disimpan dan diindeks di Fuseki sebagai triple store. Seluruh data dapat diakses via standar SPARQL 1.1.',
        'tech'     => 'Apache Jena Fuseki 4.x',
        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125S3.75 11.278 3.75 9m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125S3.75 13.903 3.75 11.625"/>',
    ],
    [
        'index'    => '04',
        'title'    => 'SPARQL Endpoint',
        'subtitle' => 'Query Engine · Laravel Service',
        'desc'     => 'Laravel memanggil endpoint Fuseki menggunakan query SPARQL terparameterisasi. Tidak ada query bebas dari pengguna yang langsung dieksekusi.',
        'tech'     => 'SPARQL 1.1 · Laravel HTTP Client',
        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z"/>',
    ],
    [
        'index'    => '05',
        'title'    => 'HeritageFinder UI',
        'subtitle' => 'Blade · Tailwind · Alpine.js',
        'desc'     => 'Hasil query dirender ke tampilan web responsif berbasis Blade. Pengguna menjelajah data semantik melalui antarmuka visual yang intuitif.',
        'tech'     => 'Laravel Blade · Tailwind CSS v4',
        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>',
    ],
];
@endphp

{{-- ── Wrapper ─────────────────────────────────────────────────────────────── --}}
<div class="w-full">

    {{-- ── Desktop: horizontal flex row ────────────────────────────────────── --}}
    <div class="hidden lg:flex items-stretch gap-0">
        @foreach($steps as $i => $step)

            {{-- Step card --}}
            <div class="reveal flex-1 flex flex-col border border-gray-700 bg-gray-900/60 rounded-xl p-5
                        transition-all duration-300 hover:bg-gray-800/70 hover:border-gray-500 group">

                {{-- Step number + icon --}}
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold tracking-widest text-gray-500 uppercase">
                        Step {{ $step['index'] }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5 text-gray-400 group-hover:text-gray-200 transition-colors">
                        {!! $step['icon'] !!}
                    </svg>
                </div>

                {{-- Title --}}
                <h4 class="text-sm font-bold text-white leading-tight mb-1">
                    {{ $step['title'] }}
                </h4>
                <p class="text-[11px] font-medium text-gray-400 mb-3">
                    {{ $step['subtitle'] }}
                </p>

                {{-- Description --}}
                <p class="text-[11px] text-gray-500 leading-relaxed flex-1">
                    {!! $step['desc'] !!}
                </p>

                {{-- Tech badge --}}
                <div class="mt-4 pt-3 border-t border-gray-800">
                    <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-widest
                                 text-gray-500 bg-gray-800 px-2 py-1 rounded-full">
                        <span class="w-1 h-1 rounded-full bg-gray-500"></span>
                        {{ $step['tech'] }}
                    </span>
                </div>
            </div>

            {{-- Arrow connector (between steps, not after last) --}}
            @if(!$loop->last)
                <div class="flex items-center justify-center px-2 flex-shrink-0">
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-px h-6 bg-gray-700"></div>
                        <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                        <div class="w-px h-6 bg-gray-700"></div>
                    </div>
                </div>
            @endif

        @endforeach
    </div>

    {{-- ── Mobile: vertical stack ────────────────────────────────────────────── --}}
    <div class="flex lg:hidden flex-col gap-3">
        @foreach($steps as $i => $step)
            <div class="reveal flex items-start gap-4 border border-gray-700 bg-gray-900/60 rounded-xl p-4">

                {{-- Step pill --}}
                <div class="flex-shrink-0 w-9 h-9 rounded-full border border-gray-600 bg-gray-800
                            flex items-center justify-center text-[10px] font-bold text-gray-400">
                    {{ $step['index'] }}
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="text-sm font-bold text-white">{{ $step['title'] }}</h4>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-2">{{ $step['subtitle'] }}</p>
                    <p class="text-[11px] text-gray-500 leading-relaxed">{!! $step['desc'] !!}</p>
                    <span class="mt-2 inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-widest
                                 text-gray-500 bg-gray-800 px-2 py-0.5 rounded-full">
                        {{ $step['tech'] }}
                    </span>
                </div>
            </div>

            {{-- Mobile connector --}}
            @if(!$loop->last)
                <div class="flex justify-center">
                    <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3"/>
                    </svg>
                </div>
            @endif
        @endforeach
    </div>

</div>
