{{-- resources/views/components/Navbar.blade.php --}}
@props([
    'active' => 'home'
])

<div x-data="{
    isScrolled: false,
    searchOpen: false,
    mobileMenuOpen: false,
    activeSegment: 'keyword',
    q: '{{ request('q', '') }}',
    country: '{{ request('country', '') }}',
    region: '{{ request('region', '') }}',
    category: '{{ request('category', '') }}',
    criterion: '{{ request('criterion', '') }}',
    year_min: '{{ request('year_min', '') }}',
    year_max: '{{ request('year_max', '') }}',
    danger: '{{ request('danger', '') }}',
    transboundary: '{{ request('transboundary', '') }}',
    sort: '{{ request('sort', 'name_asc') }}',
    clearAll() {
        this.q = '';
        this.country = '';
        this.region = '';
        this.category = '';
        this.criterion = '';
        this.year_min = '';
        this.year_max = '';
        this.danger = '';
        this.transboundary = '';
    }
}"
x-init="
    const checkScroll = () => { isScrolled = window.pageYOffset > 20; };
    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll();
"
@keydown.escape.window="searchOpen = false; mobileMenuOpen = false"
class="relative z-50">

    {{-- Overlay Scrim when Search/Filter is active --}}
    <div x-show="searchOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="searchOpen = false"
         class="fixed inset-0 bg-[#0A0A0A]/45 backdrop-blur-[6px] z-40"
         style="display: none;">
    </div>

    {{-- Main Navbar Container --}}
    <header :class="{
                'h-16 bg-white/85 backdrop-blur-md shadow-sm border-b border-gray-200/80': isScrolled,
                'h-[88px] bg-white border-b border-gray-100': !isScrolled
            }"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-350 ease-in-out">
        <div class="max-w-[1280px] h-full mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between gap-4">
            
            {{-- 1. Left: Brand / Logo --}}
            <div class="flex items-center flex-shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group focus:outline-none">
                    <div class="w-9 h-9 rounded-full bg-gray-950 text-white flex items-center justify-center transition-transform group-hover:scale-105">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="21" x2="21" y2="21"/>
                            <line x1="6" y1="21" x2="6" y2="10"/>
                            <line x1="10" y1="21" x2="10" y2="10"/>
                            <line x1="14" y1="21" x2="14" y2="10"/>
                            <line x1="18" y1="21" x2="18" y2="10"/>
                            <polygon points="12 3 2 10 22 10 12 3"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base tracking-wider uppercase text-gray-950 leading-tight">
                            HeritageFinder
                        </span>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-gray-400 leading-none">
                            Semantic Web
                        </span>
                    </div>
                </a>
            </div>

            {{-- 2. Center: Desktop Search Bar (Airbnb Inspired Pill) --}}
            <div class="hidden md:flex flex-1 justify-center max-w-2xl px-2">
                {{-- Compact Pill (Shown when scrolled and search not open) --}}
                <div x-show="isScrolled && !searchOpen" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     @click="searchOpen = true"
                     class="h-12 w-full max-w-[340px] px-4 rounded-full border border-gray-200 bg-white/95 shadow-sm hover:shadow-md cursor-pointer flex items-center justify-between transition-all group">
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-4 h-4 text-gray-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-950 truncate" x-text="q ? q : 'Search heritage...'">
                            Search heritage...
                        </span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-950 text-white flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                </div>

                {{-- Expanded Segmented Pill (Shown when at top OR when search is clicked) --}}
                <div x-show="!isScrolled || searchOpen"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     :class="searchOpen ? 'ring-2 ring-gray-950 shadow-md' : 'shadow-sm hover:shadow-md'"
                     class="h-14 w-full max-w-[640px] rounded-full border border-gray-200 bg-white flex items-center p-1.5 transition-all">
                    
                    {{-- Segment 1: Keyword --}}
                    <div @click="searchOpen = true; activeSegment = 'keyword'"
                         :class="activeSegment === 'keyword' && searchOpen ? 'bg-gray-100 shadow-inner' : 'hover:bg-gray-50'"
                         class="flex-1 px-4 py-1.5 rounded-full cursor-pointer transition-colors flex flex-col justify-center">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Keyword</span>
                        <input type="text"
                               x-model="q"
                               @focus="searchOpen = true; activeSegment = 'keyword'"
                               placeholder="Search name, site..."
                               class="w-full text-xs font-semibold text-gray-950 bg-transparent placeholder-gray-400 focus:outline-none truncate">
                    </div>

                    <div class="h-6 w-px bg-gray-200"></div>

                    {{-- Segment 2: Country --}}
                    <div @click="searchOpen = true; activeSegment = 'country'"
                         :class="activeSegment === 'country' && searchOpen ? 'bg-gray-100 shadow-inner' : 'hover:bg-gray-50'"
                         class="flex-1 px-4 py-1.5 rounded-full cursor-pointer transition-colors flex flex-col justify-center">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Country</span>
                        <span class="text-xs font-semibold text-gray-950 truncate" x-text="country ? country : 'Any country'">
                            Any country
                        </span>
                    </div>

                    <div class="h-6 w-px bg-gray-200"></div>

                    {{-- Segment 3: Category --}}
                    <div @click="searchOpen = true; activeSegment = 'category'"
                         :class="activeSegment === 'category' && searchOpen ? 'bg-gray-100 shadow-inner' : 'hover:bg-gray-50'"
                         class="flex-1 px-4 py-1.5 rounded-full cursor-pointer transition-colors flex flex-col justify-center">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Category</span>
                        <span class="text-xs font-semibold text-gray-950 truncate" x-text="category ? category : 'All types'">
                            All types
                        </span>
                    </div>

                    {{-- Search Action Button --}}
                    <button type="button"
                            @click="if (!searchOpen) { searchOpen = true; } else { $refs.searchForm.submit(); }"
                            class="w-11 h-11 rounded-full bg-gray-950 text-white flex items-center justify-center hover:bg-gray-800 transition-colors flex-shrink-0 ml-1">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- 3. Right: Navigation Menu Links --}}
            <nav class="hidden md:flex items-center gap-7">
                <a href="{{ url('/') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 hover:text-gray-950 transition-colors group">
                    <span>Home</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform duration-200 ease-out origin-left {{ request()->is('/') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/search') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 hover:text-gray-950 transition-colors group">
                    <span>Explore</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform duration-200 ease-out origin-left {{ request()->is('search*') || request()->is('explore*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/map') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 hover:text-gray-950 transition-colors group">
                    <span>Map</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform duration-200 ease-out origin-left {{ request()->is('map*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/discover') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 hover:text-gray-950 transition-colors group">
                    <span>Discover</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform duration-200 ease-out origin-left {{ request()->is('discover*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/about') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 hover:text-gray-950 transition-colors group">
                    <span>About</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform duration-200 ease-out origin-left {{ request()->is('about*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
            </nav>

            {{-- 4. Mobile Menu Trigger Button --}}
            <div class="flex md:hidden items-center gap-2">
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center justify-center text-gray-950 hover:bg-gray-100 focus:outline-none transition-colors">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Row 2: Search Pill Bar (<768px) --}}
        <div class="md:hidden px-4 pb-3">
            <button type="button"
                    @click="searchOpen = true"
                    class="w-full h-11 px-4 rounded-full border border-gray-200 bg-white/95 shadow-sm flex items-center justify-between text-left">
                <div class="flex items-center gap-2.5 truncate">
                    <svg class="w-4 h-4 text-gray-950 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-950 truncate" x-text="q ? q : 'Search heritage, country, category...'">
                        Search heritage, country, category...
                    </span>
                </div>
                <div class="w-7 h-7 rounded-full bg-gray-950 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
            </button>
        </div>
    </header>

    {{-- Mobile Drawer Menu (<768px) --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="fixed top-24 left-0 right-0 z-40 bg-white border-b border-gray-200 px-6 py-6 md:hidden shadow-lg"
         style="display: none;">
        <nav class="flex flex-col gap-4">
            <a href="{{ url('/') }}" class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Home</a>
            <a href="{{ url('/search') }}" class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Explore</a>
            <a href="{{ url('/map') }}" class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Map</a>
            <a href="{{ url('/discover') }}" class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Discover</a>
            <a href="{{ url('/about') }}" class="text-base font-bold text-gray-950 py-2">About</a>
        </nav>
    </div>

    {{-- =========================================================================
         Full Filter Panel (Desktop Floating Modal & Mobile Fullscreen Bottom Sheet)
         Connected to SearchController / SPARQL endpoint via GET /search
         ========================================================================= --}}
    <div x-show="searchOpen"
         x-transition:enter="transition-all ease-out duration-350 transform"
         x-transition:enter-start="opacity-0 -translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition-all ease-in duration-250 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-6 scale-95"
         class="fixed top-0 left-0 right-0 z-50 md:top-[96px] md:max-w-4xl md:mx-auto md:px-4"
         style="display: none;">

        <div class="bg-white w-full h-screen md:h-auto md:max-h-[85vh] md:rounded-[32px] shadow-2xl border border-gray-200 flex flex-col overflow-hidden">
            
            {{-- Panel Header --}}
            <div class="px-6 py-5 md:px-8 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-950"></span>
                    <h2 class="text-lg md:text-xl font-bold tracking-tight text-gray-950">
                        Explore World Heritage Sites
                    </h2>
                </div>
                <button type="button" 
                        @click="searchOpen = false" 
                        class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-950 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Form Connected to SPARQL Search Controller (/search) --}}
            <form x-ref="searchForm" 
                  action="{{ url('/search') }}" 
                  method="GET" 
                  class="flex-1 overflow-y-auto px-6 py-6 md:px-8 space-y-6">

                {{-- 1. Keyword Search Input --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Heritage Name or Keyword
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="q"
                               x-model="q"
                               placeholder="e.g. Borobudur, Prambanan, Serengeti, Machu Picchu..."
                               class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950 transition-colors">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- 2. Country & Region Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Country Code / ID
                        </label>
                        <input type="text"
                               name="country"
                               x-model="country"
                               placeholder="e.g. ID, FR, IT, JP"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Region
                        </label>
                        <input type="text"
                               name="region"
                               x-model="region"
                               placeholder="e.g. APA, EUR, AFR"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950 transition-colors">
                    </div>
                </div>

                {{-- 3. Category Selector --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Category
                    </label>
                    <div class="flex flex-wrap gap-2.5">
                        <input type="hidden" name="category" :value="category">
                        <button type="button"
                                @click="category = ''"
                                :class="category === '' ? 'bg-gray-950 text-white border-gray-950' : 'bg-white text-gray-950 border-gray-300 hover:border-gray-950'"
                                class="h-9 px-5 rounded-full border text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5">
                            All Types
                        </button>
                        <button type="button"
                                @click="category = 'Cultural'"
                                :class="category === 'Cultural' ? 'bg-gray-950 text-white border-gray-950' : 'bg-white text-gray-950 border-gray-300 hover:border-gray-950'"
                                class="h-9 px-5 rounded-full border text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="21" x2="21" y2="21"/>
                                <line x1="6" y1="21" x2="6" y2="10"/>
                                <line x1="10" y1="21" x2="10" y2="10"/>
                                <line x1="14" y1="21" x2="14" y2="10"/>
                                <line x1="18" y1="21" x2="18" y2="10"/>
                                <polygon points="12 3 2 10 22 10 12 3"/>
                            </svg>
                            Cultural
                        </button>
                        <button type="button"
                                @click="category = 'Natural'"
                                :class="category === 'Natural' ? 'bg-gray-950 text-white border-gray-950' : 'bg-white text-gray-950 border-gray-300 hover:border-gray-950'"
                                class="h-9 px-5 rounded-full border text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                            </svg>
                            Natural
                        </button>
                        <button type="button"
                                @click="category = 'Mixed'"
                                :class="category === 'Mixed' ? 'bg-gray-950 text-white border-gray-950' : 'bg-white text-gray-950 border-gray-300 hover:border-gray-950'"
                                class="h-9 px-5 rounded-full border text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                                <polyline points="2 17 12 22 22 17"/>
                                <polyline points="2 12 12 17 22 12"/>
                            </svg>
                            Mixed
                        </button>
                    </div>
                </div>

                {{-- 4. Inscription Year Range --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Inscription Year Range
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number"
                               name="year_min"
                               x-model="year_min"
                               placeholder="Min (e.g. 1978)"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950 tabular-nums transition-colors">
                        <input type="number"
                               name="year_max"
                               x-model="year_max"
                               placeholder="Max (e.g. 2024)"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950 tabular-nums transition-colors">
                    </div>
                </div>

                {{-- 5. UNESCO Criteria Chips (i - x) --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        UNESCO Criteria
                    </label>
                    <input type="hidden" name="criterion" :value="criterion">
                    <div class="flex flex-wrap gap-2">
                        @foreach(['(i)', '(ii)', '(iii)', '(iv)', '(v)', '(vi)', '(vii)', '(viii)', '(ix)', '(x)'] as $crit)
                            <button type="button"
                                    @click="criterion = (criterion === '{{ $crit }}' ? '' : '{{ $crit }}')"
                                    :class="criterion === '{{ $crit }}' ? 'bg-gray-950 text-white border-gray-950' : 'bg-white text-gray-950 border-gray-200 hover:border-gray-950'"
                                    class="w-11 h-9 rounded-lg border text-xs font-bold tabular-nums transition-colors flex items-center justify-center">
                                {{ $crit }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 6. Toggles: In Danger & Transboundary --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-200">
                    {{-- Toggle: In Danger --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 bg-gray-50/50">
                        <div>
                            <span class="block text-xs font-bold text-gray-950">World Heritage in Danger</span>
                            <span class="block text-[11px] text-gray-500">Filter properties under threat</span>
                        </div>
                        <input type="hidden" name="danger" :value="danger">
                        <button type="button"
                                @click="danger = (danger === 'true' ? '' : 'true')"
                                :class="danger === 'true' ? 'bg-gray-950' : 'bg-gray-300'"
                                class="w-11 h-6 rounded-full transition-colors relative flex items-center p-0.5 focus:outline-none">
                            <span :class="danger === 'true' ? 'translate-x-5' : 'translate-x-0'"
                                  class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"></span>
                        </button>
                    </div>

                    {{-- Toggle: Transboundary --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 bg-gray-50/50">
                        <div>
                            <span class="block text-xs font-bold text-gray-950">Transboundary Property</span>
                            <span class="block text-[11px] text-gray-500">Shared across multiple countries</span>
                        </div>
                        <input type="hidden" name="transboundary" :value="transboundary">
                        <button type="button"
                                @click="transboundary = (transboundary === 'true' ? '' : 'true')"
                                :class="transboundary === 'true' ? 'bg-gray-950' : 'bg-gray-300'"
                                class="w-11 h-6 rounded-full transition-colors relative flex items-center p-0.5 focus:outline-none">
                            <span :class="transboundary === 'true' ? 'translate-x-5' : 'translate-x-0'"
                                  class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"></span>
                        </button>
                    </div>
                </div>

                {{-- Sort Option --}}
                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Sort Order
                    </label>
                    <select name="sort" 
                            x-model="sort"
                            class="w-full h-11 px-4 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-950 focus:outline-none focus:border-gray-950 cursor-pointer">
                        <option value="name_asc">Name (A — Z)</option>
                        <option value="name_desc">Name (Z — A)</option>
                        <option value="year_asc">Inscription Year (Oldest First)</option>
                        <option value="year_desc">Inscription Year (Newest First)</option>
                    </select>
                </div>
            </form>

            {{-- Panel Footer Actions --}}
            <div class="px-6 py-4 md:px-8 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-shrink-0">
                <button type="button"
                        @click="clearAll()"
                        class="text-xs font-bold uppercase tracking-wider text-gray-600 hover:text-gray-950 underline underline-offset-4 focus:outline-none transition-colors">
                    Clear all filters
                </button>
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="searchOpen = false"
                            class="px-5 py-2.5 rounded-full border border-gray-300 text-xs font-bold uppercase tracking-wider text-gray-950 hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="button"
                            @click="$refs.searchForm.submit()"
                            class="px-6 py-2.5 rounded-full bg-gray-950 hover:bg-gray-800 text-xs font-bold uppercase tracking-wider text-white shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span>Search SPARQL</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
