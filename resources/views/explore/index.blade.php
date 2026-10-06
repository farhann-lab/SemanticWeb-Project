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

@php
    // ---- All state comes from the server (SearchController -> SearchService -> Fuseki) ----
    $result  = $result  ?? ['data' => [], 'current_page' => 1, 'per_page' => 12, 'total' => 0, 'total_pages' => 1, 'from' => 0, 'to' => 0, 'took_ms' => 0];
    $filters = $filters ?? [];
    $error   = $error   ?? null;

    $yearMinBound = $filters['year_min'] ?? 1978;
    $yearMaxBound = $filters['year_max'] ?? (int) date('Y');

    $labelOf = function (string $group, ?string $value) use ($filters) {
        foreach ($filters[$group] ?? [] as $opt) {
            if (strcasecmp($opt['value'], (string) $value) === 0) return $opt['label'];
        }
        return $value;
    };

    // Active refinements -> removable chips (each chip is a link that drops that one parameter)
    $chips = [];
    if (request('q'))             $chips[] = ['label' => 'Keyword',   'text' => '"' . request('q') . '"', 'drop' => ['q']];
    if (request('category'))      $chips[] = ['label' => 'Category',  'text' => $labelOf('categories', request('category')), 'drop' => ['category']];
    if (request('region'))        $chips[] = ['label' => 'Region',    'text' => $labelOf('regions', request('region')), 'drop' => ['region']];
    if (request('country'))       $chips[] = ['label' => 'Country',   'text' => $labelOf('countries', request('country')), 'drop' => ['country']];
    if (request('criterion'))     $chips[] = ['label' => 'Criterion', 'text' => '(' . trim(request('criterion'), '() ') . ')', 'drop' => ['criterion']];
    if (request('year_min') > $yearMinBound || (request('year_max') && request('year_max') < $yearMaxBound))
                                  $chips[] = ['label' => 'Era', 'text' => (request('year_min') ?: $yearMinBound) . ' — ' . (request('year_max') ?: $yearMaxBound), 'drop' => ['year_min', 'year_max']];
    if (request('danger') === 'true')        $chips[] = ['label' => 'Status', 'text' => 'In Danger', 'drop' => ['danger'], 'danger' => true];
    if (request('transboundary') === 'true') $chips[] = ['label' => 'Scope',  'text' => 'Transboundary', 'drop' => ['transboundary']];

    $dropUrl = fn (array $keys) => request()->fullUrlWithQuery(array_merge(array_fill_keys($keys, null), ['page' => null]));

    $sortOptions = [
        'year_desc' => 'Recently Inscribed',
        'name_asc'  => 'Name (A — Z)',
        'name_desc' => 'Name (Z — A)',
        'year_asc'  => 'Inscription Year (Oldest)',
    ];
    $currentSort = request('sort', 'year_desc');
@endphp

