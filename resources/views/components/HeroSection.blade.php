{{-- resources/views/components/HeroSection.blade.php --}}
@props([
    'stats' => [
        'total'     => 0,
        'countries' => 0,
        'regions'   => 0
    ]
])

{{-- ── Reveal + Count-Up styles (scoped to this component) ──────────────── --}}
<style>
    /* Reveal: elemen mulai dari bawah + transparan */
    .hero-reveal {
        opacity: 0;
        transform: translateY(40px);
        transition: opacity 0.9s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.9s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .hero-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Delay helpers untuk stagger antar elemen */
    .hero-reveal-d1 { transition-delay: 0.10s; }
    .hero-reveal-d2 { transition-delay: 0.22s; }
    .hero-reveal-d3 { transition-delay: 0.34s; }
    .hero-reveal-d4 { transition-delay: 0.46s; }
    .hero-reveal-d5 { transition-delay: 0.58s; }

    @media (prefers-reduced-motion: reduce) {
        .hero-reveal,
        .hero-reveal-d1,
        .hero-reveal-d2,
        .hero-reveal-d3,
        .hero-reveal-d4,
        .hero-reveal-d5 {
            opacity: 1;
            transform: none;
            transition: none;
        }
    }
</style>

<div class="relative w-full bg-[#0A0A0A] text-white -mt-12">
    {{-- Container Parallax Hero --}}
    <section class="relative h-screen w-full overflow-hidden bg-gradient-to-b from-[#A3A3A3] to-[#0A0A0A]">
        {{-- Layers Parallax --}}
        <div data-parallax-layers class="relative h-full w-full">
            
            {{-- Stage 1: Background Terjauh --}}
            <div data-parallax-layer="1" class="absolute inset-0 w-full h-full">
                <img 
                    src="{{ asset('img/1.png') }}" 
                    alt="World Heritage Background" 
                    class="w-full h-full object-cover rounded-[32px] md:rounded-none"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-black/0 via-black/30 to-[#0A0A0A]"></div>
            </div>

            {{-- Layer 2: Midground Hutan --}}
            <div data-parallax-layer="2" class="absolute inset-0 w-full h-full">
                <img 
                    src="{{ asset('img/2.png') }}"  
                    alt="Heritage Forest Layer" 
                    class="w-full h-full object-cover opacity-80"
                />
            </div>

            {{-- Stage 3: Text Overlay — dengan reveal + stagger --}}
            <div 
                data-parallax-layer="3" 
                class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none px-4 text-center z-10"
            >
                <div class="max-w-5xl mx-auto space-y-4">
                    {{-- Label kecil --}}
                    <p class="hero-reveal hero-reveal-d1 text-xs md:text-sm font-bold uppercase tracking-[0.2em] text-gray-300">
                        Semantic Explorer for World Heritage Sites
                    </p>
                    {{-- Headline utama --}}
                    <h1 class="hero-reveal hero-reveal-d2 text-[clamp(3rem,7vw,6rem)] font-extrabold leading-none tracking-tight text-white drop-shadow-2xl uppercase">
                        HeritageFinder
                    </h1>
                    {{-- Subheadline --}}
                    <p class="hero-reveal hero-reveal-d3 text-sm md:text-lg text-gray-200 max-w-2xl mx-auto font-medium drop-shadow">
                        Uncover interconnected historical sites across the globe powered by RDF Knowledge Graph &amp; SPARQL.
                    </p>
                </div>

                {{-- Scroll Indicator --}}
                <div class="hero-reveal hero-reveal-d4 absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-80">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-gray-300">Scroll to Explore</span>
                    <div class="w-5 h-8 border-2 border-white/60 rounded-full flex justify-center p-1">
                        <div class="w-1 h-2 bg-white rounded-full animate-bounce"></div>
                    </div>
                </div>
            </div>

            {{-- Layer 4: Foreground Terdekat --}}
            <div data-parallax-layer="4" class="absolute inset-0 w-full h-full pointer-events-none z-20">
                <img 
                    src="{{ asset('img/last layer.png') }}" 
                    alt="Foreground Layer" 
                    class="w-full h-full object-cover opacity-100"
                />
            </div>
        </div>

        {{-- Fade Gradient Transition --}}
        <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-[#0A0A0A] via-[#0A0A0A]/80 to-transparent z-30 pointer-events-none"></div>
    </section>

    {{-- Stage 2 & 3: Transisi Tertimpa & Section Statistik --}}
    <section class="relative z-30 bg-[#0A0A0A] rounded-t-[40px] -mt-10 pt-16 pb-24 px-6 md:px-12 border-t border-gray-800/60 shadow-2xl">
        <div class="max-w-6xl mx-auto space-y-16">
            
            {{-- Header Transisi --}}
            <div class="hero-reveal text-center max-w-3xl mx-auto space-y-4">
                <span class="text-xs font-bold uppercase tracking-[0.15em] text-gray-400">
                    Connected Knowledge Base
                </span>
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold text-white tracking-tight leading-snug">
                    Preserving World History Through Linked Open Data
                </h2>
            </div>

            {{-- Stage 3: Section Statistik — Count-Up --}}
            <div id="hero-stats-grid" class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Stat Card 1 --}}
                <div class="hero-reveal hero-reveal-d1 p-8 rounded-[24px] bg-[#171717] border border-gray-800
                            flex flex-col justify-between hover:border-gray-700 transition-colors">
                    <span class="text-[0.75rem] font-bold tracking-widest text-gray-400 uppercase">
                        World Heritage Properties
                    </span>
                    <div class="mt-4">
                        <span class="text-[clamp(2.25rem,4.5vw,3.5rem)] font-extrabold text-white leading-none tabular-nums tracking-tight"
                              data-count-to="{{ $stats['total'] ?? 1154 }}"
                              data-count-separator=",">0</span>
                        <p class="text-xs text-gray-400 mt-2">Cultural, Natural, and Mixed sites indexed in Fuseki.</p>
                    </div>
                </div>

                {{-- Stat Card 2 --}}
                <div class="hero-reveal hero-reveal-d2 p-8 rounded-[24px] bg-[#171717] border border-gray-800
                            flex flex-col justify-between hover:border-gray-700 transition-colors">
                    <span class="text-[0.75rem] font-bold tracking-widest text-gray-400 uppercase">
                        Participating Countries
                    </span>
                    <div class="mt-4">
                        <span class="text-[clamp(2.25rem,4.5vw,3.5rem)] font-extrabold text-white leading-none tabular-nums tracking-tight"
                              data-count-to="{{ $stats['countries'] ?? 168 }}"
                              data-count-separator=",">0</span>
                        <p class="text-xs text-gray-400 mt-2">State parties with inscribed UNESCO heritage.</p>
                    </div>
                </div>

                {{-- Stat Card 3 --}}
                <div class="hero-reveal hero-reveal-d3 p-8 rounded-[24px] bg-[#171717] border border-gray-800
                            flex flex-col justify-between hover:border-gray-700 transition-colors">
                    <span class="text-[0.75rem] font-bold tracking-widest text-gray-400 uppercase">
                        Geographic Regions
                    </span>
                    <div class="mt-4">
                        <span class="text-[clamp(2.25rem,4.5vw,3.5rem)] font-extrabold text-white leading-none tabular-nums tracking-tight"
                              data-count-to="{{ $stats['regions'] ?? 5 }}"
                              data-count-separator=",">0</span>
                        <p class="text-xs text-gray-400 mt-2">Global regional zones mapped across continents.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="relative z-30 bg-[#0A0A0A] py-20 border-t border-gray-900 -mt-10 ">
        <div class="max-w-6xl mx-auto px-6 md:px-12 ">
            <div class="mb-12 -mt-10">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Semantic Web Architecture</span>
                <h2 class="text-2xl md:text-4xl font-bold text-white tracking-tight mt-1">
                    Explore Our Knowledge Graph
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-13">
                <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800/80 hover:border-gray-700 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="21" x2="21" y2="21"/>
                            <line x1="6" y1="21" x2="6" y2="10"/>
                            <line x1="10" y1="21" x2="10" y2="10"/>
                            <line x1="14" y1="21" x2="14" y2="10"/>
                            <line x1="18" y1="21" x2="18" y2="10"/>
                            <polygon points="12 3 2 10 22 10 12 3"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">
                        Heritage Sites
                    </h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Browse detailed information about World Heritage Sites queried directly from RDF dataset.
                    </p>
                </div>

                <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800/80 hover:border-gray-700 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">
                        Search & Filter
                    </h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Find heritage sites based on name, country, region, category, and UNESCO criteria.
                    </p>
                </div>

                <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800/80 hover:border-gray-700 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">
                        Related Heritage
                    </h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Discover connected semantic nodes and recommended heritage properties.
                    </p>
                </div>
            </div>
            {{-- Stage 4: Tombol Aksi --}}
            <div class="hero-reveal flex flex-col sm:flex-row items-center justify-center gap-4 pt-6">
                <a href="{{ url('/search') }}" 
                   class="w-full sm:w-auto h-[52px] px-8 rounded-full bg-white text-[#0A0A0A] font-bold text-sm tracking-wide
                          flex items-center justify-center hover:bg-gray-200 transition-all hover:scale-105 active:scale-95 shadow-lg">
                    Explore Heritage
                </a>
                <a href="{{ url('/map') }}" 
                   class="w-full sm:w-auto h-[52px] px-8 rounded-full border-[1.5px] border-white/80 bg-transparent text-white font-bold text-sm tracking-wide
                          flex items-center justify-center hover:bg-white/10 transition-all hover:scale-105 active:scale-95">
                    Interactive Map
                </a>
            </div>
        </div>
    </section>
</div>

{{-- ── Hero Reveal + Count-Up Script ─────────────────────────────────────── --}}
<script>
(function () {
    /* ── 1. Reveal Observer ──────────────────────────────────────────────── */
    const revealIO = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealIO.unobserve(entry.target);
            }
        });
    }, { threshold: 0.06 });

    // Trigger hero text immediately (already in viewport on load)
    document.querySelectorAll('.hero-reveal').forEach(el => revealIO.observe(el));

    /* ── 2. Count-Up ─────────────────────────────────────────────────────── */
    /**
     * Animates a number from 0 → target using easeOutExpo.
     * @param {HTMLElement} el
     * @param {number}      target
     * @param {string}      sep    — thousands separator character
     * @param {number}      dur    — animation duration in ms
     */
    function countUp(el, target, sep, dur) {
        const start     = performance.now();
        const isSmall   = target <= 10; // e.g. Regions = 5 → faster feel

        function easeOutExpo(t) {
            return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
        }

        function format(n) {
            if (!sep) return Math.round(n).toString();
            return Math.round(n).toLocaleString();
        }

        function frame(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / dur, 1);
            const eased    = easeOutExpo(progress);
            el.textContent = format(eased * target);
            if (progress < 1) requestAnimationFrame(frame);
        }

        requestAnimationFrame(frame);
    }

    /* Observe the stats grid; start counting when it enters the viewport */
    const statsGrid = document.getElementById('hero-stats-grid');
    if (statsGrid) {
        const countIO = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                countIO.unobserve(entry.target);

                entry.target.querySelectorAll('[data-count-to]').forEach((el, i) => {
                    const target = parseInt(el.dataset.countTo, 10);
                    const sep    = el.dataset.countSeparator || '';
                    const dur    = target <= 10 ? 1200 : 2000; // small numbers feel faster

                    // Stagger start per card (matches the reveal delay)
                    setTimeout(() => countUp(el, target, sep, dur), i * 120);
                });
            });
        }, { threshold: 0.25 });

        countIO.observe(statsGrid);
    }
})();
</script>
