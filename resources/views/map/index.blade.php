{{--
    resources/views/map/index.blade.php
    HeritageFinder — Interactive World Map
    Layout: layouts/app
    Custom Navbar: components/map/Navbar (single compact mode, location search integration)
--}}

@extends('layouts.app')

@section('title', 'Interactive Map — HeritageFinder')

{{-- Render dedicated map layout inside single heritageMap() Alpine scope --}}
@section('full-content')
<div x-data="heritageMap()" class="w-full h-screen overflow-hidden flex flex-col relative">

    {{-- Dedicated Map Navbar --}}
    <x-map.Navbar />

    {{-- Main Viewport --}}
    <main class="pt-[68px] w-full flex-1 overflow-hidden relative flex flex-col">
        <div class="relative w-full h-full flex-1 overflow-hidden select-none">

            {{-- ════════════════════════════════════════════════════════════════════
                 1. FLOATING FILTER PANEL (Under Navbar, Top Left)
                 ════════════════════════════════════════════════════════════════════ --}}
    <div class="absolute top-4 left-4 sm:left-6 z-[400] transition-all duration-300"
         :class="desktopFilterCollapsed ? 'w-auto' : 'w-[calc(100vw-32px)] sm:w-80 md:w-88'">

        {{-- Collapsed toggle pill --}}
        <div x-show="desktopFilterCollapsed" style="display:none;">
            <button
                type="button"
                @click="desktopFilterCollapsed = false"
                class="h-11 px-4 rounded-full bg-white/95 backdrop-blur-md border border-gray-200 shadow-lg text-xs font-bold uppercase tracking-wider text-gray-950 hover:bg-gray-50 flex items-center gap-2.5 transition-all"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span>Filters</span>
                <span x-show="hasActiveFilters" class="w-2 h-2 rounded-full bg-gray-950"></span>
            </button>
        </div>

        {{-- Expanded Filter Card --}}
        <div
            x-show="!desktopFilterCollapsed"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-x-3 scale-98"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            @wheel.stop
            @touchmove.stop
            class="bg-white/95 backdrop-blur-md rounded-2xl border border-gray-200 shadow-xl p-5 space-y-4 max-h-[calc(100vh-100px)] overflow-y-auto overscroll-contain select-text"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <h2 class="text-xs font-extrabold uppercase tracking-widest text-gray-950">Map Filters</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-show="hasActiveFilters"
                        @click="clearAllFilters()"
                        class="text-[11px] font-bold text-gray-500 hover:text-gray-950 underline mr-1"
                    >
                        Reset
                    </button>
                    <button
                        type="button"
                        @click="desktopFilterCollapsed = true"
                        class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition-colors"
                        title="Collapse panel"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- 1. Category Filter --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Category</label>
                <div class="grid grid-cols-2 gap-1.5">
                    <button
                        type="button"
                        @click="selectedCategory = ''; applyFilters()"
                        :class="selectedCategory === '' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2 rounded-lg text-xs transition-colors flex items-center justify-center"
                    >
                        All
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Cultural' ? '' : 'Cultural'); applyFilters()"
                        :class="selectedCategory === 'Cultural' ? 'bg-[#582E16] text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full bg-[#582E16]"></span>
                        Cultural
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Natural' ? '' : 'Natural'); applyFilters()"
                        :class="selectedCategory === 'Natural' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                        Natural
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Mixed' ? '' : 'Mixed'); applyFilters()"
                        :class="selectedCategory === 'Mixed' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full bg-[#6366F1]"></span>
                        Mixed
                    </button>
                </div>
            </div>

            {{-- 2. Country Filter --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">States Party / Country</label>
                <div class="relative">
                    <select
                        x-model="selectedCountry"
                        @change="applyFilters()"
                        class="w-full h-9 px-3 rounded-xl border border-gray-300 bg-white text-xs font-medium text-gray-950 focus:outline-none focus:border-gray-950 cursor-pointer"
                    >
                        <option value="">Any Country</option>
                        <template x-for="c in countries" :key="c">
                            <option :value="c" x-text="c"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- 3. Geographic Zone / Region --}}
            <div x-show="regions.length > 0">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Geographic Zone</label>
                <div class="relative">
                    <select
                        x-model="selectedRegion"
                        @change="applyFilters()"
                        class="w-full h-9 px-3 rounded-xl border border-gray-300 bg-white text-xs font-medium text-gray-950 focus:outline-none focus:border-gray-950 cursor-pointer"
                    >
                        <option value="">Any Geographic Zone</option>
                        <template x-for="r in regions" :key="r">
                            <option :value="r" x-text="r"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- 4. Inscription Era (Year Range) --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Inscription Era</label>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold tabular-nums text-gray-950" x-text="yearMin"></span>
                    <span class="text-xs font-bold tabular-nums text-gray-950" x-text="yearMax"></span>
                </div>
                <div class="space-y-1.5">
                    <input type="range"
                           :min="absYearMin" :max="absYearMax" x-model.number="yearMin"
                           @input="if(yearMin > yearMax) yearMax = yearMin; applyFilters()"
                           class="w-full h-1 accent-gray-950 cursor-pointer">
                    <input type="range"
                           :min="absYearMin" :max="absYearMax" x-model.number="yearMax"
                           @input="if(yearMax < yearMin) yearMin = yearMax; applyFilters()"
                           class="w-full h-1 accent-gray-950 cursor-pointer">
                </div>
            </div>

            {{-- 5. UNESCO Criteria --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">UNESCO Criteria</label>
                    <button type="button" x-show="selectedCriterion" @click="selectedCriterion = ''; applyFilters()" class="text-[10px] text-gray-400 hover:text-gray-950 underline">Clear</button>
                </div>
                <div class="grid grid-cols-5 gap-1">
                    <template x-for="code in criteriaList" :key="code">
                        <button
                            type="button"
                            @click="selectedCriterion = (selectedCriterion === code ? '' : code); applyFilters()"
                            :class="selectedCriterion === code ? 'bg-gray-950 text-white border-gray-950' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-950 hover:text-gray-950'"
                            class="h-7 rounded-md border text-[10px] font-bold tabular-nums flex items-center justify-center transition-colors"
                            x-text="'(' + code + ')'"
                        >
                        </button>
                    </template>
                </div>
            </div>

            {{-- 6. Toggles: In Danger & Transboundary --}}
            <div class="pt-2 border-t border-gray-100 space-y-2.5">
                {{-- In Danger --}}
                <div class="flex items-center justify-between p-2 rounded-xl border border-gray-200 bg-gray-50/70">
                    <div>
                        <span class="block text-xs font-bold text-gray-950">In Danger Only</span>
                        <span class="block text-[10px] text-gray-500">Threatened properties</span>
                    </div>
                    <button
                        type="button"
                        @click="inDangerOnly = !inDangerOnly; applyFilters()"
                        :class="inDangerOnly ? 'bg-red-600' : 'bg-gray-300'"
                        role="switch"
                        :aria-checked="inDangerOnly.toString()"
                        class="w-9 h-5 rounded-full transition-colors relative flex items-center p-0.5 focus:outline-none flex-shrink-0"
                    >
                        <span
                            :class="inDangerOnly ? 'translate-x-4' : 'translate-x-0'"
                            class="w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-200"
                        ></span>
                    </button>
                </div>

                {{-- Transboundary --}}
                <div class="flex items-center justify-between p-2 rounded-xl border border-gray-200 bg-gray-50/70">
                    <div>
                        <span class="block text-xs font-bold text-gray-950">Transboundary</span>
                        <span class="block text-[10px] text-gray-500">Cross-border sites</span>
                    </div>
                    <button
                        type="button"
                        @click="transboundaryOnly = !transboundaryOnly; applyFilters()"
                        :class="transboundaryOnly ? 'bg-gray-950' : 'bg-gray-300'"
                        role="switch"
                        :aria-checked="transboundaryOnly.toString()"
                        class="w-9 h-5 rounded-full transition-colors relative flex items-center p-0.5 focus:outline-none flex-shrink-0"
                    >
                        <span
                            :class="transboundaryOnly ? 'translate-x-4' : 'translate-x-0'"
                            class="w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-200"
                        ></span>
                    </button>
                </div>
            </div>

            {{-- 7. Marker Style Switcher (Compare Option A and Option B) --}}
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Pin Style</label>
                <div class="grid grid-cols-2 gap-1.5 p-1 bg-gray-100 rounded-lg">
                    <button
                        type="button"
                        @click="setMarkerStyle('optionA')"
                        :class="markerStyle === 'optionA' ? 'bg-white text-gray-950 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-950'"
                        class="py-1 px-2 text-[11px] rounded-md transition-all flex items-center justify-center gap-1"
                    >
                        <span>Pin A</span>
                        <span class="text-[9px] text-gray-400">(Teardrop)</span>
                    </button>
                    <button
                        type="button"
                        @click="setMarkerStyle('optionB')"
                        :class="markerStyle === 'optionB' ? 'bg-white text-gray-950 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-950'"
                        class="py-1 px-2 text-[11px] rounded-md transition-all flex items-center justify-center gap-1"
                    >
                        <span>Pin B</span>
                        <span class="text-[9px] text-gray-400">(Diamond)</span>
                    </button>
                </div>
            </div>

            {{-- Footer Summary --}}
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-semibold">
                <span>Showing <strong class="text-gray-950" x-text="filteredHeritages.length"></strong> sites</span>
                <span x-show="filteredHeritages.length !== heritages.length" class="text-[10px] text-gray-400">
                    of <span x-text="heritages.length"></span>
                </span>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         2. MAP CONTAINER (Full Viewport)
         ════════════════════════════════════════════════════════════════════ --}}
    <div x-ref="mapContainer" class="heritage-map-container" style="width: 100%; height: 100%; min-height: 400px;"></div>

    {{-- ════════════════════════════════════════════════════════════════════
         3. MAP LEGEND - Bottom Left
         ════════════════════════════════════════════════════════════════════ --}}
    <div class="hidden sm:flex absolute bottom-6 left-6 z-[400] bg-white/95 backdrop-blur-md rounded-2xl border border-gray-200 shadow-md px-4 py-2.5 items-center gap-4 text-xs font-semibold text-gray-700">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#582E16] border border-white inline-block"></span>
            <span>Cultural</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#059669] border border-white inline-block"></span>
            <span>Natural</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#6366F1] border border-white inline-block"></span>
            <span>Mixed</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="relative inline-block w-3 h-3">
                <span class="w-3 h-3 rounded-full bg-[#EF4444] inline-block"></span>
                <span class="absolute -top-1 -left-1 w-5 h-5 rounded-full border border-red-500 animate-ping opacity-75"></span>
            </span>
            <span>In Danger</span>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         4. LOADING OVERLAY
         ════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="isLoading"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-white/80 backdrop-blur-sm z-[700] flex flex-col items-center justify-center p-4"
        style="display:none;"
    >
        <div class="w-12 h-12 rounded-full border-3 border-gray-200 border-t-gray-950 animate-spin"></div>
        <p class="mt-4 text-xs font-bold uppercase tracking-widest text-gray-950">Loading Knowledge Graph Coordinates...</p>
        <p class="mt-1 text-xs text-gray-500">Retrieving UNESCO World Heritage markers</p>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         5. ERROR STATE OVERLAY
         ════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="hasError"
        x-transition
        class="absolute inset-0 bg-white/95 backdrop-blur-md z-[750] flex flex-col items-center justify-center p-6 text-center"
        style="display:none;"
    >
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-950 mb-4">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="10" r="8"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-950">Unable to Load Map Data</h3>
        <p class="mt-2 text-xs text-gray-500 max-w-sm" x-text="errorMessage || 'The semantic web endpoint could not be reached.'"></p>
        <button
            type="button"
            @click="loadHeritageData()"
            class="mt-6 px-6 py-2.5 rounded-full bg-gray-950 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider shadow-md transition-colors"
        >
            Retry Connection
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         6. EMPTY FILTER STATE NOTIFICATION
         ════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="!isLoading && !hasError && filteredHeritages.length === 0 && heritages.length > 0"
        x-transition
        class="absolute top-20 left-1/2 -translate-x-1/2 z-[450] bg-white/95 backdrop-blur-md border border-gray-200 rounded-2xl shadow-lg px-5 py-3 flex items-center gap-4 text-xs"
        style="display:none;"
    >
        <span class="font-medium text-gray-700">No heritage properties match the current filters.</span>
        <button
            type="button"
            @click="clearAllFilters()"
            class="font-bold uppercase tracking-wider text-gray-950 underline hover:no-underline"
        >
            Clear filters
        </button>
    </div>

    </div>
    </main>

</div>
@endsection
