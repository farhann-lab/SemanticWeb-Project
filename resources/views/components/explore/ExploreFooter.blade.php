{{-- resources/views/components/explore/ExploreFooter.blade.php --}}


{{-- Footer Full Width (Tanpa Rounded & Melebar Penuh) --}}
<footer class=" w-full bg-gray-950 text-white border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">

            {{-- Col 1: Brand --}}
            <div class="space-y-4">
                <div>
                    <span class="text-base font-extrabold uppercase tracking-wider">HeritageFinder</span>
                    <span class="ml-2 text-[9px] font-bold uppercase tracking-widest text-gray-500 border border-gray-700 px-1.5 py-0.5 rounded">Archive</span>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    An open curatorial catalogue pairing documentary preservation with spatial intelligence. Designed under strict monochrome editorial minimalism.
                </p>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Source Veracity</p>
                    <p class="text-[11px] text-gray-500 leading-relaxed">
                        Fed via UNESCO World Heritage Center Linked Data & Wikidata SPARQL endpoint graph queries.
                    </p>
                </div>
            </div>

            {{-- Col 2: Indices --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Indices</h3>
                <nav class="flex flex-col gap-2.5">
                    <a href="{{ url('/search') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Master Catalog</a>
                    <a href="{{ url('/discover') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Cartographic Index</a>
                    <a href="{{ url('/search?danger=true') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Endangered Sites</a>
                    <a href="{{ url('/discover') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Intangible Culture</a>
                </nav>
            </div>

            {{-- Col 3: Curatorial --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Curatorial</h3>
                <nav class="flex flex-col gap-2.5">
                    <a href="{{ url('/about') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Colophon & Ethos</a>
                    <a href="#" class="text-xs text-gray-300 hover:text-white transition-colors">SPARQL API</a>
                    <a href="{{ url('/about') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Taxonomy Standards</a>
                    <a href="{{ url('/about') }}" class="text-xs text-gray-300 hover:text-white transition-colors">Citation Protocol</a>
                </nav>
            </div>

            {{-- Col 4: Institutional Access --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Institutional Access</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Query endpoints, open archival data repositories, and digital preservation consortiums.
                </p>
                <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    SPARQL Node Active
                </div>
            </div>
        </div>

        {{-- Copyright row --}}
        <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[11px] text-gray-500">
                © {{ date('Y') }} HeritageFinder Archive. Dedicated to public domain cultural knowledge.
            </p>
            <nav class="flex items-center gap-4">
                <a href="#" class="text-[11px] text-gray-500 hover:text-white transition-colors">Wikidata Endpoint</a>
                <a href="#" class="text-[11px] text-gray-500 hover:text-white transition-colors">UNESCO License</a>
                <a href="#" class="text-[11px] text-gray-500 hover:text-white transition-colors">Privacy</a>
            </nav>
        </div>
    </div>
</footer>