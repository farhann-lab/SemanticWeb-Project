{{--
    resources/views/map/index.blade.php
    HeritageFinder — Interactive World Map (MAP-1 to MAP-5)
    Layout: layouts/app (extends Navbar & global design tokens)
    Design: Monochrome Editorial Minimalism (DESIGN.md Section 4.10)
--}}

@extends('layouts.app')

@section('title', 'Interactive Map — HeritageFinder')

{{-- Override default main-class to fit full remaining viewport below fixed navbar --}}
@section('main-class', 'pt-[80px] md:pt-[68px] w-full h-screen overflow-hidden flex flex-col relative')

{{-- Hide bottom footer on the full-screen interactive map --}}
@section('hide-footer', 'true')

@section('content')
<div x-data="heritageMap()" class="relative w-full h-full flex-1 overflow-hidden select-none">

    {{-- ════════════════════════════════════════════════════════════════════
         1. FLOATING LOCATION SEARCH (MAP-5) - Top Center
         ════════════════════════════════════════════════════════════════════ --}}
    <div class="absolute top-4 left-1/2 -translate-x-1/2 z-[400] w-[92%] sm:w-[420px] md:w-[480px]">
        <div class="relative" @click.outside="showLocationDropdown = false">
            <div class="flex items-center h-12 bg-white/95 backdrop-blur-md rounded-full border border-gray-200 shadow-md px-4 gap-3 focus-within:border-gray-950 focus-within:ring-1 focus-within:ring-gray-950 transition-all duration-200">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    type="text"
                    x-model="locationQuery"
                    @input="onLocationInput()"
                    @keydown="onLocationKeydown($event)"
                    @focus="if (locationSuggestions.length > 0) showLocationDropdown = true"
                    placeholder="Search heritage location or country..."
                    aria-label="Search heritage location"
                    class="w-full bg-transparent text-xs sm:text-sm font-semibold text-gray-950 placeholder-gray-400 focus:outline-none truncate"
                />
                <button
                    type="button"
                    x-show="locationQuery"
                    @click="locationQuery = ''; locationSuggestions = []; showLocationDropdown = false;"
                    class="w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition-colors"
                    title="Clear search"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Suggestions Dropdown (MAP-5) --}}
            <div
                x-show="showLocationDropdown"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                class="absolute left-0 right-0 top-14 bg-white/98 backdrop-blur-md border border-gray-200 rounded-[20px] shadow-xl overflow-hidden z-[500] max-h-72 overflow-y-auto"
                style="display:none;"
            >
                <template x-if="locationSuggestions.length > 0">
                    <ul class="py-2 divide-y divide-gray-100">
                        <template x-for="(item, idx) in locationSuggestions" :key="item.id || idx">
                            <li
                                @click="selectLocation(item)"
                                @mouseenter="selectedIndex = idx"
                                :class="selectedIndex === idx ? 'bg-gray-100' : 'hover:bg-gray-50'"
                                class="px-4 py-2.5 cursor-pointer flex items-center justify-between gap-3 transition-colors text-left"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-gray-600">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-950 truncate" x-text="item.name"></p>
                                        <p class="text-[11px] text-gray-500 truncate" x-text="item.country"></p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full flex-shrink-0"
                                      :class="item.category.toLowerCase() === 'natural' ? 'border border-gray-950 text-gray-950' : (item.category.toLowerCase() === 'mixed' ? 'bg-gray-200 text-gray-950' : 'bg-gray-950 text-white')"
                                      x-text="item.category || 'Cultural'">
                                </span>
                            </li>
                        </template>
                    </ul>
                </template>

                <template x-if="locationSuggestions.length === 0 && locationQuery.length >= 2">
                    <div class="p-6 text-center text-xs text-gray-500">
                        <p class="font-bold text-gray-800">No matching heritage sites found</p>
                        <p class="mt-1">Try another keyword or country name.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         2. DESKTOP FLOATING FILTER PANEL (MAP-4) - Top Left
         ════════════════════════════════════════════════════════════════════ --}}
    <div class="hidden md:block absolute top-4 left-6 z-[400] transition-all duration-300"
         :class="desktopFilterCollapsed ? 'w-auto' : 'w-72 lg:w-80'">

        {{-- Collapsed toggle pill --}}
        <div x-show="desktopFilterCollapsed" style="display:none;">
            <button
                type="button"
                @click="desktopFilterCollapsed = false"
                class="h-11 px-4 rounded-full bg-white/95 backdrop-blur-md border border-gray-200 shadow-md text-xs font-bold uppercase tracking-wider text-gray-950 hover:bg-gray-50 flex items-center gap-2 transition-all"
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
            x-transition:enter-start="opacity-0 -translate-x-4 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            class="bg-white/95 backdrop-blur-md rounded-[24px] border border-gray-200 shadow-lg p-5 space-y-4"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-950"></span>
                    <h2 class="text-xs font-extrabold uppercase tracking-widest text-gray-950">Map Filters</h2>
                </div>
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        x-show="hasActiveFilters"
                        @click="clearAllFilters()"
                        class="text-[11px] font-bold text-gray-500 hover:text-gray-950 underline mr-2"
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

            {{-- Category Filter --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Category</label>
                <div class="grid grid-cols-2 gap-1.5">
                    <button
                        type="button"
                        @click="selectedCategory = ''; applyFilters()"
                        :class="selectedCategory === '' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2.5 rounded-lg text-xs transition-colors flex items-center justify-center"
                    >
                        All Types
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Cultural' ? '' : 'Cultural'); applyFilters()"
                        :class="selectedCategory === 'Cultural' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2.5 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full bg-current"></span>
                        Cultural
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Natural' ? '' : 'Natural'); applyFilters()"
                        :class="selectedCategory === 'Natural' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2.5 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full border border-current"></span>
                        Natural
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Mixed' ? '' : 'Mixed'); applyFilters()"
                        :class="selectedCategory === 'Mixed' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="h-8 px-2.5 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full bg-gradient-to-r from-current to-transparent"></span>
                        Mixed
                    </button>
                </div>
            </div>

            {{-- Country Selector --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Country</label>
                <div class="relative">
                    <select
                        x-model="selectedCountry"
                        @change="applyFilters()"
                        class="w-full h-9 px-3 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-950 focus:outline-none focus:border-gray-950 cursor-pointer"
                    >
                        <option value="">All Countries</option>
                        <template x-for="c in countries" :key="c">
                            <option :value="c" x-text="c"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- World Heritage in Danger Toggle --}}
            <div class="pt-2 border-t border-gray-100">
                <div class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 bg-gray-50/70">
                    <div>
                        <span class="block text-xs font-bold text-gray-950">In Danger Only</span>
                        <span class="block text-[10px] text-gray-500">Threatened heritage sites</span>
                    </div>
                    <button
                        type="button"
                        @click="inDangerOnly = !inDangerOnly; applyFilters()"
                        :class="inDangerOnly ? 'bg-gray-950' : 'bg-gray-300'"
                        role="switch"
                        :aria-checked="inDangerOnly.toString()"
                        class="w-10 h-6 rounded-full transition-colors relative flex items-center p-0.5 focus:outline-none flex-shrink-0"
                    >
                        <span
                            :class="inDangerOnly ? 'translate-x-4' : 'translate-x-0'"
                            class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"
                        ></span>
                    </button>
                </div>
            </div>

            {{-- Name Keyword Search --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Keyword</label>
                <div class="relative">
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input="onSearchInput()"
                        placeholder="Filter visible markers..."
                        class="w-full h-9 pl-8 pr-3 rounded-xl border border-gray-300 bg-white text-xs font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950"
                    />
                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
            </div>

            {{-- Marker Count Badge --}}
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px]">
                <span class="text-gray-500">Visible Properties:</span>
                <span class="font-bold font-mono text-gray-950" x-text="`${filteredHeritages.length} / ${heritages.length}`"></span>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         3. MOBILE FILTER BUTTON & BOTTOM SHEET (MAP-4)
         ════════════════════════════════════════════════════════════════════ --}}
    {{-- Mobile Floating Trigger Button --}}
    <div class="md:hidden absolute top-4 left-4 z-[400]">
        <button
            type="button"
            @click="mobileFilterOpen = true"
            class="h-10 px-4 rounded-full bg-white/95 backdrop-blur-md border border-gray-200 shadow-md text-xs font-bold uppercase tracking-wider text-gray-950 flex items-center gap-2"
        >
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span>Filters</span>
            <span x-show="hasActiveFilters" class="w-2 h-2 rounded-full bg-gray-950"></span>
        </button>
    </div>

    {{-- Mobile Bottom Sheet Modal --}}
    <div
        x-show="mobileFilterOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="md:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[600]"
        style="display:none;"
        @click="mobileFilterOpen = false"
    >
        <div
            @click.stop
            x-show="mobileFilterOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute bottom-0 left-0 right-0 bg-white rounded-t-[32px] p-6 max-h-[85vh] overflow-y-auto space-y-5 shadow-2xl"
        >
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-950"></span>
                    <h2 class="text-base font-bold text-gray-950">Filter Heritage Map</h2>
                </div>
                <button
                    type="button"
                    @click="mobileFilterOpen = false"
                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-700"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Categories --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Category</label>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        @click="selectedCategory = ''; applyFilters()"
                        :class="selectedCategory === '' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-100 text-gray-800'"
                        class="h-10 rounded-xl text-xs flex items-center justify-center"
                    >
                        All Types
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Cultural' ? '' : 'Cultural'); applyFilters()"
                        :class="selectedCategory === 'Cultural' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-100 text-gray-800'"
                        class="h-10 rounded-xl text-xs flex items-center justify-center"
                    >
                        Cultural
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Natural' ? '' : 'Natural'); applyFilters()"
                        :class="selectedCategory === 'Natural' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-100 text-gray-800'"
                        class="h-10 rounded-xl text-xs flex items-center justify-center"
                    >
                        Natural
                    </button>
                    <button
                        type="button"
                        @click="selectedCategory = (selectedCategory === 'Mixed' ? '' : 'Mixed'); applyFilters()"
                        :class="selectedCategory === 'Mixed' ? 'bg-gray-950 text-white font-bold' : 'bg-gray-100 text-gray-800'"
                        class="h-10 rounded-xl text-xs flex items-center justify-center"
                    >
                        Mixed
                    </button>
                </div>
            </div>

            {{-- Mobile Country --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Country</label>
                <select
                    x-model="selectedCountry"
                    @change="applyFilters()"
                    class="w-full h-11 px-4 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-950 focus:outline-none"
                >
                    <option value="">All Countries</option>
                    <template x-for="c in countries" :key="c">
                        <option :value="c" x-text="c"></option>
                    </template>
                </select>
            </div>

            {{-- Mobile In Danger Toggle --}}
            <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 bg-gray-50">
                <div>
                    <span class="block text-xs font-bold text-gray-950">In Danger Only</span>
                    <span class="block text-[11px] text-gray-500">Threatened heritage sites</span>
                </div>
                <button
                    type="button"
                    @click="inDangerOnly = !inDangerOnly; applyFilters()"
                    :class="inDangerOnly ? 'bg-gray-950' : 'bg-gray-300'"
                    class="w-11 h-6 rounded-full transition-colors relative flex items-center p-0.5"
                >
                    <span
                        :class="inDangerOnly ? 'translate-x-5' : 'translate-x-0'"
                        class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"
                    ></span>
                </button>
            </div>

            {{-- Action buttons --}}
            <div class="pt-2 flex items-center gap-3">
                <button
                    type="button"
                    @click="clearAllFilters(); mobileFilterOpen = false"
                    class="flex-1 h-11 rounded-full border border-gray-300 text-xs font-bold uppercase tracking-wider text-gray-950"
                >
                    Clear All
                </button>
                <button
                    type="button"
                    @click="mobileFilterOpen = false"
                    class="flex-1 h-11 rounded-full bg-gray-950 text-white text-xs font-bold uppercase tracking-wider"
                >
                    Apply (<span x-text="filteredHeritages.length"></span>)
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         4. MAP CONTAINER (Full Viewport)
         ════════════════════════════════════════════════════════════════════ --}}
    <div x-ref="mapContainer" class="heritage-map-container" style="width: 100%; height: 100%; min-height: 400px;"></div>

    {{-- ════════════════════════════════════════════════════════════════════
         5. MAP LEGEND - Bottom Left (Monochrome DESIGN.md Section 4.10)
         ════════════════════════════════════════════════════════════════════ --}}
    <div class="hidden sm:flex absolute bottom-6 left-6 z-[400] bg-white/95 backdrop-blur-md rounded-2xl border border-gray-200 shadow-md px-4 py-2.5 items-center gap-4 text-xs font-medium text-gray-700">
        <div class="flex items-center gap-2">
            <span class="heritage-marker-pin heritage-marker-cultural inline-block" style="width: 12px; height: 12px;"></span>
            <span>Cultural</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="heritage-marker-pin heritage-marker-natural inline-block" style="width: 12px; height: 12px;"></span>
            <span>Natural</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="heritage-marker-pin heritage-marker-mixed inline-block" style="width: 12px; height: 12px;"></span>
            <span>Mixed</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="relative inline-block" style="width: 12px; height: 12px;">
                <span class="heritage-marker-pin heritage-marker-cultural" style="width: 12px; height: 12px;"></span>
                <span class="heritage-marker-danger-pulse" style="top: -4px; left: -4px; width: 20px; height: 20px;"></span>
            </span>
            <span>In Danger</span>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         6. LOADING OVERLAY
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
         7. ERROR STATE OVERLAY
         ════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="hasError"
        x-transition
        class="absolute inset-0 bg-white/95 backdrop-blur-md z-[750] flex flex-col items-center justify-center p-6 text-center"
        style="display:none;"
    >
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-950 mb-4">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
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
         8. EMPTY FILTER STATE NOTIFICATION
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
@endsection
