{{--
    resources/views/about.blade.php
    HeritageFinder — About, Data Sources, Semantic Architecture & Ethos
    Layout: layouts/app (includes x-Navbar + x-explore.Footer)
    Design: Monochrome Editorial Minimalism (DESIGN.md)
--}}

@extends('layouts.app')

@section('title', 'About — HeritageFinder Semantic Explorer')

@section('content')

{{-- ── Scroll-reveal observer & sticky anchor activation ──────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* 1. Intersection Observer — reveal sections on scroll */
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    /* 2. Sticky anchor-nav — highlight active section */
    const anchorLinks = document.querySelectorAll('[data-anchor]');
    const sections    = document.querySelectorAll('[data-section]');

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                anchorLinks.forEach(a => a.classList.remove('is-active'));
                const active = document.querySelector(`[data-anchor="${entry.target.dataset.section}"]`);
                if (active) active.classList.add('is-active');
            }
        });
    }, { rootMargin: '-30% 0px -60% 0px' });

    sections.forEach(s => sectionObserver.observe(s));
});
</script>

<style>
    /* ── Reveal animation ──────────────────────────────────────────────────── */
    .reveal {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s var(--ease-out, cubic-bezier(0.22,1,0.36,1)),
                    transform 0.6s var(--ease-out, cubic-bezier(0.22,1,0.36,1));
    }
    .reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    @media (prefers-reduced-motion: reduce) {
        .reveal { opacity: 1; transform: none; transition: none; }
    }

    /* ── Anchor nav active state ───────────────────────────────────────────── */
    [data-anchor].is-active {
        color: #0A0A0A;
        border-bottom-color: #0A0A0A;
    }
</style>

{{-- ════════════════════════════════════════════════════════════════════════
     TOP META BREADCRUMB
     ════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-white border-b border-gray-200 px-6 py-2.5 mt-20">
    <div class="max-w-6xl mx-auto">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 flex items-center gap-2">
            <a href="{{ url('/') }}" class="hover:text-gray-950 transition-colors">HeritageFinder</a>
            <span class="text-gray-300">/</span>
            <span>About Archive</span>
            <span class="text-gray-300">/</span>
            <span class="text-gray-600">System Architecture</span>
        </p>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     STICKY ANCHOR NAVIGATION
     ════════════════════════════════════════════════════════════════════════ --}}
