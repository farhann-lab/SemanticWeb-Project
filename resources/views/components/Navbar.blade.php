{{-- resources/views/components/Navbar.blade.php --}}
@props([
    'active' => 'home'
])

{{--
    LAYOUT (desktop)
    ────────────────
    At top of page:
        Row 1 : Logo · Category pills (center) · Nav links
        Row 2 : Search bar (wide)

    After scrolling (compact):
        Row 1 : Logo · Search bar (compact, slid up into the center) · Nav links
        Row 2 : collapsed to 0 height
        Category pills slide up + fade out.

    Clicking the compact search bar returns to the top-of-page layout:
        the bar slides back down into row 2 and widens, pills slide back in.
        It folds back on Escape, click outside, or page scroll.

    The search bar is ONE absolutely-positioned element that moves with
    translateY (same duration + easing as the row-2 collapse and row-1 height
    change) so everything stays perfectly in sync.
--}}

<style>
    :root {
        --nav-ease: cubic-bezier(0.4, 0, 0.2, 1);
        --nav-dur: 350ms;
    }

    /* ── Search bar: vertical slide (wrapper) ───────────────────────── */
    .search-slot {
        transition: transform var(--nav-dur) var(--nav-ease);
        will-change: transform;
    }

    /* ── Search bar: shape morph (pill) ─────────────────────────────── */
    .search-morph {
        transition:
            max-width      var(--nav-dur) var(--nav-ease),
            height         var(--nav-dur) var(--nav-ease),
            box-shadow     300ms ease,
            outline-color  200ms ease;
        outline: 2px solid transparent;
        outline-offset: 0;
    }
    .search-morph.is-wide    { max-width: 560px; height: 3.5rem; }
    .search-morph.is-compact { max-width: 360px; height: 3rem; }
    .search-morph.is-focused {
        max-width: 560px;
        height: 3.5rem;
        outline-color: #030712;
        box-shadow: 0 10px 30px -8px rgba(3, 7, 18, 0.25);
    }

    /* ── Category pills: slide up + fade ────────────────────────────── */
    .category-row {
        transition:
            transform  var(--nav-dur) var(--nav-ease),
            opacity    280ms ease,
            visibility 0s linear 0s;
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }
    .category-row.is-hidden {
        transform: translateY(-18px);
        opacity: 0;
        pointer-events: none;
        visibility: hidden;
        transition:
            transform  var(--nav-dur) var(--nav-ease),
            opacity    220ms ease,
            visibility 0s linear var(--nav-dur);
    }

    /* ── Height morphs ──────────────────────────────────────────────── */
    .navbar-row1 { transition: height var(--nav-dur) var(--nav-ease); }
    .navbar-row2 { transition: height var(--nav-dur) var(--nav-ease); }
</style>

