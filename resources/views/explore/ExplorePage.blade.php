<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Explore World Heritage Properties — HeritageFinder</title>
    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- Tailwind CSS CDN / Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#000000",
                        "on-primary": "#ffffff",
                        "surface": "#f9f9f9",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f3f3f3",
                        "surface-container": "#eeeeee",
                        "surface-container-high": "#e8e8e8",
                        "surface-container-highest": "#e2e2e2",
                        "on-surface": "#1a1c1c",
                        "on-surface-variant": "#444748",
                        "secondary": "#5e5e5e",
                        "outline": "#747878",
                        "outline-variant": "#c4c7c7",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        sans: ["Plus Jakarta Sans", "sans-serif"]
                    }
                }
            }
        };
    </script>

    <!-- Alpine.js for lightweight client-side interactive state management -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { margin: 0; padding: 0; background-color: #f9f9f9; color: #1a1c1c; }
        ::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-surface font-sans text-on-surface antialiased selection:bg-primary selection:text-on-primary">

<div x-data="{
    // Mock Data List (6 Items as requested)
    mockItems: [
        {
            id: 'site_borobudur_1001',
            name: 'Borobudur Temple Compounds',
            category: 'Cultural',
            country: 'Indonesia',
            region: 'Asia and the Pacific',
            year: 1991,
            is_in_danger: false,
            transboundary: false,
            criteria: ['(i)', '(ii)', '(vi)'],
            connections: 42
        },
        {
            id: 'site_florence_1002',
            name: 'Historic Centre of Florence',
            category: 'Cultural',
            country: 'Italy',
            region: 'Europe and North America',
            year: 1982,
            is_in_danger: false,
            transboundary: false,
            criteria: ['(i)', '(ii)', '(iii)', '(iv)', '(vi)'],
            connections: 38
        },
        {
            id: 'site_palmyra_1003',
            name: 'Ancient City of Palmyra',
            category: 'Cultural',
            country: 'Syrian Arab Republic',
            region: 'Arab States',
            year: 1980,
            is_in_danger: true,
            transboundary: false,
            criteria: ['(i)', '(ii)', '(iv)'],
            connections: 29
        },
        {
            id: 'site_galapagos_1004',
            name: 'Galápagos Islands',
            category: 'Natural',
            country: 'Ecuador',
            region: 'Latin America and the Caribbean',
            year: 1978,
            is_in_danger: true,
            transboundary: false,
            criteria: ['(vii)', '(viii)', '(ix)', '(x)'],
            connections: 31
        },
        {
            id: 'site_machupicchu_1005',
            name: 'Historic Sanctuary of Machu Picchu',
            category: 'Mixed',
            country: 'Peru',
            region: 'Latin America and the Caribbean',
            year: 1983,
            is_in_danger: false,
            transboundary: false,
            criteria: ['(i)', '(iii)', '(vii)', '(ix)'],
            connections: 56
        },
        {
            id: 'site_serengeti_1006',
            name: 'Serengeti National Park',
            category: 'Natural',
            country: 'Tanzania',
            region: 'Africa',
            year: 1981,
            is_in_danger: false,
            transboundary: true,
            criteria: ['(vii)', '(x)'],
            connections: 18
        }
    ],

    // Dynamic Filter State
    searchQuery: '{{ request('q', '') }}',
    selectedCategory: '{{ request('category', '') }}',
    selectedCountry: '{{ request('country', '') }}',
    selectedRegion: '{{ request('region', '') }}',
    yearMin: {{ request('year_min', 1978) }},
    yearMax: {{ request('year_max', 2024) }},
    selectedCriterion: '{{ request('criterion', '') }}',
    inDangerOnly: {{ request('danger') === 'true' ? 'true' : 'false' }},
    transboundaryOnly: {{ request('transboundary') === 'true' ? 'true' : 'false' }},
    sortBy: '{{ request('sort', 'recent') }}',
    viewMode: 'grid', // 'grid' | 'table'
    currentPage: 1,
    perPage: 6,
    mobileFilterOpen: false,

    // Dynamic Computations
    get dangerTotal() {
        return this.mockItems.filter(item => item.is_in_danger).length;
    },
    get filteredItems() {
        return this.mockItems.filter(item => {
            if (this.searchQuery && !item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) && !item.country.toLowerCase().includes(this.searchQuery.toLowerCase())) return false;
            if (this.selectedCategory && item.category.toLowerCase() !== this.selectedCategory.toLowerCase()) return false;
            if (this.selectedCountry && item.country !== this.selectedCountry) return false;
            if (this.selectedRegion && item.region !== this.selectedRegion) return false;
            if (item.year && (item.year < this.yearMin || item.year > this.yearMax)) return false;
            if (this.selectedCriterion && !item.criteria.includes(this.selectedCriterion)) return false;
            if (this.inDangerOnly && !item.is_in_danger) return false;
            if (this.transboundaryOnly && !item.transboundary) return false;
            return true;
        }).sort((a, b) => {
            if (this.sortBy === 'alpha') return a.name.localeCompare(b.name);
            if (this.sortBy === 'year-asc') return a.year - b.year;
            if (this.sortBy === 'criteria') return b.criteria.length - a.criteria.length;
            return b.year - a.year; // default 'recent'
        });
    },
    get totalPropertiesCount() {
        return this.filteredItems.length;
    },
    get activeCount() {
        let count = 0;
        if (this.searchQuery) count++;
        if (this.selectedCategory) count++;
        if (this.selectedCountry) count++;
        if (this.selectedRegion) count++;
        if (this.yearMin > 1978 || this.yearMax < 2024) count++;
        if (this.selectedCriterion) count++;
        if (this.inDangerOnly) count++;
        if (this.transboundaryOnly) count++;
        return count;
    },
    get paginatedItems() {
        const start = (this.currentPage - 1) * this.perPage;
        return this.filteredItems.slice(start, start + this.perPage);
    },
    get totalPages() {
        return Math.ceil(this.totalPropertiesCount / this.perPage) || 1;
    },
    clearAllFilters() {
        this.searchQuery = '';
        this.selectedCategory = '';
        this.selectedCountry = '';
        this.selectedRegion = '';
        this.yearMin = 1978;
        this.yearMax = 2024;
        this.selectedCriterion = '';
        this.inDangerOnly = false;
        this.transboundaryOnly = false;
        this.currentPage = 1;
    },
    removeFilter(key) {
        if (key === 'danger') this.inDangerOnly = false;
        if (key === 'category') this.selectedCategory = '';
        if (key === 'region') this.selectedRegion = '';
        if (key === 'country') this.selectedCountry = '';
        if (key === 'year') { this.yearMin = 1978; this.yearMax = 2024; }
        if (key === 'criterion') this.selectedCriterion = '';
        if (key === 'q') this.searchQuery = '';
        if (key === 'transboundary') this.transboundaryOnly = false;
        this.currentPage = 1;
    }
}">

    <!-- Navbar Component -->
    <x-Navbar />

    <!-- Main Exploration Container -->
    <main class="w-full pt-[5.5rem] bg-surface min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8 lg:px-12 py-6">

            <!-- 1. Query Header & Editorial Ticker Bar -->
            <section class="w-full bg-surface-container-low border border-surface-variant rounded-2xl px-6 py-3.5 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-primary text-on-primary uppercase tracking-widest">
                            Index 02
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider text-secondary">
                            Archival Exploration · SPARQL Graph Endpoint
                        </span>
                        <span class="hidden sm:inline text-outline-variant">/</span>
                        <span class="text-xs font-semibold text-secondary">
                            Registry Release v4.8
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-secondary">
                        <span class="inline-flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Linked Data
                        </span>
                        <span class="text-outline-variant">|</span>
                        <span>Response: <strong class="text-on-surface font-semibold">42ms</strong></span>
                    </div>
                </div>
            </section>

            <!-- 2. Top Bar: Judul Utama (Dynamic Numbers) & Controls (SORT BY + Toggle View) -->
            <div class="flex flex-col gap-4 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 pb-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-secondary mb-1">
                            Index Query Results
                        </p>
                        <h1 class="text-2xl md:text-4xl font-extrabold text-on-surface tracking-tight">
                            Showing <span class="text-3xl md:text-5xl font-black text-primary tabular-nums" x-text="totalPropertiesCount">284</span> World Heritage Properties
                        </h1>
                    </div>

                    <!-- Sort and View Toolbar -->
                    <div class="flex items-center gap-3 self-start lg:self-end">
                        <!-- Dropdown SORT BY -->
                        <div class="relative inline-block text-left">
                            <div class="flex items-center gap-2 bg-surface-container-lowest border border-gray-200 px-4 py-2 rounded-full shadow-sm">
                                <span class="material-symbols-outlined text-[18px] text-secondary">sort</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-secondary">Sort by:</span>
                                <select
                                    x-model="sortBy"
                                    class="bg-transparent text-xs font-bold text-on-surface focus:outline-none cursor-pointer pr-2"
                                >
                                    <option value="recent">Recently Inscribed</option>
                                    <option value="alpha">Name (A — Z)</option>
                                    <option value="year-asc">Inscription Year (Oldest)</option>
                                    <option value="criteria">Criteria Complexity</option>
                                </select>
                            </div>
                        </div>

                        <!-- Toggle View (Grid View / Table View) -->
                        <div class="flex items-center bg-surface-container-lowest border border-gray-200 rounded-full p-1 shadow-sm">
                            <button
                                type="button"
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-primary text-on-primary' : 'text-secondary hover:text-on-surface'"
                                aria-label="Grid View"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                            >
                                <span class="material-symbols-outlined text-[18px]">grid_view</span>
                            </button>
                            <button
                                type="button"
                                @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-primary text-on-primary' : 'text-secondary hover:text-on-surface'"
                                aria-label="Table View"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                            >
                                <span class="material-symbols-outlined text-[18px]">format_list_bulleted</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Active Refinements Bar -->
                <div class="flex items-center gap-2 flex-wrap pt-1" x-show="activeCount > 0" x-transition>
                    <span class="text-xs font-bold uppercase tracking-wider text-secondary mr-1">Active Refinements:</span>
                    
                    <!-- Chip: In Danger -->
                    <template x-if="inDangerOnly">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary text-on-primary text-xs font-medium">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span>In Danger (<span x-text="dangerTotal">56</span>)</span>
                            <button @click="removeFilter('danger')" aria-label="Remove filter: In Danger" class="hover:opacity-75 transition-opacity ml-1">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Chip: Category -->
                    <template x-if="selectedCategory">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-gray-200 text-on-surface text-xs font-medium shadow-sm">
                            <span>Category: <strong x-text="selectedCategory">Cultural</strong></span>
                            <button @click="removeFilter('category')" aria-label="Remove filter: Category" class="hover:opacity-75 transition-opacity ml-1 text-secondary">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Chip: Region -->
                    <template x-if="selectedRegion">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-gray-200 text-on-surface text-xs font-medium shadow-sm">
                            <span>Region: <strong x-text="selectedRegion">Asia and the Pacific</strong></span>
                            <button @click="removeFilter('region')" aria-label="Remove filter: Region" class="hover:opacity-75 transition-opacity ml-1 text-secondary">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Chip: Country -->
                    <template x-if="selectedCountry">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-gray-200 text-on-surface text-xs font-medium shadow-sm">
                            <span>Country: <strong x-text="selectedCountry">Indonesia</strong></span>
                            <button @click="removeFilter('country')" aria-label="Remove filter: Country" class="hover:opacity-75 transition-opacity ml-1 text-secondary">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Chip: Era -->
                    <template x-if="yearMin > 1978 || yearMax < 2024">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-gray-200 text-on-surface text-xs font-medium shadow-sm">
                            <span>Era: <strong x-text="yearMin + ' — ' + yearMax">1980 — 2020</strong></span>
                            <button @click="removeFilter('year')" aria-label="Remove filter: Era" class="hover:opacity-75 transition-opacity ml-1 text-secondary">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Chip: UNESCO Criterion -->
                    <template x-if="selectedCriterion">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-gray-200 text-on-surface text-xs font-medium shadow-sm">
                            <span>Criterion: <strong x-text="selectedCriterion">(i)</strong></span>
                            <button @click="removeFilter('criterion')" aria-label="Remove filter: Criterion" class="hover:opacity-75 transition-opacity ml-1 text-secondary">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Chip: Search Keyword -->
                    <template x-if="searchQuery">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-gray-200 text-on-surface text-xs font-medium shadow-sm">
                            <span>Keyword: "<strong x-text="searchQuery"></strong>"</span>
                            <button @click="removeFilter('q')" aria-label="Remove search query" class="hover:opacity-75 transition-opacity ml-1 text-secondary">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </template>

                    <!-- Clear All Filters Button -->
                    <button
                        @click="clearAllFilters()"
                        class="text-xs font-bold text-secondary hover:text-primary underline ml-2 transition-colors cursor-pointer"
                    >
                        Clear all filters
                    </button>
                </div>
            </div>

            <!-- 4. Grid Layout Utama (2 Kolom Desktop) -->
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                <!-- Kolom Kiri: Render FilterSidebar secara sticky -->
                <x-explore.FilterSidebar :filters="$filters ?? []" :result="$result ?? []" />

                <!-- Kolom Kanan: Render List Mock Data & Pagination -->
                <div class="flex-1 w-full min-w-0">
                    
                    <!-- Responsive Mobile Filter Trigger -->
                    <div class="block lg:hidden mb-4">
                        <button
                            @click="mobileFilterOpen = !mobileFilterOpen"
                            class="w-full py-2.5 px-4 bg-primary text-on-primary rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2"
                        >
                            <span class="material-symbols-outlined text-[18px]">tune</span>
                            Filter Properties (<span x-text="activeCount">0</span> active)
                        </button>
                    </div>

                    <!-- View Mode: GRID (3 Columns) -->
                    <div
                        x-show="viewMode === 'grid'"
                        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
                    >
                        @php
                            $mockData = [
                                [
                                    'id' => 'site_borobudur_1001',
                                    'name' => 'Borobudur Temple Compounds',
                                    'category' => 'Cultural',
                                    'country' => 'Indonesia',
                                    'region' => 'Asia and the Pacific',
                                    'year' => 1991,
                                    'is_in_danger' => false,
                                    'criteria' => ['(i)', '(ii)', '(vi)'],
                                    'connections' => 42
                                ],
                                [
                                    'id' => 'site_florence_1002',
                                    'name' => 'Historic Centre of Florence',
                                    'category' => 'Cultural',
                                    'country' => 'Italy',
                                    'region' => 'Europe and North America',
                                    'year' => 1982,
                                    'is_in_danger' => false,
                                    'criteria' => ['(i)', '(ii)', '(iii)', '(iv)', '(vi)'],
                                    'connections' => 38
                                ],
                                [
                                    'id' => 'site_palmyra_1003',
                                    'name' => 'Ancient City of Palmyra',
                                    'category' => 'Cultural',
                                    'country' => 'Syrian Arab Republic',
                                    'region' => 'Arab States',
                                    'year' => 1980,
                                    'is_in_danger' => true,
                                    'criteria' => ['(i)', '(ii)', '(iv)'],
                                    'connections' => 29
                                ],
                                [
                                    'id' => 'site_galapagos_1004',
                                    'name' => 'Galápagos Islands',
                                    'category' => 'Natural',
                                    'country' => 'Ecuador',
                                    'region' => 'Latin America and the Caribbean',
                                    'year' => 1978,
                                    'is_in_danger' => true,
                                    'criteria' => ['(vii)', '(viii)', '(ix)', '(x)'],
                                    'connections' => 31
                                ],
                                [
                                    'id' => 'site_machupicchu_1005',
                                    'name' => 'Historic Sanctuary of Machu Picchu',
                                    'category' => 'Mixed',
                                    'country' => 'Peru',
                                    'region' => 'Latin America and the Caribbean',
                                    'year' => 1983,
                                    'is_in_danger' => false,
                                    'criteria' => ['(i)', '(iii)', '(vii)', '(ix)'],
                                    'connections' => 56
                                ],
                                [
                                    'id' => 'site_serengeti_1006',
                                    'name' => 'Serengeti National Park',
                                    'category' => 'Natural',
                                    'country' => 'Tanzania',
                                    'region' => 'Africa',
                                    'year' => 1981,
                                    'is_in_danger' => false,
                                    'criteria' => ['(vii)', '(x)'],
                                    'connections' => 18
                                ]
                            ];
                            $itemsToRender = !empty($result['data']) ? $result['data'] : $mockData;
                        @endphp

                        @foreach($itemsToRender as $heritage)
                            <x-explore.HeritageCard :heritage="$heritage" />
                        @endforeach
                    </div>

                    <!-- View Mode: TABLE View Alternative -->
                    <div x-show="viewMode === 'table'" x-cloak class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs text-gray-700">
                            <thead class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                                <tr>
                                    <th class="py-3 px-4">Property Name</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4">State Party</th>
                                    <th class="py-3 px-4">Year</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="item in paginatedItems" :key="item.id">
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3 px-4 font-bold text-gray-950" x-text="item.name"></td>
                                        <td class="py-3 px-4 font-medium" x-text="item.category"></td>
                                        <td class="py-3 px-4 text-gray-600" x-text="item.country"></td>
                                        <td class="py-3 px-4 tabular-nums" x-text="item.year"></td>
                                        <td class="py-3 px-4">
                                            <template x-if="item.is_in_danger">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                                    IN DANGER
                                                </span>
                                            </template>
                                            <template x-if="!item.is_in_danger">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                    Protected
                                                </span>
                                            </template>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <a :href="'/heritage/' + item.id" class="text-xs font-bold text-primary hover:underline">
                                                View detail →
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Render Pagination Component di bagian bawah grid -->
                    <x-explore.Pagination :result="$result ?? ['current_page' => 1, 'total_pages' => 48, 'total' => 284, 'from' => 1, 'to' => 6, 'per_page' => 6]" />

                </div>
            </div>

            

        </div>
    </main>
    <!-- 5. Banner Promosi Peta & 6. Footer Component -->
            <x-explore.Footer />

</div>

</body>
</html>
