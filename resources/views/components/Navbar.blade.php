{{-- resources/views/components/Navbar.blade.php --}}
@props([
    'active' => 'home'
])

{{--
    ANIMATION STRATEGY
    ──────────────────
    The compact ↔ expanded search bar is ONE single DOM element whose shape
    morphs via CSS transitions (max-width, height, border-radius, padding).
    No x-show swap between two elements → no glitch, no overlap.

    Category pills sit in a separate row above the header's row-1.
    They animate with clip-path + opacity so they collapse to zero height
    without any layout reflow touching the search bar row.
--}}

<style>
    /* ── Search bar morph ────────────────────────────────────────────── */
    .search-morph {
        transition:
            max-width      350ms cubic-bezier(0.4, 0, 0.2, 1),
            height         350ms cubic-bezier(0.4, 0, 0.2, 1),
            border-radius  350ms cubic-bezier(0.4, 0, 0.2, 1),
            box-shadow     300ms ease,
            outline-width  200ms ease,
            outline-color  200ms ease;
        will-change: max-width, height;
    }
    .search-morph.is-compact {
        max-width: 340px;
        height: 3rem;       /* h-12 */
        border-radius: 9999px;
    }
    .search-morph.is-expanded {
        max-width: 580px;
        height: 3.5rem;     /* h-14 */
        border-radius: 9999px;
    }
    .search-morph.is-focused {
        outline: 2px solid #030712;
        outline-offset: 0px;
    }

    /* ── Segments inside the pill ────────────────────────────────────── */
    /* In compact mode the label + separator are hidden via opacity/width */
    .seg-label,
    .seg-separator {
        transition: opacity 250ms ease, width 250ms ease;
    }
    .search-morph.is-compact .seg-label     { opacity: 0; height: 0; overflow: hidden; }
    .search-morph.is-compact .seg-separator { opacity: 0; width: 0;  overflow: hidden; }
    .search-morph.is-compact .seg-country,
    .search-morph.is-compact .seg-filters   { max-width: 0; overflow: hidden; padding: 0; opacity: 0;
                                              transition: max-width 300ms ease, padding 300ms ease, opacity 200ms ease; }
    .search-morph.is-expanded .seg-country,
    .search-morph.is-expanded .seg-filters  { max-width: 200px; opacity: 1;
                                              transition: max-width 350ms cubic-bezier(0.4,0,0.2,1),
                                                          opacity   300ms ease 100ms; }

    /* In compact mode show the compact label, hide it in expanded */
    .compact-label { transition: opacity 200ms ease; }
    .search-morph.is-expanded .compact-label { opacity: 0; pointer-events: none; position: absolute; }
    .search-morph.is-compact  .compact-label { opacity: 1; }

    /* ── Category pills bar ──────────────────────────────────────────── */
    .category-bar {
        /* clip from top so it folds up without shifting anything below */
        overflow: hidden;
        transition:
            max-height 350ms cubic-bezier(0.4, 0, 0.2, 1),
            opacity    280ms ease;
        max-height: 60px;   /* enough to show one row of pills */
        opacity: 1;
    }
    .category-bar.is-hidden {
        max-height: 0;
        opacity: 0;
        pointer-events: none;
    }

    /* ── Navbar height morph ─────────────────────────────────────────── */
    .navbar-row1 {
        transition: height 350ms cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<div x-data="{
    isScrolled: false,
    searchOpen: false,
    mobileMenuOpen: false,
    activeSegment: 'keyword',
    q:             '{{ request('q', '') }}',
    country:       '{{ request('country', '') }}',
    region:        '{{ request('region', '') }}',
    category:      '{{ request('category', '') }}',
    criterion:     '{{ request('criterion', '') }}',
    year_min:      '{{ request('year_min', '') }}',
    year_max:      '{{ request('year_max', '') }}',
    danger:        '{{ request('danger', '') }}',
    transboundary: '{{ request('transboundary', '') }}',
    sort:          '{{ request('sort', 'name_asc') }}',

    /* Derived: compact = scrolled AND search panel closed */
    get isCompact() { return this.isScrolled && !this.searchOpen; },
    /* Category bar visible when NOT compact (i.e. at top OR search open) */
    get showCategoryBar() { return !this.isCompact; },

    clearAll() {
        this.q = this.country = this.region = this.category =
        this.criterion = this.year_min = this.year_max =
        this.danger = this.transboundary = '';
    },

    selectCategory(val) {
        this.category = val;
        this.$nextTick(() => this.$refs.categoryPillForm.submit());
    },

    openSearch() {
        this.searchOpen = true;
    }
}"
x-init="
    const onScroll = () => {
        isScrolled = window.pageYOffset > 20;
        if (!isScrolled) searchOpen = false;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
"
@keydown.escape.window="searchOpen = false; mobileMenuOpen = false"
class="relative z-50">

    {{-- ── Overlay Scrim ─────────────────────────────────────────────────── --}}
    <div x-show="searchOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="searchOpen = false"
         class="fixed inset-0 bg-[#0A0A0A]/45 backdrop-blur-[6px] z-40"
         style="display:none">
    </div>

    {{-- ── Main Header ────────────────────────────────────────────────────── --}}
    <header :class="isScrolled
                ? 'bg-white/65 backdrop-blur-md shadow-sm border-b border-gray-200/80'
                : 'bg-white border-b border-gray-100'"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-350 ease-in-out">

        {{-- ════════════════════════════════════════════════════════════════
             ROW 1 — Logo · Search Bar · Nav Links
             ════════════════════════════════════════════════════════════════ --}}
        <div :class="isScrolled ? 'h-[68px]' : 'h-[80px]'"
             class="navbar-row1 max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10
                    flex items-center justify-between gap-4">

            {{-- Left: Brand --}}
            <div class="flex items-center flex-shrink-0 w-[220px]">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group focus:outline-none">
                    <div class="w-9 h-9 rounded-full bg-gray-950 text-white flex items-center justify-center
                                transition-transform group-hover:scale-105">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="21" x2="21" y2="21"/>
                            <line x1="6" y1="21" x2="6"  y2="10"/>
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

            {{-- ── Center: Single morphing search bar (desktop only) ── --}}
            <div class="hidden md:flex flex-1 justify-center items-center">

                {{--
                    ONE element — shape morphs between compact and expanded.
                    - .is-compact  : narrow pill, single keyword line
                    - .is-expanded : wide segmented pill
                --}}
                <div :class="[
                         isCompact ? 'is-compact' : 'is-expanded',
                         searchOpen ? 'is-focused' : (isCompact ? '' : 'hover:shadow-md shadow-sm')
                     ]"
                     class="search-morph relative w-full border border-gray-200 bg-white
                            flex items-center p-1.5 overflow-hidden cursor-pointer"
                     @click="if (isCompact) { openSearch(); }">

                    {{-- ── Compact view label (hidden when expanded) ── --}}
                    <div class="compact-label flex items-center gap-3 flex-1 px-3 min-w-0">
                        <svg class="w-4 h-4 text-gray-950 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-950 truncate"
                              x-text="q ? q : 'Search heritage...'"></span>
                    </div>

                    {{-- ── Segment 1: Keyword (always visible in expanded) ── --}}
                    <div @click.stop="if (!isCompact) { searchOpen = true; activeSegment = 'keyword'; }"
                         :class="activeSegment === 'keyword' && searchOpen ? 'bg-gray-100 shadow-inner' : (!isCompact ? 'hover:bg-gray-50' : '')"
                         class="seg-keyword flex-shrink-0 px-4 py-1.5 rounded-full transition-colors
                                flex flex-col justify-center"
                         :style="isCompact ? 'display:none' : ''">
                        <span class="seg-label text-[11px] font-bold uppercase tracking-wider text-gray-500">Keyword</span>
                        <input type="text"
                               x-model="q"
                               @focus="searchOpen = true; activeSegment = 'keyword'"
                               @click.stop
                               placeholder="Search name, site..."
                               class="w-32 text-xs font-semibold text-gray-950 bg-transparent
                                      placeholder-gray-400 focus:outline-none truncate">
                    </div>

                    {{-- Separator 1 --}}
                    <div class="seg-separator h-6 w-px bg-gray-200 flex-shrink-0"
                         :style="isCompact ? 'display:none' : ''"></div>

                    {{-- ── Segment 2: Country ── --}}
                    <div @click.stop="searchOpen = true; activeSegment = 'country'"
                         :class="activeSegment === 'country' && searchOpen ? 'bg-gray-100 shadow-inner' : 'hover:bg-gray-50'"
                         class="seg-country flex-1 px-4 py-1.5 rounded-full cursor-pointer
                                transition-colors flex flex-col justify-center">
                        <span class="seg-label text-[11px] font-bold uppercase tracking-wider text-gray-500">Country</span>
                        <span class="text-xs font-semibold text-gray-950 truncate"
                              x-text="country ? country : 'Any country'"></span>
                    </div>

                    {{-- Separator 2 --}}
                    <div class="seg-separator h-6 w-px bg-gray-200 flex-shrink-0"
                         :style="isCompact ? 'display:none' : ''"></div>

                    {{-- ── Segment 3: Filters ── --}}
                    <div @click.stop="searchOpen = true; activeSegment = 'filters'"
                         :class="activeSegment === 'filters' && searchOpen ? 'bg-gray-100 shadow-inner' : 'hover:bg-gray-50'"
                         class="seg-filters flex-1 px-4 py-1.5 rounded-full cursor-pointer
                                transition-colors flex flex-col justify-center">
                        <span class="seg-label text-[11px] font-bold uppercase tracking-wider text-gray-500">Filters</span>
                        <span class="text-xs font-semibold text-gray-950 truncate">Year, criteria...</span>
                    </div>

                    {{-- Search Action Button --}}
                    <button type="button"
                            @click.stop="if (isCompact) { openSearch(); } else if (searchOpen) { $refs.searchForm.submit(); } else { searchOpen = true; }"
                            class="w-10 h-10 rounded-full bg-gray-950 text-white flex items-center justify-center
                                   hover:bg-gray-800 transition-colors flex-shrink-0 ml-1">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Right: Nav Links --}}
            <nav class="hidden md:flex items-center gap-6 flex-shrink-0 w-[220px] justify-end">
                <a href="{{ url('/') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 transition-colors group">
                    <span>Home</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform
                                 duration-200 ease-out origin-left
                                 {{ request()->is('/') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/search') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 transition-colors group">
                    <span>Explore</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform
                                 duration-200 ease-out origin-left
                                 {{ request()->is('search*') || request()->is('explore*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/map') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 transition-colors group">
                    <span>Map</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform
                                 duration-200 ease-out origin-left
                                 {{ request()->is('map*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
                <a href="{{ url('/about') }}"
                   class="relative py-2 text-sm font-semibold text-gray-950 transition-colors group">
                    <span>About</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 transition-transform
                                 duration-200 ease-out origin-left
                                 {{ request()->is('about*') ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                </a>
            </nav>

            {{-- Mobile hamburger --}}
            <div class="flex md:hidden items-center gap-2">
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center
                               justify-center text-gray-950 hover:bg-gray-100 focus:outline-none transition-colors">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6"  x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         style="display:none">
                        <line x1="18" y1="6"  x2="6"  y2="18"/>
                        <line x1="6"  y1="6"  x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════
             ROW 2 — Category Pills  (desktop only, collapses on scroll)
             ════════════════════════════════════════════════════════════════ --}}

        {{-- Hidden form: carries all current filters when a pill is clicked --}}
        <form x-ref="categoryPillForm" action="{{ url('/search') }}" method="GET" class="hidden">
            <input type="hidden" name="q"             :value="q">
            <input type="hidden" name="country"       :value="country">
            <input type="hidden" name="region"        :value="region">
            <input type="hidden" name="category"      :value="category">
            <input type="hidden" name="criterion"     :value="criterion">
            <input type="hidden" name="year_min"      :value="year_min">
            <input type="hidden" name="year_max"      :value="year_max">
            <input type="hidden" name="danger"        :value="danger">
            <input type="hidden" name="transboundary" :value="transboundary">
            <input type="hidden" name="sort"          :value="sort">
        </form>

        <div :class="showCategoryBar ? '' : 'is-hidden'"
             class="category-bar hidden md:flex justify-center items-center gap-1 pb-3 px-4">

            {{-- All Types --}}
            <button type="button" @click="selectCategory('')"
                    :class="category === ''
                        ? 'border-gray-950 text-gray-950 font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                    class="relative h-9 px-5 rounded-full border text-xs font-semibold tracking-wide
                           transition-all duration-200 flex items-center gap-2 group">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10
                             15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                All Types
                <span :class="category === '' ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"
                      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-4/5 h-[2px] bg-gray-950
                             rounded-full transition-transform duration-200 ease-out origin-center"></span>
            </button>

            <div class="w-px h-4 bg-gray-200 mx-1"></div>

            {{-- Cultural --}}
            <button type="button" @click="selectCategory('Cultural')"
                    :class="category === 'Cultural'
                        ? 'border-gray-950 text-gray-950 font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                    class="relative h-9 px-5 rounded-full border text-xs font-semibold tracking-wide
                           transition-all duration-200 flex items-center gap-2 group">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="21" x2="21" y2="21"/>
                    <line x1="6" y1="21" x2="6"  y2="10"/>
                    <line x1="10" y1="21" x2="10" y2="10"/>
                    <line x1="14" y1="21" x2="14" y2="10"/>
                    <line x1="18" y1="21" x2="18" y2="10"/>
                    <polygon points="12 3 2 10 22 10 12 3"/>
                </svg>
                Cultural
                <span :class="category === 'Cultural' ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"
                      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-4/5 h-[2px] bg-gray-950
                             rounded-full transition-transform duration-200 ease-out origin-center"></span>
            </button>

            {{-- Natural --}}
            <button type="button" @click="selectCategory('Natural')"
                    :class="category === 'Natural'
                        ? 'border-gray-950 text-gray-950 font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                    class="relative h-9 px-5 rounded-full border text-xs font-semibold tracking-wide
                           transition-all duration-200 flex items-center gap-2 group">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8
                             0 5.5-4.78 10-10 10Z"/>
                    <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                </svg>
                Natural
                <span :class="category === 'Natural' ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"
                      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-4/5 h-[2px] bg-gray-950
                             rounded-full transition-transform duration-200 ease-out origin-center"></span>
            </button>

            {{-- Mixed --}}
            <button type="button" @click="selectCategory('Mixed')"
                    :class="category === 'Mixed'
                        ? 'border-gray-950 text-gray-950 font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                    class="relative h-9 px-5 rounded-full border text-xs font-semibold tracking-wide
                           transition-all duration-200 flex items-center gap-2 group">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                    <polyline points="2 17 12 22 22 17"/>
                    <polyline points="2 12 12 17 22 12"/>
                </svg>
                Mixed
                <span :class="category === 'Mixed' ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"
                      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-4/5 h-[2px] bg-gray-950
                             rounded-full transition-transform duration-200 ease-out origin-center"></span>
            </button>
        </div>

        {{-- ── Mobile: Search Pill Row (<768px) ── --}}
        <div class="md:hidden px-4 pb-3">
            <button type="button" @click="searchOpen = true"
                    class="w-full h-11 px-4 rounded-full border border-gray-200 bg-white/95 shadow-sm
                           flex items-center justify-between text-left">
                <div class="flex items-center gap-2.5 truncate">
                    <svg class="w-4 h-4 text-gray-950 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-950 truncate"
                          x-text="q ? q : 'Search heritage, country, category...'">
                        Search heritage, country, category...
                    </span>
                </div>
                <div class="w-7 h-7 rounded-full bg-gray-950 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
            </button>
        </div>
    </header>

    {{-- ── Mobile Drawer ──────────────────────────────────────────────────── --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="fixed top-24 left-0 right-0 z-40 bg-white border-b border-gray-200
                px-6 py-6 md:hidden shadow-lg"
         style="display:none">
        <nav class="flex flex-col gap-4">
            <a href="{{ url('/') }}"         class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Home</a>
            <a href="{{ url('/search') }}"   class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Explore</a>
            <a href="{{ url('/map') }}"      class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Map</a>
            <a href="{{ url('/discover') }}" class="text-base font-bold text-gray-950 py-2 border-b border-gray-100">Discover</a>
            <a href="{{ url('/about') }}"    class="text-base font-bold text-gray-950 py-2">About</a>
        </nav>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         FILTER PANEL
         Desktop: floating modal anchored below navbar
         Mobile:  fullscreen
         ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="searchOpen"
         x-transition:enter="transition-all ease-out duration-350 transform"
         x-transition:enter-start="opacity-0 -translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition-all ease-in duration-250 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-6 scale-95"
         class="fixed inset-0 md:inset-auto md:top-[88px] md:left-1/2 md:-translate-x-1/2
                md:w-full md:max-w-3xl md:px-4 z-50"
         style="display:none">

        <div class="bg-white w-full h-full md:h-auto md:max-h-[82vh] md:rounded-[32px]
                    shadow-2xl border border-gray-200 flex flex-col overflow-hidden">

            {{-- Panel Header --}}
            <div class="px-6 py-5 md:px-8 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-950"></span>
                    <h2 class="text-lg md:text-xl font-bold tracking-tight text-gray-950">
                        Explore World Heritage Sites
                    </h2>
                </div>
                <button type="button" @click="searchOpen = false"
                        class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-950
                               flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6"  x2="6"  y2="18"/>
                        <line x1="6"  y1="6"  x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Scrollable Form --}}
            <form x-ref="searchForm"
                  action="{{ url('/search') }}"
                  method="GET"
                  class="flex-1 overflow-y-auto min-h-0 px-6 py-6 md:px-8 space-y-6">

                {{-- 1. Keyword --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Heritage Name or Keyword
                    </label>
                    <div class="relative">
                        <input type="text" name="q" x-model="q"
                               placeholder="e.g. Borobudur, Prambanan, Serengeti, Machu Picchu..."
                               class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 bg-white
                                      text-sm font-medium text-gray-950 placeholder-gray-400
                                      focus:outline-none focus:border-gray-950 transition-colors">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- 2. Country & Region --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Country Code / ID
                        </label>
                        <input type="text" name="country" x-model="country"
                               placeholder="e.g. ID, FR, IT, JP"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white
                                      text-sm font-medium text-gray-950 placeholder-gray-400
                                      focus:outline-none focus:border-gray-950 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Region
                        </label>
                        <input type="text" name="region" x-model="region"
                               placeholder="e.g. APA, EUR, AFR"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white
                                      text-sm font-medium text-gray-950 placeholder-gray-400
                                      focus:outline-none focus:border-gray-950 transition-colors">
                    </div>
                </div>

                {{-- 3. Inscription Year Range --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Inscription Year Range
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="year_min" x-model="year_min"
                               placeholder="Min (e.g. 1978)"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white
                                      text-sm font-medium text-gray-950 placeholder-gray-400
                                      focus:outline-none focus:border-gray-950 tabular-nums transition-colors">
                        <input type="number" name="year_max" x-model="year_max"
                               placeholder="Max (e.g. 2024)"
                               class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white
                                      text-sm font-medium text-gray-950 placeholder-gray-400
                                      focus:outline-none focus:border-gray-950 tabular-nums transition-colors">
                    </div>
                </div>

                {{-- 4. UNESCO Criteria --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        UNESCO Criteria
                    </label>
                    <input type="hidden" name="criterion" :value="criterion">
                    <div class="flex flex-wrap gap-2">
                        @foreach(['(i)', '(ii)', '(iii)', '(iv)', '(v)', '(vi)', '(vii)', '(viii)', '(ix)', '(x)'] as $crit)
                            <button type="button"
                                    @click="criterion = (criterion === '{{ $crit }}' ? '' : '{{ $crit }}')"
                                    :class="criterion === '{{ $crit }}'
                                        ? 'bg-gray-950 text-white border-gray-950'
                                        : 'bg-white text-gray-950 border-gray-200 hover:border-gray-950'"
                                    class="w-11 h-9 rounded-lg border text-xs font-bold tabular-nums
                                           transition-colors flex items-center justify-center">
                                {{ $crit }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 5. Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-200">
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 bg-gray-50/50">
                        <div>
                            <span class="block text-xs font-bold text-gray-950">World Heritage in Danger</span>
                            <span class="block text-[11px] text-gray-500">Filter properties under threat</span>
                        </div>
                        <input type="hidden" name="danger" :value="danger">
                        <button type="button"
                                @click="danger = (danger === 'true' ? '' : 'true')"
                                :class="danger === 'true' ? 'bg-gray-950' : 'bg-gray-300'"
                                class="w-11 h-6 rounded-full transition-colors relative flex items-center
                                       p-0.5 focus:outline-none flex-shrink-0">
                            <span :class="danger === 'true' ? 'translate-x-5' : 'translate-x-0'"
                                  class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 bg-gray-50/50">
                        <div>
                            <span class="block text-xs font-bold text-gray-950">Transboundary Property</span>
                            <span class="block text-[11px] text-gray-500">Shared across multiple countries</span>
                        </div>
                        <input type="hidden" name="transboundary" :value="transboundary">
                        <button type="button"
                                @click="transboundary = (transboundary === 'true' ? '' : 'true')"
                                :class="transboundary === 'true' ? 'bg-gray-950' : 'bg-gray-300'"
                                class="w-11 h-6 rounded-full transition-colors relative flex items-center
                                       p-0.5 focus:outline-none flex-shrink-0">
                            <span :class="transboundary === 'true' ? 'translate-x-5' : 'translate-x-0'"
                                  class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"></span>
                        </button>
                    </div>
                </div>

                {{-- 6. Sort --}}
                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Sort Order
                    </label>
                    <select name="sort" x-model="sort"
                            class="w-full h-11 px-4 rounded-xl border border-gray-300 bg-white
                                   text-xs font-semibold text-gray-950 focus:outline-none
                                   focus:border-gray-950 cursor-pointer">
                        <option value="name_asc">Name (A — Z)</option>
                        <option value="name_desc">Name (Z — A)</option>
                        <option value="year_asc">Inscription Year (Oldest First)</option>
                        <option value="year_desc">Inscription Year (Newest First)</option>
                    </select>
                </div>

                {{-- Hidden: carry category into main search form --}}
                <input type="hidden" name="category" :value="category">

            </form>

            {{-- Panel Footer --}}
            <div class="px-6 py-4 md:px-8 border-t border-gray-200 bg-gray-50
                        flex items-center justify-between flex-shrink-0">
                <button type="button" @click="clearAll()"
                        class="text-xs font-bold uppercase tracking-wider text-gray-600
                               hover:text-gray-950 underline underline-offset-4
                               focus:outline-none transition-colors">
                    Clear all filters
                </button>
                <div class="flex items-center gap-3">
                    <button type="button" @click="searchOpen = false"
                            class="px-5 py-2.5 rounded-full border border-gray-300 text-xs font-bold
                                   uppercase tracking-wider text-gray-950 hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="$refs.searchForm.submit()"
                            class="px-6 py-2.5 rounded-full bg-gray-950 hover:bg-gray-800 text-xs font-bold
                                   uppercase tracking-wider text-white shadow-md transition-colors
                                   flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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