<div x-data="{
    isScrolled: false,
    searchOpen: false,
    mobileMenuOpen: false,
    q:        @js(request('q', '')),
    category: @js(request('category', '')),

    /* Expanded layout = top-of-page layout (pills in row 1, wide bar in row 2).
       It is shown at the top of the page, OR when the user opens the search
       while scrolled. Otherwise the navbar is compact. */
    get isExpanded() { return !this.isScrolled || this.searchOpen; },

    /* translateY of the search bar:
       expanded → row 2 (row1 80px + 8px gap)
       compact  → centered in row 1 (68px high, bar 48px) */
    get searchY() { return this.isExpanded ? 78 : 10; },

    openSearch() {
        this.searchOpen = true;
        this.$refs.searchInput.focus();
    },
    closeSearch() {
        this.searchOpen = false;
        this.$refs.searchInput.blur();
    },

    selectCategory(val) {
        this.category = val;
        this.$nextTick(() => this.$refs.categoryPillForm.submit());
    }
}"
x-init="
    const onScroll = () => {
        isScrolled = window.pageYOffset > 20;
        if (searchOpen) closeSearch();   /* scrolling the page folds it back to compact */
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
"
@keydown.escape.window="closeSearch(); mobileMenuOpen = false"
@click.outside="if (searchOpen) closeSearch()"
class="relative z-50">

    {{-- ── Main Header ─────────────────────────────────────────────────── --}}
    <header :class="(isScrolled && !searchOpen)
                ? 'bg-white/65 backdrop-blur-md shadow-sm border-b border-gray-200/80'
                : 'bg-white border-b border-gray-100'"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-350 ease-in-out">

        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10">
            <div class="relative">

                {{-- ════════════════════════════════════════════════════
                     ROW 1 — Logo · Category pills · Nav links
                     ════════════════════════════════════════════════════ --}}
                <div :class="isExpanded ? 'h-[80px]' : 'h-[68px]'"
                     class="navbar-row1 flex items-center justify-between gap-4">

                    {{-- Left: Brand (unchanged position) --}}
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

                    {{-- Center: Category pills (slide up + fade on scroll) --}}
                    <div class="hidden md:flex flex-1 justify-center items-center min-w-0">

                        {{-- Hidden form: carries q + category when a pill is clicked --}}
                        <form x-ref="categoryPillForm" action="{{ url('/search') }}" method="GET" class="hidden">
                            <input type="hidden" name="q"        :value="q">
                            <input type="hidden" name="category" :value="category">
                        </form>

                        <div :class="isExpanded ? '' : 'is-hidden'"
                             class="category-row flex items-center gap-1">

                            {{-- All Types --}}
                            <button type="button" @click="selectCategory('')"
                                    :class="category === ''
                                        ? 'border-gray-950 text-gray-950 font-bold'
                                        : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                                    class="relative h-9 px-3 lg:px-5 rounded-full border text-xs font-semibold tracking-wide
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
                                    class="relative h-9 px-3 lg:px-5 rounded-full border text-xs font-semibold tracking-wide
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
                                    class="relative h-9 px-3 lg:px-5 rounded-full border text-xs font-semibold tracking-wide
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
                                    class="relative h-9 px-3 lg:px-5 rounded-full border text-xs font-semibold tracking-wide
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
                    </div>

                    {{-- Right: Nav Links (unchanged position) --}}
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

                {{-- ════════════════════════════════════════════════════
                     ROW 2 — spacer that reserves room for the search bar
                     at top of page and collapses to 0 on scroll.
                     (desktop only; the bar itself is positioned below)
                     ════════════════════════════════════════════════════ --}}
                <div :class="isExpanded ? 'h-[72px]' : 'h-0'"
                     class="navbar-row2 hidden md:block"></div>

                {{-- ════════════════════════════════════════════════════
                     SEARCH BAR — one element, slides between row 2 and
                     the center of row 1 (desktop only).
                     left/right = 220px side column + 1rem gap, so the bar
                     is always centered between logo and nav links.
                     ════════════════════════════════════════════════════ --}}
                <form action="{{ url('/search') }}" method="GET"
                      :style="`transform: translateY(${searchY}px)`"
                      class="search-slot hidden md:flex justify-center absolute top-0
                             left-[calc(220px+1rem)] right-[calc(220px+1rem)]">
                    <input type="hidden" name="category" :value="category">

                    <div :class="[
                             searchOpen ? 'is-focused' : (isExpanded ? 'is-wide' : 'is-compact'),
                             (!searchOpen && isExpanded) ? 'hover:shadow-md shadow-sm' : ''
                         ]"
                         class="search-morph relative w-full border border-gray-200 bg-white
                                rounded-full flex items-center pl-5 pr-1.5 gap-3 cursor-text"
                         @click="openSearch()">

                        <svg class="w-4 h-4 text-gray-950 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>

                        <input x-ref="searchInput"
                               type="text" name="q" x-model="q"
                               @focus="searchOpen = true"
                               placeholder="Search heritage..."
                               autocomplete="off"
                               class="flex-1 min-w-0 bg-transparent text-sm font-semibold text-gray-950
                                      placeholder-gray-500 focus:outline-none truncate">

                        <button type="submit"
                                @mousedown.prevent
                                @click="if (!searchOpen) { $event.preventDefault(); openSearch(); }"
                                class="w-10 h-10 rounded-full bg-gray-950 text-white flex items-center
                                       justify-center hover:bg-gray-800 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- Mobile: search row (<768px) --}}
        <form action="{{ url('/search') }}" method="GET" class="md:hidden px-4 pb-3">
            <input type="hidden" name="category" :value="category">
            <div class="w-full h-11 pl-4 pr-1.5 rounded-full border border-gray-200 bg-white/95 shadow-sm
                        flex items-center gap-2.5 focus-within:shadow-md focus-within:border-gray-950
                        transition-shadow">
                <svg class="w-4 h-4 text-gray-950 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="q" x-model="q"
                       placeholder="Search heritage..."
                       autocomplete="off"
                       class="flex-1 min-w-0 bg-transparent text-xs font-semibold text-gray-950
                              placeholder-gray-500 focus:outline-none truncate">
                <button type="submit"
                        class="w-8 h-8 rounded-full bg-gray-950 text-white flex items-center
                               justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </button>
            </div>
        </form>
    </header>

    {{-- ── Mobile Drawer ───────────────────────────────────────────────── --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="fixed top-32 left-0 right-0 z-40 bg-white border-b border-gray-200
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

</div>