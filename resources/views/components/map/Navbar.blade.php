{{-- resources/views/components/map/Navbar.blade.php --}}
@props([
    'active' => 'map'
])

<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm transition-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-[68px] gap-4">

            {{-- 1. Brand / Logo (Left) --}}
            <div class="flex-shrink-0 w-[200px] flex items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-full bg-gray-950 text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="21" x2="21" y2="21"/>
                            <line x1="6" y1="21" x2="6"  y2="10"/>
                            <line x1="10" y1="21" x2="10" y2="10"/>
                            <line x1="14" y1="21" x2="14" y2="10"/>
                            <line x1="18" y1="21" x2="18" y2="10"/>
                            <polygon points="12 3 2 10 22 10 12 3"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-sm sm:text-base tracking-wider uppercase text-gray-950 leading-tight">
                            HeritageFinder
                        </span>
                        <span class="text-[9px] sm:text-[10px] uppercase font-bold tracking-widest text-gray-400 leading-none">
                            Interactive Map
                        </span>
                    </div>
                </a>
            </div>

            {{-- 2. Center: Location / Heritage Search Bar (Connected to Alpine heritageMap) --}}
            <div class="flex-1 max-w-md lg:max-w-lg relative" @click.outside="showLocationDropdown = false">
                <div class="flex items-center h-11 bg-gray-50/90 hover:bg-gray-50 border border-gray-200 rounded-full px-4 gap-2.5 shadow-sm focus-within:bg-white focus-within:border-gray-950 focus-within:ring-1 focus-within:ring-gray-950 transition-all duration-200">
                    <button type="button" @click="performSearch()" title="Search site" class="text-gray-500 hover:text-gray-950 focus:outline-none flex-shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>

                    <input
                        type="text"
                        x-model="locationQuery"
                        @input="onLocationInput()"
                        @keydown="onLocationKeydown($event)"
                        @focus="if (locationSuggestions.length > 0) showLocationDropdown = true"
                        placeholder="Search heritage site or location..."
                        aria-label="Search heritage site or location on map"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-gray-950 placeholder-gray-400 focus:outline-none truncate"
                    />

                    {{-- Clear button --}}
                    <button
                        type="button"
                        x-show="locationQuery"
                        @click="locationQuery = ''; locationSuggestions = []; showLocationDropdown = false; onLocationInput();"
                        class="w-5 h-5 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition-colors flex-shrink-0"
                        title="Clear search"
                        style="display: none;"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                {{-- Location Search Suggestions Dropdown --}}
                <div
                    x-show="showLocationDropdown"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                    class="absolute left-0 right-0 top-13 bg-white/98 backdrop-blur-md border border-gray-200 rounded-2xl shadow-xl overflow-hidden z-50 max-h-72 overflow-y-auto"
                    style="display:none;"
                >
                    <template x-if="locationSuggestions.length > 0">
                        <ul class="py-1.5 divide-y divide-gray-100">
                            <template x-for="(item, idx) in locationSuggestions" :key="item.id || idx">
                                <li
                                    @click="selectLocation(item)"
                                    @mouseenter="selectedIndex = idx"
                                    :class="selectedIndex === idx ? 'bg-gray-100' : 'hover:bg-gray-50'"
                                    class="px-4 py-2 cursor-pointer flex items-center justify-between gap-3 transition-colors text-left"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-gray-600">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-950 truncate" x-text="item.name"></p>
                                            <p class="text-[10px] text-gray-500 truncate" x-text="item.country"></p>
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full flex-shrink-0"
                                          :class="item.category.toLowerCase() === 'natural' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (item.category.toLowerCase() === 'mixed' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-800 border border-gray-200')"
                                          x-text="item.category || 'Cultural'">
                                    </span>
                                </li>
                            </template>
                        </ul>
                    </template>

                    <template x-if="locationSuggestions.length === 0 && locationQuery.length >= 2">
                        <div class="p-5 text-center text-xs text-gray-500">
                            <p class="font-bold text-gray-800">No matching heritage sites found</p>
                            <p class="mt-1 text-[11px]">Try another heritage name or country.</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- 3. Right: Navigation Links --}}
            <nav class="hidden md:flex items-center gap-6 flex-shrink-0 w-[200px] justify-end">
                <a href="{{ url('/') }}" class="relative py-2 text-sm font-semibold text-gray-700 hover:text-gray-950 transition-colors">
                    Home
                </a>
                <a href="{{ url('/search') }}" class="relative py-2 text-sm font-semibold text-gray-700 hover:text-gray-950 transition-colors">
                    Explore
                </a>
                <a href="{{ url('/map') }}" class="relative py-2 text-sm font-bold text-gray-950 transition-colors">
                    <span>Map</span>
                    <span class="absolute bottom-0 left-0 w-full h-[2px] bg-gray-950 rounded-full"></span>
                </a>
                <a href="{{ url('/about') }}" class="relative py-2 text-sm font-semibold text-gray-700 hover:text-gray-950 transition-colors">
                    About
                </a>
            </nav>

            {{-- 4. Mobile hamburger toggle --}}
            <div class="flex md:hidden items-center" x-data="{ mobileNav: false }">
                <button
                    type="button"
                    @click="mobileNav = !mobileNav"
                    class="w-9 h-9 rounded-full border border-gray-200 bg-white flex items-center justify-center text-gray-950 hover:bg-gray-100 transition-colors"
                >
                    <svg x-show="!mobileNav" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileNav" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                {{-- Mobile Navigation Dropdown --}}
                <div
                    x-show="mobileNav"
                    @click.outside="mobileNav = false"
                    x-transition
                    class="absolute top-[68px] left-0 right-0 bg-white border-b border-gray-200 shadow-lg px-6 py-4 flex flex-col gap-3 z-50 md:hidden"
                    style="display: none;"
                >
                    <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-700 py-1.5">Home</a>
                    <a href="{{ url('/search') }}" class="text-sm font-semibold text-gray-700 py-1.5">Explore</a>
                    <a href="{{ url('/map') }}" class="text-sm font-bold text-gray-950 py-1.5 border-l-2 border-gray-950 pl-2">Map</a>
                    <a href="{{ url('/about') }}" class="text-sm font-semibold text-gray-700 py-1.5">About</a>
                </div>
            </div>

        </div>
    </div>
</header>