<div x-data="{ viewMode: 'grid', mobileFilterOpen: false }">

    <!-- Navbar Component -->
    <x-Navbar />

    <main class="w-full pt-[5.5rem] bg-surface min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8 lg:px-12 py-6">

            <!-- 1. Query ticker (response time is measured on the server) -->
            <section class="w-full bg-surface-container-low border border-gray-200 rounded-2xl px-6 py-3.5 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-primary text-on-primary uppercase tracking-widest">Index 02</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-secondary">Archival Exploration · SPARQL Graph Endpoint</span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-secondary">
                        @if($error)
                            <span class="inline-flex items-center gap-1.5 font-medium text-error">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Graph offline
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Linked Data
                            </span>
                            <span class="text-outline-variant">|</span>
                            <span>Response: <strong class="text-on-surface font-semibold">{{ $result['took_ms'] }}ms</strong></span>
                        @endif
                    </div>
                </div>
            </section>

            @if($error)
                <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 text-red-800 px-5 py-4 text-sm">
                    <strong class="font-bold">{{ $error }}</strong>
                    Make sure Apache Jena Fuseki is running and <code class="font-mono text-xs">FUSEKI_ENDPOINT</code> in <code class="font-mono text-xs">.env</code> points to your dataset.
                </div>
            @endif

            <!-- 2. Title + sort + view toggle -->
            <div class="flex flex-col gap-4 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 pb-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-secondary mb-1">Index Query Results</p>
                        <h1 class="text-2xl md:text-4xl font-extrabold text-on-surface tracking-tight">
                            Showing <span class="text-3xl md:text-5xl font-black text-primary tabular-nums">{{ number_format($result['total']) }}</span>
                            World Heritage {{ $result['total'] === 1 ? 'Property' : 'Properties' }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3 self-start lg:self-end">
                        <!-- Sort: navigates with ?sort=… (server-side ordering) -->
                        <div class="flex items-center gap-2 bg-surface-container-lowest border border-gray-200 px-4 py-2 rounded-full shadow-sm">
                            <span class="material-symbols-outlined text-[18px] text-secondary">sort</span>
                            <label for="sort-select" class="text-xs font-bold uppercase tracking-wider text-secondary">Sort by:</label>
                            <select id="sort-select"
                                    onchange="window.location.href = this.value"
                                    class="bg-transparent text-xs font-bold text-on-surface focus:outline-none cursor-pointer pr-2">
                                @foreach($sortOptions as $value => $label)
                                    <option value="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => null]) }}" @selected($currentSort === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center bg-surface-container-lowest border border-gray-200 rounded-full p-1 shadow-sm">
                            <button type="button" @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-primary text-on-primary' : 'text-secondary hover:text-on-surface'"
                                    aria-label="Grid View" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[18px]">grid_view</span>
                            </button>
                            <button type="button" @click="viewMode = 'table'"
                                    :class="viewMode === 'table' ? 'bg-primary text-on-primary' : 'text-secondary hover:text-on-surface'"
                                    aria-label="Table View" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[18px]">format_list_bulleted</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Active refinements -->
                @if(count($chips))
                    <div class="flex items-center gap-2 flex-wrap pt-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-secondary mr-1">Active Refinements:</span>
                        @foreach($chips as $chip)
                            <a href="{{ $dropUrl($chip['drop']) }}"
                               aria-label="Remove filter: {{ $chip['label'] }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium shadow-sm transition-opacity hover:opacity-75
                                      {{ !empty($chip['danger']) ? 'bg-primary text-on-primary' : 'bg-surface-container-lowest border border-gray-200 text-on-surface' }}">
                                @if(!empty($chip['danger']))<span class="w-2 h-2 rounded-full bg-red-500"></span>@endif
                                <span>{{ $chip['label'] }}: <strong>{{ $chip['text'] }}</strong></span>
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </a>
                        @endforeach
                        <a href="{{ url('/search') }}" class="text-xs font-bold text-secondary hover:text-primary underline ml-2 transition-colors">Clear all filters</a>
                    </div>
                @endif
            </div>

            <!-- 4. Sidebar + results -->
            <div class="flex flex-col lg:flex-row gap-8 items-start">

                <x-explore.FilterSidebar :filters="$filters" :result="$result" />

                <div class="flex-1 w-full min-w-0">

                    <!-- Mobile filter trigger -->
                    <div class="block lg:hidden mb-4">
                        <button @click="mobileFilterOpen = !mobileFilterOpen"
                                class="w-full py-2.5 px-4 bg-primary text-on-primary rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">tune</span>
                            Filter Properties ({{ count($chips) }} active)
                        </button>
                    </div>

                    @if(empty($result['data']))
                        <div class="bg-white border border-gray-200 rounded-2xl px-8 py-16 text-center">
                            <p class="text-lg font-bold text-gray-950">
                                {{ $error ? 'No data available right now' : 'No heritage properties match these filters' }}
                            </p>
                            @unless($error)
                                <p class="text-sm text-gray-500 mt-2">Try removing a refinement or searching for a different keyword.</p>
                                <a href="{{ url('/search') }}" class="inline-flex mt-6 h-10 px-5 rounded-full bg-gray-950 text-white text-xs font-bold uppercase tracking-wide items-center hover:bg-gray-800 transition-colors">Reset filters</a>
                            @endunless
                        </div>
                    @else
                        <!-- GRID -->
                        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($result['data'] as $heritage)
                                <x-explore.HeritageCard :heritage="$heritage" />
                            @endforeach
                        </div>

                        <!-- TABLE -->
                        <div x-show="viewMode === 'table'" x-cloak class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
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
                                    @foreach($result['data'] as $item)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="py-3 px-4 font-bold text-gray-950">{{ $item['name'] }}</td>
                                            <td class="py-3 px-4 font-medium">{{ $item['category'] ?? '' }}</td>
                                            <td class="py-3 px-4 text-gray-600">{{ $item['country'] ?? '' }}</td>
                                            <td class="py-3 px-4 tabular-nums">{{ $item['year'] ?? '—' }}</td>
                                            <td class="py-3 px-4">
                                                @if(!empty($item['is_in_danger']))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">IN DANGER</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Protected</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <a href="{{ url('/heritage/' . $item['id']) }}" class="text-xs font-bold text-primary hover:underline">View detail →</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-explore.Pagination :result="$result" />
                    @endif
                </div>
            </div>

            {{-- Map promo --}}
            <section class="mt-10 bg-white border border-gray-200 rounded-[32px] p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gray-950"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400">Cartographic & Graph Discovery</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-950 tracking-tight leading-snug">Need multi-dimensional spatial projection?</h2>
                    <p class="text-sm text-gray-500 max-w-lg leading-relaxed">
                        Switch to the interactive geographic projection to visualize transboundary migration corridors and climate risk heatmaps across all sites.
                    </p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="{{ url('/statistics') }}" class="h-10 px-5 rounded-full border border-gray-300 text-xs font-bold uppercase tracking-wide text-gray-950 flex items-center gap-2 hover:bg-gray-100 transition-colors">Statistics</a>
                    <a href="{{ url('/map') }}" class="h-10 px-5 rounded-full bg-gray-950 text-white text-xs font-bold uppercase tracking-wide flex items-center gap-2 hover:bg-gray-800 transition-colors">Launch Global Map</a>
                </div>
            </section>

        </div>
    </main>

    <x-explore.Footer />

</div>

</body>
</html>