<nav class="sticky top-[68px] z-40 bg-white/80 backdrop-blur-md border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center gap-0 overflow-x-auto">
            @foreach([
                ['id' => 'about',        'label' => '01 — About'],
                ['id' => 'data-sources', 'label' => '02 — Data Sources'],
                ['id' => 'architecture', 'label' => '03 — Semantic Architecture'],
                ['id' => 'ethos',        'label' => '04 — Ethos & Citation'],
            ] as $anchor)
                <a href="#{{ $anchor['id'] }}"
                   data-anchor="{{ $anchor['id'] }}"
                   class="flex-shrink-0 h-10 px-4 text-[11px] font-bold uppercase tracking-wider
                          text-gray-400 border-b-2 border-transparent
                          hover:text-gray-950 hover:border-gray-400
                          transition-all duration-200 flex items-center
                          whitespace-nowrap">
                    {{ $anchor['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>


{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 01 — ABOUT HERITAGEFINDER
     ════════════════════════════════════════════════════════════════════════ --}}
<section id="about" data-section="about" class="bg-white py-20 md:py-28 -mt-20">
    <div class="max-w-6xl mx-auto px-6 md:px-10">

        {{-- Section label + headline --}}
        <div class="reveal mb-12">
            <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-4">
                <span class="w-4 h-px bg-gray-300"></span>
                01 — About
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-950 tracking-tight leading-tight max-w-3xl">
                Connecting World Heritage<br class="hidden md:block"> Through Semantic Intelligence
            </h1>
        </div>

        {{-- Narrative paragraphs --}}
        <div class="reveal grid md:grid-cols-2 gap-10 mb-16">
            <div class="space-y-5">
                <p class="text-lg text-gray-700 leading-relaxed">
                    Informasi tentang situs warisan dunia UNESCO umumnya masih tersebar di berbagai
                    sumber dan hanya dapat ditelusuri melalui pencarian kata kunci pada daftar statis.
                    Hubungan antar situs — kesamaan kriteria, kategori, wilayah, atau keterkaitan
                    entitas sejarah dan budaya — sulit diketahui pengguna karena data tidak terhubung.
                </p>
                <p class="text-base text-gray-500 leading-relaxed">
                    <strong class="text-gray-800">HeritageFinder</strong> hadir memanfaatkan teknologi
                    Semantic Web dan Knowledge Graph agar eksplorasi antar situs warisan, kriteria UNESCO,
                    dan wilayah geografis dapat dilakukan secara intuitif — tanpa batasan pencarian
                    konvensional berbasis kata kunci.
                </p>
            </div>
            <div class="space-y-5">
                <p class="text-base text-gray-500 leading-relaxed">
                    Seluruh data bersumber dari UNESCO World Heritage Centre, dimodelkan sebagai
                    RDF menggunakan ontologi kustom, lalu disimpan di Apache Jena Fuseki.
                    Antarmuka web berkomunikasi eksklusif melalui query SPARQL terparameterisasi —
                    tidak ada data yang dikodekan langsung (<em>hardcoded</em>) ke tampilan.
                </p>
                <p class="text-base text-gray-500 leading-relaxed">
                    Proyek ini merupakan bagian dari tugas besar mata kuliah <strong class="text-gray-800">Web Semantik</strong>,
                    dirancang untuk membuktikan bahwa antarmuka berbasis <em>Linked Open Data</em>
                    dapat menghadirkan pengalaman eksplorasi warisan budaya yang bermakna.
                </p>
            </div>
        </div>

        {{-- 3-column pillars --}}
        <div class="reveal grid md:grid-cols-3 gap-5">

            {{-- Pillar 1 --}}
            <div class="border border-gray-200 rounded-2xl p-6 group hover:border-gray-400 hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-gray-950 text-white flex items-center justify-center mb-5
                            group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-950 mb-2">Semantic Discovery</h3>
                <p class="text-[13px] text-gray-500 leading-relaxed">
                    Penelusuran data terstruktur berbasis knowledge graph yang melampaui
                    batas pencarian kata kunci konvensional. Temukan situs berdasarkan
                    relasi semantik, bukan hanya teks.
                </p>
            </div>

            {{-- Pillar 2 --}}
            <div class="border border-gray-200 rounded-2xl p-6 group hover:border-gray-400 hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-gray-950 text-white flex items-center justify-center mb-5
                            group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-950 mb-2">Connected Knowledge</h3>
                <p class="text-[13px] text-gray-500 leading-relaxed">
                    Ungkap hubungan tersembunyi antar situs warisan, kriteria UNESCO,
                    konteks sejarah, dan entitas budaya yang belum pernah terlihat
                    dalam katalog konvensional.
                </p>
            </div>

            {{-- Pillar 3 --}}
            <div class="border border-gray-200 rounded-2xl p-6 group hover:border-gray-400 hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-gray-950 text-white flex items-center justify-center mb-5
                            group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-950 mb-2">Open &amp; Verified Data</h3>
                <p class="text-[13px] text-gray-500 leading-relaxed">
                    Seluruh informasi diambil secara <em>real-time</em> melalui query SPARQL
                    dari knowledge graph yang dapat diverifikasi. Tidak ada data statis
                    yang dikodekan langsung ke antarmuka.
                </p>
            </div>

        </div>
    </div>
</section>


{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 02 — DATA SOURCES & VERACITY
     ════════════════════════════════════════════════════════════════════════ --}}
<section id="data-sources" data-section="data-sources"
         class="bg-gray-50 py-20 md:py-28 border-t border-gray-200">
    <div class="max-w-6xl mx-auto px-6 md:px-10">

        {{-- Section header --}}
        <div class="reveal mb-12">
            <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-4">
                <span class="w-4 h-px bg-gray-300"></span>
                02 — Data Sources
            </span>
            <h2 class="text-2xl md:text-4xl font-extrabold text-gray-950 tracking-tight leading-tight">
                Pondasi Data Terbuka yang Terintegrasi
            </h2>
            <p class="mt-4 text-base text-gray-500 max-w-2xl leading-relaxed">
                HeritageFinder mengintegrasikan beberapa sumber data terbuka dan terverifikasi
                untuk menghadirkan informasi yang kaya dan saling terhubung.
            </p>
        </div>

        {{-- Data source cards --}}
        @php
        $sources = [
            [
                'badge'       => 'PRIMARY SOURCE',
                'badge_style' => 'bg-gray-950 text-white',
                'status'      => 'Loaded · Verified',
                'title'       => 'UNESCO World Heritage Centre',
                'desc'        => 'Dataset utama mencakup 1.199+ situs warisan dunia beserta kriteria UNESCO (i–x), status risiko In Danger, koordinat geografis, tahun inscripsi, dan deskripsi Outstanding Universal Value.',
                'meta'        => 'whc.unesco.org · CC BY-SA 4.0',
                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>',
            ],
            [
                'badge'       => 'LIVE LINK ACTIVE',
                'badge_style' => 'bg-gray-100 text-gray-800 border border-gray-300',
                'status'      => 'Planned · Integration Pending',
                'title'       => 'Wikidata SPARQL Endpoint',
                'desc'        => 'Pengayaan entitas sejarah, tokoh budaya, serta hubungan antar-entitas eksternal secara dinamis melalui query SPARQL ke endpoint publik Wikidata (query.wikidata.org).',
                'meta'        => 'query.wikidata.org · CC0',
                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z"/>',
            ],
            [
                'badge'       => 'CUSTOM ONTOLOGY',
                'badge_style' => 'bg-gray-200 text-gray-700',
                'status'      => 'Implemented · RDFS/OWL 2',
                'title'       => 'Ontologi Semantik Kustom',
                'desc'        => 'Pemodelan RDF khusus berbasis standar RDFS/OWL 2 untuk mendefinisikan relasi antara WorldHeritageSite, Country, Region, Category, Criterion, dan HeritageComponent dalam satu namespace koheren.',
                'meta'        => 'wh: · Turtle Syntax · Apache Jena',
                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>',
            ],
        ];
        @endphp

        <div class="reveal grid md:grid-cols-3 gap-5">
            @foreach($sources as $source)
                <div class="bg-white border border-gray-200 rounded-2xl p-6
                            hover:shadow-md hover:border-gray-400 transition-all duration-300
                            flex flex-col relative overflow-hidden">

                    {{-- Badge top-right --}}
                    <div class="absolute top-4 right-4">
                        <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full
                                     {{ $source['badge_style'] }}">
                            {{ $source['badge'] }}
                        </span>
                    </div>

                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-5 flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.75">
                            {!! $source['icon'] !!}
                        </svg>
                    </div>

                    {{-- Content --}}
                    <h3 class="text-sm font-bold text-gray-950 mb-2 pr-20">{{ $source['title'] }}</h3>
                    <p class="text-[13px] text-gray-500 leading-relaxed flex-1">{{ $source['desc'] }}</p>

                    {{-- Footer meta --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-mono text-gray-400">{{ $source['meta'] }}</span>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full
                                {{ str_contains($source['status'], 'Verified') ? 'bg-green-500' :
                                   (str_contains($source['status'], 'Implemented') ? 'bg-blue-500' : 'bg-yellow-400') }}">
                            </span>
                            {{ $source['status'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 03 — SEMANTIC WEB ARCHITECTURE (DARK)
     ════════════════════════════════════════════════════════════════════════ --}}
<section id="architecture" data-section="architecture"
         class="relative bg-gradient-to-b from-[#525252] to-[#0A0A0A] text-white py-20 md:py-28">
    <div class="max-w-6xl mx-auto px-6 md:px-10">

        {{-- Header --}}
        <div class="reveal mb-14">
            <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-4">
                <span class="w-4 h-px bg-gray-600"></span>
                03 — Semantic Architecture
            </span>
            <h2 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Alur Pemrosesan Data Berbasis Linked Data
            </h2>
            <p class="mt-4 text-base text-gray-400 max-w-2xl leading-relaxed">
                Dari sumber mentah UNESCO hingga antarmuka interaktif — setiap langkah
                mengikuti standar Semantic Web yang terbuka dan dapat diverifikasi.
            </p>
        </div>

        {{-- Workflow component --}}
        <div class="reveal">
            <x-semantic-workflow />
        </div>

        {{-- Architectural principle cards --}}
        <div class="reveal mt-14 grid md:grid-cols-2 gap-5">

            <div class="border border-gray-700 bg-gray-900/50 rounded-2xl p-6">
                <h4 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                    Keamanan & Isolasi
                </h4>
                <p class="text-[13px] text-gray-400 leading-relaxed">
                    Browser tidak pernah mengakses Apache Jena Fuseki secara langsung.
                    Semua query SPARQL dieksekusi oleh Laravel di sisi server menggunakan
                    template terparameterisasi. Input pengguna divalidasi ketat sebelum
                    diteruskan ke query engine.
                </p>
            </div>

            <div class="border border-gray-700 bg-gray-900/50 rounded-2xl p-6">
                <h4 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/>
                    </svg>
                    Arsitektur Berlapis
                </h4>
                <p class="text-[13px] text-gray-400 leading-relaxed">
                    Implementasi mengikuti pola berlapis: <span class="text-gray-200 font-mono text-[11px]">Controller → Service → SparqlService → Fuseki</span>.
                    Tidak ada logika SPARQL kompleks yang ditulis di dalam controller,
                    sesuai prinsip <em>single responsibility</em> yang diwajibkan ARCHITECTURE.md.
                </p>
            </div>

        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 04 — ETHOS & CITATION
     ════════════════════════════════════════════════════════════════════════ --}}
<section id="ethos" data-section="ethos"
         class="bg-white py-20 md:py-28 border-t border-gray-200">
    <div class="max-w-6xl mx-auto px-6 md:px-10">

        {{-- Header --}}
        <div class="reveal mb-12">
            <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-4">
                <span class="w-4 h-px bg-gray-300"></span>
                04 — Ethos &amp; Citation
            </span>
            <h2 class="text-2xl md:text-4xl font-extrabold text-gray-950 tracking-tight leading-tight">
                Etika Data &amp; Transparansi
            </h2>
        </div>

        {{-- Main ethics panel --}}
        <div class="reveal grid md:grid-cols-5 gap-6 mb-8">

            {{-- Large text block --}}
            <div class="md:col-span-3 bg-gray-50 border border-gray-200 rounded-2xl p-8">
                <h3 class="text-sm font-bold text-gray-950 uppercase tracking-widest mb-4">
                    Prinsip Penggunaan Data
                </h3>
                <div class="space-y-4 text-[13px] text-gray-600 leading-relaxed">
                    <p>
                        Seluruh data heritage yang ditampilkan bersumber dari <strong class="text-gray-800">UNESCO World Heritage Centre</strong>
                        dan disediakan di bawah lisensi terbuka untuk keperluan pendidikan dan penelitian
                        non-komersial. HeritageFinder beroperasi sebagai platform baca-saja (<em>read-only</em>)
                        tanpa memodifikasi dataset sumber.
                    </p>
                    <p>
                        Integrasi dengan <strong class="text-gray-800">Wikidata</strong> mengikuti ketentuan penggunaan
                        Wikimedia Foundation. Data yang diambil dari SPARQL endpoint Wikidata
                        berlisensi <strong class="text-gray-800">Creative Commons CC0</strong> (domain publik) sesuai
                        kebijakan resmi Wikidata.
                    </p>
                    <p>
                        Jika Anda mengutip informasi yang ditampilkan di HeritageFinder dalam
                        karya akademis atau penelitian, harap cantumkan juga sumber primer
                        yang tertera pada setiap halaman detail situs.
                    </p>
                </div>
            </div>

            {{-- Side metadata --}}
            <div class="md:col-span-2 space-y-4">

                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">
                        Machine Readability
                    </h4>
                    <p class="text-[13px] text-gray-600 leading-relaxed mb-3">
                        Halaman publik HeritageFinder menyematkan markup
                        <strong class="text-gray-800">RDFa</strong> dan
                        <strong class="text-gray-800">Open Graph Protocol</strong>
                        untuk mendukung pemrosesan mesin dan pratinjau berbagi halaman.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['RDFa', 'Open Graph', 'schema.org', 'SPARQL 1.1'] as $tag)
                            <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-1
                                         bg-gray-200 text-gray-700 rounded-full">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">
                        Konteks Proyek
                    </h4>
                    <div class="space-y-2 text-[13px] text-gray-600">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Mata Kuliah</span>
                            <span class="font-medium text-gray-800">Web Semantik</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Framework</span>
                            <span class="font-medium text-gray-800">Laravel 12 · PHP 8.2</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Triple Store</span>
                            <span class="font-medium text-gray-800">Apache Jena Fuseki</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Dataset</span>
                            <span class="font-medium text-gray-800">1.199+ Sites · RDF/TTL</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Citation box --}}
        <div class="reveal bg-gray-950 text-white rounded-2xl p-8">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                <div class="flex-1">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-3">
                        Cara Mengutip
                    </h3>
                    <p class="text-[13px] text-gray-300 leading-relaxed mb-3">
                        Jika Anda menggunakan data atau antarmuka HeritageFinder sebagai referensi:
                    </p>
                    <div class="bg-gray-900 border border-gray-800 rounded-xl px-5 py-3.5 font-mono text-[11px] text-gray-300 leading-relaxed">
                        HeritageFinder ({{ date('Y') }}). <em>Semantic Explorer for UNESCO World Heritage Sites.</em>
                        [Tugas Besar Web Semantik]. Sumber data primer: UNESCO World Heritage Centre.
                        &lt;{{ url('/about') }}&gt;
                    </div>
                </div>
                <div class="md:w-48 flex-shrink-0">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-3">
                        Tautan Resmi
                    </h3>
                    <nav class="space-y-2">
                        <a href="https://whc.unesco.org" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2 text-[12px] text-gray-300 hover:text-white transition-colors">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                            </svg>
                            UNESCO WHC
                        </a>
                        <a href="https://query.wikidata.org" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2 text-[12px] text-gray-300 hover:text-white transition-colors">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                            </svg>
                            Wikidata SPARQL
                        </a>
                        <a href="https://jena.apache.org/documentation/fuseki2" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2 text-[12px] text-gray-300 hover:text-white transition-colors">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                            </svg>
                            Apache Jena Fuseki
                        </a>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════════════════════════════════
     BACK TO TOP STRIP
     ════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-gray-50 border-t border-gray-200 py-6">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between">
        <p class="text-[11px] text-gray-400 uppercase tracking-widest">HeritageFinder — About Archive</p>
        <a href="#"
           class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-gray-500
                  hover:text-gray-950 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/>
            </svg>
            Back to Top
        </a>
    </div>
</div>

@endsection
