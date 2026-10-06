{{-- resources/views/components/explore/FilterSidebar.blade.php --}}
@props([
    'filters'  => [],
    'result'   => [],
])

@php
    $activeCount = collect([
        request('q'),
        request('country'),
        request('region'),
        request('category'),
        request('criterion'),
        (request('year_min') && request('year_min') > ($filters['year_min'] ?? 1978)) ? 'y' : null,
        (request('year_max') && request('year_max') < ($filters['year_max'] ?? 2100)) ? 'y' : null,
        request('danger'),
        request('transboundary'),
    ])->filter()->count();
@endphp

<aside :class="{ '!block': mobileFilterOpen }" class="w-full lg:w-72 flex-shrink-0 lg:sticky lg:top-[88px] self-start hidden lg:block">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">

        {{-- Sidebar Header --}}
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
                </svg>
                <span class="text-sm font-bold text-gray-950 tracking-tight">Filters</span>
            </div>
            @if($activeCount > 0)
                <span class="text-xs font-bold text-gray-950 bg-gray-100 px-2 py-0.5 rounded-full">{{ $activeCount }} active</span>
            @endif
        </div>

        <form method="GET" action="{{ url('/search') }}" id="filter-form" class="divide-y divide-gray-100">
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            @if(request('criterion'))<input type="hidden" name="criterion" value="{{ request('criterion') }}">@endif

            {{-- 1. Search Keyword --}}
            <div class="px-5 py-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Search Keyword</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Search..."
                           class="w-full h-9 pl-8 pr-3 rounded-lg border border-gray-200 bg-gray-50 text-xs font-medium text-gray-950 placeholder-gray-400 focus:outline-none focus:border-gray-950 transition-colors">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>

            {{-- 2. Category --}}
            <div class="px-5 py-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Category</label>
                <div class="flex flex-wrap gap-2">
                    @php
                        $categories = ['' => 'All'];
                        foreach (($filters['categories'] ?? []) ?: [['value' => 'Cultural', 'label' => 'Cultural'], ['value' => 'Natural', 'label' => 'Natural'], ['value' => 'Mixed', 'label' => 'Mixed']] as $opt) {
                            $categories[$opt['value']] = $opt['label'];
                        }
                    @endphp
                    @foreach($categories as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $val, 'page' => 1]) }}"
                           class="h-7 px-3 rounded-full border text-[11px] font-bold uppercase tracking-wide transition-colors
                                  {{ strcasecmp(request('category', ''), (string) $val) === 0
                                      ? 'bg-gray-950 text-white border-gray-950'
                                      : 'bg-white text-gray-600 border-gray-200 hover:border-gray-950 hover:text-gray-950' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- 3. States Party / Country --}}
            @if(!empty($filters['countries']))
            <div class="px-5 py-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">States Party / Country</label>
                <select name="country" onchange="this.form.submit()"
                        class="w-full h-9 px-3 rounded-lg border border-gray-200 bg-gray-50 text-xs font-medium text-gray-950 focus:outline-none focus:border-gray-950 cursor-pointer">
                    <option value="">Any country</option>
                    @foreach($filters['countries'] as $c)
                        <option value="{{ $c['value'] }}" @selected(strcasecmp(request('country', ''), $c['value']) === 0)>{{ $c['label'] }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- 4. Geographic Zone / Region --}}
            @if(!empty($filters['regions']))
            <div class="px-5 py-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Geographic Zone</label>
                <div class="space-y-2">
                    @foreach($filters['regions'] as $r)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="region" value="{{ $r['value'] }}"
                                       @checked(strcasecmp(request('region', ''), $r['value']) === 0)
                                       onchange="this.form.submit()"
                                       class="w-3.5 h-3.5 accent-gray-950">
                                <span class="text-xs font-medium text-gray-700 group-hover:text-gray-950 transition-colors">{{ $r['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                    @if(request('region'))
                        <a href="{{ request()->fullUrlWithQuery(['region' => null, 'page' => 1]) }}"
                           class="text-[11px] text-gray-400 hover:text-gray-950 underline underline-offset-2 transition-colors">
                            Clear region
                        </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- 5. Inscription Era (Year Range) --}}
            <div class="px-5 py-4" x-data="{
                min: {{ (int) request('year_min', $filters['year_min'] ?? 1978) }},
                max: {{ (int) request('year_max', $filters['year_max'] ?? (int) date('Y')) }},
                absMin: {{ $filters['year_min'] ?? 1978 }},
                absMax: {{ $filters['year_max'] ?? (int) date('Y') }},
            }">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Inscription Era</label>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold tabular-nums text-gray-950" x-text="min"></span>
                    <span class="text-xs font-bold tabular-nums text-gray-950" x-text="max"></span>
                </div>
                <div class="space-y-2">
                    <input type="range" name="year_min" aria-label="Earliest inscription year"
                           :min="absMin" :max="absMax" x-model.number="min"
                           @change="if(min > max) max = min"
                           class="w-full h-1 accent-gray-950 cursor-pointer">
                    <input type="range" name="year_max" aria-label="Latest inscription year"
                           :min="absMin" :max="absMax" x-model.number="max"
                           @change="if(max < min) min = max"
                           class="w-full h-1 accent-gray-950 cursor-pointer">
                </div>
            </div>

            {{-- 6. UNESCO Criteria --}}
            @if(!empty($filters['criteria'] ?? []) || true)
            <div class="px-5 py-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">UNESCO Criteria</label>
                <div class="grid grid-cols-5 gap-1.5">
                    @php $activeCrit = trim(strtolower((string) request('criterion')), '() '); @endphp
                    @foreach(['i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix', 'x'] as $code)
                        @php $crit = '(' . $code . ')'; @endphp
                        <a href="{{ request()->fullUrlWithQuery(['criterion' => $activeCrit === $code ? null : $code, 'page' => 1]) }}"
                           class="h-8 flex items-center justify-center rounded-lg border text-[10px] font-bold tabular-nums transition-colors
                                  {{ $activeCrit === $code
                                      ? 'bg-gray-950 text-white border-gray-950'
                                      : 'bg-white text-gray-600 border-gray-200 hover:border-gray-950 hover:text-gray-950' }}">
                            {{ $crit }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 7. Toggles --}}
            <div class="px-5 py-4 space-y-3">
                {{-- In Danger --}}
                <label class="flex items-center justify-between cursor-pointer group">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gray-950"></span>
                        <span class="text-xs font-medium text-gray-700 group-hover:text-gray-950 transition-colors">Sites In Danger</span>
                    </div>
                    <input type="checkbox" name="danger" value="true" @checked(request('danger') === 'true')
                           onchange="this.form.submit()"
                           class="w-4 h-4 accent-gray-950">
                </label>
                {{-- Transboundary --}}
                <label class="flex items-center justify-between cursor-pointer group">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span class="text-xs font-medium text-gray-700 group-hover:text-gray-950 transition-colors">Transboundary</span>
                    </div>
                    <input type="checkbox" name="transboundary" value="true" @checked(request('transboundary') === 'true')
                           onchange="this.form.submit()"
                           class="w-4 h-4 accent-gray-950">
                </label>
            </div>

            {{-- Hidden sort preserved --}}
            <input type="hidden" name="sort" value="{{ request('sort', 'year_desc') }}">

            {{-- 8. Footer Actions --}}
            <div class="px-5 py-4 bg-gray-50 flex items-center gap-2">
                <a href="{{ url('/search') }}"
                   class="flex-1 h-9 rounded-full border border-gray-300 text-xs font-bold uppercase tracking-wide text-gray-950 flex items-center justify-center hover:bg-gray-100 transition-colors">
                    Reset
                </a>
                <button type="submit"
                        class="flex-1 h-9 rounded-full bg-gray-950 text-white text-xs font-bold uppercase tracking-wide flex items-center justify-center hover:bg-gray-800 transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</aside>
