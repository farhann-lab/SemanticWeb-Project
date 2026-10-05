{{-- resources/views/components/HeroSection.blade.php --}}
@props([
    'stats' => [
        'total' => 0,
        'countries' => 0,
        'regions' => 0
    ]
])

<div class="relative w-full bg-[#0A0A0A] text-white">
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
                    src="{{ asset('img/3.png') }}"  
                    alt="Heritage Forest Layer" 
                    class="w-full h-full object-cover opacity-80"
                />
            </div>

            {{-- Stage 1: Display Tagline & Text Overlay --}}
            <div 
                data-parallax-layer="3" 
                class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none px-4 text-center z-10"
            >
                <div class="max-w-5xl mx-auto space-y-4">
                    <p class="text-xs md:text-sm font-bold uppercase tracking-[0.2em] text-gray-300">
                        Semantic Explorer for World Heritage Sites
                    </p>
                    <h1 class="text-[clamp(3rem,7vw,6rem)] font-extrabold leading-none tracking-tight text-white drop-shadow-2xl uppercase">
                        HeritageFinder
                    </h1>
                    <p class="text-sm md:text-lg text-gray-200 max-w-2xl mx-auto font-medium drop-shadow">
                        Uncover interconnected historical sites across the globe powered by RDF Knowledge Graph & SPARQL.
                    </p>
                </div>

                {{-- Scroll Indicator --}}
                <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-80">
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

        {{-- Fade Gradient Transition di bagian bawah section hero --}}
        <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-[#0A0A0A] via-[#0A0A0A]/80 to-transparent z-30 pointer-events-none"></div>
    </section>

    {{-- Stage 2 & 3: Transisi Tertimpa & Section Statistik --}}
    <section class="relative z-30 bg-[#0A0A0A] rounded-t-[40px] -mt-10 pt-16 pb-24 px-6 md:px-12 border-t border-gray-800/60 shadow-2xl">
        <div class="max-w-6xl mx-auto space-y-16">
            
            {{-- Header Transisi --}}
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <span class="text-xs font-bold uppercase tracking-[0.15em] text-gray-400">
                    Connected Knowledge Base
                </span>
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold text-white tracking-tight leading-snug">
                    Preserving World History Through Linked Open Data
                </h2>
            </div>

            {{-- Stage 3: Section Statistik (H1 clamp + tabular-nums) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Stat Card 1 --}}
                <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800 flex flex-col justify-between hover:border-gray-700 transition-colors">
                    <span class="text-[0.75rem] font-bold tracking-widest text-gray-400 uppercase">
                        World Heritage Properties
                    </span>
                    <div class="mt-4">
                        <span class="text-[clamp(2.25rem,4.5vw,3.5rem)] font-extrabold text-white leading-none tabular-nums tracking-tight">
                            {{ number_format($stats['total'] ?? 1154) }}
                        </span>
                        <p class="text-xs text-gray-400 mt-2">Cultural, Natural, and Mixed sites indexed in Fuseki.</p>
                    </div>
                </div>

                {{-- Stat Card 2 --}}
                <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800 flex flex-col justify-between hover:border-gray-700 transition-colors">
                    <span class="text-[0.75rem] font-bold tracking-widest text-gray-400 uppercase">
                        Participating Countries
                    </span>
                    <div class="mt-4">
                        <span class="text-[clamp(2.25rem,4.5vw,3.5rem)] font-extrabold text-white leading-none tabular-nums tracking-tight">
                            {{ number_format($stats['countries'] ?? 168) }}
                        </span>
                        <p class="text-xs text-gray-400 mt-2">State parties with inscribed UNESCO heritage.</p>
                    </div>
                </div>

                {{-- Stat Card 3 --}}
                <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800 flex flex-col justify-between hover:border-gray-700 transition-colors">
                    <span class="text-[0.75rem] font-bold tracking-widest text-gray-400 uppercase">
                        Geographic Regions
                    </span>
                    <div class="mt-4">
                        <span class="text-[clamp(2.25rem,4.5vw,3.5rem)] font-extrabold text-white leading-none tabular-nums tracking-tight">
                            {{ number_format($stats['regions'] ?? 5) }}
                        </span>
                        <p class="text-xs text-gray-400 mt-2">Global regional zones mapped across continents.</p>
                    </div>
                </div>
            </div>

            {{-- Stage 4: Tombol Aksi di Akhir Scroll --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-6">
                {{-- Primary Button: Explore --}}
                <a href="{{ url('/search') }}" 
                   class="w-full sm:w-auto h-[52px] px-8 rounded-full bg-white text-[#0A0A0A] font-bold text-sm tracking-wide flex items-center justify-center hover:bg-gray-200 transition-all hover:scale-105 active:scale-95 shadow-lg">
                    Explore Heritage
                </a>

                {{-- Secondary Button: Map --}}
                <a href="{{ url('/map') }}" 
                   class="w-full sm:w-auto h-[52px] px-8 rounded-full border-[1.5px] border-white/80 bg-transparent text-white font-bold text-sm tracking-wide flex items-center justify-center hover:bg-white/10 transition-all hover:scale-105 active:scale-95">
                    Interactive Map
                </a>
            </div>
        </div>
    </section>
</div>

