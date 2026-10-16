import L from 'leaflet';
import 'leaflet.markercluster';

/**
 * Escapes HTML entities to prevent XSS.
 */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Validates URLs for image source or links.
 */
function sanitizeUrl(url) {
    if (!url) return '';
    const trimmed = String(url).trim();
    if (/^https?:\/\//i.test(trimmed)) {
        return trimmed;
    }
    return '';
}

/**
 * Alpine component for the HeritageFinder Interactive Map.
 */
export function heritageMap() {
    return {
        // Map instance & layers
        map: null,
        clusterGroup: null,
        markersById: new Map(),

        // Data State
        heritages: [],
        filteredHeritages: [],
        countries: [],
        isLoading: true,
        hasError: false,
        errorMessage: '',

        // Filters State
        selectedCategory: '',
        selectedCountry: '',
        inDangerOnly: false,
        searchQuery: '',

        // Location Search (MAP-5) State
        locationQuery: '',
        locationSuggestions: [],
        showLocationDropdown: false,
        selectedIndex: -1,

        // Mobile Filter Panel / Sheet
        mobileFilterOpen: false,
        desktopFilterCollapsed: false,

        // Debounce timer for search
        searchDebounceTimer: null,

        /**
         * Initialize component
         */
        init() {
            this.$nextTick(() => {
                this.initLeaflet();
                this.loadHeritageData();
            });
        },

        /**
         * Initialize Leaflet map and monochrome CARTO Positron tile layer
         */
        initLeaflet() {
            const container = this.$refs.mapContainer;
            if (!container) return;

            // Initialize map with default world view
            this.map = L.map(container, {
                center: [20, 0],
                zoom: 3,
                minZoom: 2,
                maxZoom: 18,
                zoomControl: false, // We'll add custom zoom control in bottom-right
                worldCopyJump: true,
            });

            // Add custom zoom control in bottom-right
            L.control.zoom({ position: 'bottomright' }).addTo(this.map);

            // Tile Layer configuration (CARTO with API key from .env, or OSM fallback)
            const cartoKey = import.meta.env.VITE_CARTO_API_KEY || '';
            let tileUrl, tileAttribution, tileClassName = '';

            if (cartoKey) {
                tileUrl = `https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png?key=${encodeURIComponent(cartoKey)}`;
                tileAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions" target="_blank" rel="noopener">CARTO</a>';
            } else {
                // Free, no API key required fallback with monochrome CSS filter
                tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                tileAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors';
                tileClassName = 'monochrome-tile-layer';
            }

            L.tileLayer(tileUrl, {
                attribution: tileAttribution,
                subdomains: cartoKey ? 'abcd' : 'abc',
                maxZoom: 19,
                className: tileClassName,
            }).addTo(this.map);

            // Initialize Marker Cluster Group with monochrome styling
            this.clusterGroup = L.markerClusterGroup({
                showCoverageOnHover: false,
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                zoomToBoundsOnClick: true,
                iconCreateFunction: (cluster) => {
                    const count = cluster.getChildCount();
                    let size = 34;
                    if (count > 100) size = 46;
                    else if (count > 20) size = 40;

                    return L.divIcon({
                        html: `<div class="heritage-cluster-inner" style="width: ${size}px; height: ${size}px;">${count}</div>`,
                        className: 'heritage-cluster',
                        iconSize: [size, size],
                        iconAnchor: [size / 2, size / 2],
                    });
                }
            });

            this.map.addLayer(this.clusterGroup);

            // Invalidate size on window resize
            window.addEventListener('resize', () => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            });
        },

        /**
         * Fetch all markers from /api/map/heritages
         */
        async loadHeritageData() {
            this.isLoading = true;
            this.hasError = false;
            this.errorMessage = '';

            try {
                const response = await fetch('/api/map/heritages');
                if (!response.ok) {
                    throw new Error(`Failed to load heritage data (status ${response.status})`);
                }

                const result = await response.json();
                const rawData = Array.isArray(result) ? result : (result.data || []);

                // Validate and filter invalid coordinates
                this.heritages = rawData.filter(item => {
                    const lat = parseFloat(item.lat);
                    const lng = parseFloat(item.lng);
                    return Number.isFinite(lat) && Number.isFinite(lng) &&
                           lat >= -90 && lat <= 90 &&
                           lng >= -180 && lng <= 180 &&
                           !(lat === 0 && lng === 0); // Ignore unlocated default (0,0) if any
                });

                // Extract unique countries sorted alphabetically
                const countrySet = new Set();
                this.heritages.forEach(h => {
                    if (h.country && typeof h.country === 'string') {
                        // In case countries are pipe-separated or comma-separated
                        h.country.split(/[,|]/).forEach(c => {
                            const trimmed = c.trim();
                            if (trimmed) countrySet.add(trimmed);
                        });
                    }
                });
                this.countries = Array.from(countrySet).sort((a, b) => a.localeCompare(b));

                // Render markers with current filters
                this.applyFilters();

                // Process initial URL parameters (lat, lng, id)
                this.handleUrlParameters();

            } catch (err) {
                console.error('Map data load error:', err);
                this.hasError = true;
                this.errorMessage = err.message || 'Unable to connect to heritage service.';
            } finally {
                this.isLoading = false;
                if (this.map) {
                    setTimeout(() => this.map.invalidateSize(), 100);
                }
            }
        },

        /**
         * Apply client-side filters (Category, Country, In Danger, Keyword)
         */
        applyFilters() {
            const cat = this.selectedCategory.toLowerCase();
            const country = this.selectedCountry.toLowerCase();
            const q = this.searchQuery.trim().toLowerCase();
            const danger = this.inDangerOnly;

            this.filteredHeritages = this.heritages.filter(item => {
                // Category match
                if (cat && (item.category || '').toLowerCase() !== cat) {
                    return false;
                }

                // Country match
                if (country) {
                    const itemCountry = (item.country || '').toLowerCase();
                    if (!itemCountry.includes(country)) {
                        return false;
                    }
                }

                // Danger match
                if (danger && !item.in_danger) {
                    return false;
                }

                // Keyword match
                if (q) {
                    const name = (item.name || '').toLowerCase();
                    const cty = (item.country || '').toLowerCase();
                    if (!name.includes(q) && !cty.includes(q)) {
                        return false;
                    }
                }

                return true;
            });

            this.renderMarkers();
        },

        /**
         * Render filtered markers onto Leaflet map
         */
        renderMarkers() {
            if (!this.clusterGroup) return;

            // Clear existing markers
            this.clusterGroup.clearLayers();
            this.markersById.clear();

            const markers = [];

            this.filteredHeritages.forEach(item => {
                const lat = parseFloat(item.lat);
                const lng = parseFloat(item.lng);
                const cat = (item.category || '').toLowerCase();
                const inDanger = !!item.in_danger;

                // Determine marker class based on category
                let markerClass = 'heritage-marker-cultural';
                if (cat === 'natural') {
                    markerClass = 'heritage-marker-natural';
                } else if (cat === 'mixed') {
                    markerClass = 'heritage-marker-mixed';
                }

                // Danger pulse ring
                const dangerPulseHtml = inDanger ? '<div class="heritage-marker-danger-pulse"></div>' : '';

                const customIcon = L.divIcon({
                    className: 'custom-heritage-div-icon',
                    html: `
                        <div class="heritage-marker-pin ${markerClass}" title="${escapeHtml(item.name)}">
                            ${dangerPulseHtml}
                        </div>
                    `,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7],
                    popupAnchor: [0, -10],
                });

                const marker = L.marker([lat, lng], { icon: customIcon });

                // Bind popup
                const popupContent = this.createPopupHtml(item);
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    minWidth: 260,
                    className: 'heritage-custom-popup',
                });

                // Index marker by ID
                if (item.id) {
                    this.markersById.set(item.id, marker);
                }

                markers.push(marker);
            });

            this.clusterGroup.addLayers(markers);
        },

        /**
         * Build popup HTML string compliant with DESIGN.md Section 4.10
         */
        createPopupHtml(item) {
            const name = escapeHtml(item.name || 'Untitled Heritage');
            const country = escapeHtml(item.country || 'Global');
            const category = escapeHtml(item.category || 'Cultural');
            const catLower = (item.category || '').toLowerCase();
            const inDanger = !!item.in_danger;
            const year = item.year ? escapeHtml(item.year) : null;
            const detailUrl = `/heritage/${encodeURIComponent(item.id || '')}`;
            const rawImage = item.image ? sanitizeUrl(item.image) : '';

            // Category badge markup
            let badgeClass = 'bg-gray-950 text-white';
            if (catLower === 'natural') {
                badgeClass = 'bg-white text-gray-950 border border-gray-950';
            } else if (catLower === 'mixed') {
                badgeClass = 'bg-gray-200 text-gray-950';
            }

            // Image or clean placeholder
            const imageHtml = rawImage
                ? `<div class="w-full h-32 bg-gray-100 overflow-hidden relative">
                     <img src="${rawImage}" alt="${name}" loading="lazy" referrerpolicy="no-referrer"
                          class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                          onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gray-100 flex items-center justify-center text-gray-400\\'><svg class=\\'w-8 h-8\\' viewBox=\\'0 0 24 24\\' fill=\\'none\\' stroke=\\'currentColor\\' stroke-width=\\'1.5\\'><line x1=\\'3\\' y1=\\'21\\' x2=\\'21\\' y2=\\'21\\'/><line x1=\\'6\\' y1=\\'21\\' x2=\\'6\\' y2=\\'10\\'/><line x1=\\'10\\' y1=\\'21\\' x2=\\'10\\' y2=\\'10\\'/><line x1=\\'14\\' y1=\\'21\\' x2=\\'14\\' y2=\\'10\\'/><line x1=\\'18\\' y1=\\'21\\' x2=\\'18\\' y2=\\'10\\'/><polygon points=\\'12 3 2 10 22 10 12 3\\'/></svg></div>';" />
                   </div>`
                : `<div class="w-full h-24 bg-gray-100 flex items-center justify-center text-gray-400">
                     <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                       <line x1="3" y1="21" x2="21" y2="21"/>
                       <line x1="6" y1="21" x2="6" y2="10"/>
                       <line x1="10" y1="21" x2="10" y2="10"/>
                       <line x1="14" y1="21" x2="14" y2="10"/>
                       <line x1="18" y1="21" x2="18" y2="10"/>
                       <polygon points="12 3 2 10 22 10 12 3"/>
                     </svg>
                   </div>`;

            // Danger pill
            const dangerBadge = inDanger
                ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">
                     <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span>
                     In Danger
                   </span>`
                : '';

            return `
                <div class="flex flex-col font-sans text-gray-950 overflow-hidden rounded-[20px]">
                    ${imageHtml}
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${badgeClass}">
                                ${category}
                            </span>
                            ${dangerBadge}
                            ${year ? `<span class="text-[10px] font-semibold text-gray-500 tabular-nums">Inscribed ${year}</span>` : ''}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold leading-tight text-gray-950 line-clamp-2">
                                ${name}
                            </h3>
                            <p class="text-xs font-medium text-gray-500 mt-0.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                  <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span class="truncate">${country}</span>
                            </p>
                        </div>
                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-[10px] font-mono text-gray-400">wh:${escapeHtml(item.id || '')}</span>
                            <a href="${detailUrl}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-gray-950 hover:bg-gray-800 text-white text-xs font-semibold transition-colors">
                                <span>View Details</span>
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="7" y1="17" x2="17" y2="7"/>
                                    <polyline points="7 7 17 7 17 17"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        },

        /**
         * Handle URL query parameters (?lat=&lng=&id=)
         */
        handleUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            const latParam = urlParams.get('lat');
            const lngParam = urlParams.get('lng');
            const idParam = urlParams.get('id');

            const hasLat = latParam !== null && latParam.trim() !== '';
            const hasLng = lngParam !== null && lngParam.trim() !== '';
            const lat = hasLat ? parseFloat(latParam) : NaN;
            const lng = hasLng ? parseFloat(lngParam) : NaN;
            const hasValidCoords = Number.isFinite(lat) && Number.isFinite(lng) &&
                                  lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;

            if (idParam) {
                const targetHeritage = this.heritages.find(h => h.id === idParam);
                if (targetHeritage) {
                    const targetLat = parseFloat(targetHeritage.lat);
                    const targetLng = parseFloat(targetHeritage.lng);
                    this.flyToAndOpenMarker(targetHeritage.id, targetLat, targetLng, 11);
                    return;
                }
            }

            if (hasValidCoords) {
                if (this.map) {
                    this.map.flyTo([lat, lng], 10, { duration: 1.5 });
                }
            }
        },

        /**
         * Fly to a coordinate and open the marker's popup
         */
        flyToAndOpenMarker(id, lat, lng, zoom = 12) {
            if (!this.map) return;

            this.map.flyTo([lat, lng], zoom, {
                duration: 1.2,
                easeLinearity: 0.25
            });

            // After fly animation, zoom in cluster and open popup
            setTimeout(() => {
                const marker = this.markersById.get(id);
                if (marker && this.clusterGroup) {
                    // Zoom into cluster if clustered
                    this.clusterGroup.zoomToShowLayer(marker, () => {
                        marker.openPopup();
                    });
                }
            }, 1250);
        },

        /**
         * Keyword debounce handler
         */
        onSearchInput() {
            clearTimeout(this.searchDebounceTimer);
            this.searchDebounceTimer = setTimeout(() => {
                this.applyFilters();
            }, 250);
        },

        /**
         * Location search (MAP-5) input handler
         */
        onLocationInput() {
            const q = this.locationQuery.trim().toLowerCase();
            this.selectedIndex = -1;

            if (q.length < 2) {
                this.locationSuggestions = [];
                this.showLocationDropdown = false;
                return;
            }

            // Search through loaded heritage dataset by name & country
            const matches = [];
            for (const h of this.heritages) {
                const name = (h.name || '').toLowerCase();
                const country = (h.country || '').toLowerCase();

                if (name.includes(q) || country.includes(q)) {
                    matches.push(h);
                    if (matches.length >= 8) break; // Top 8 suggestions
                }
            }

            this.locationSuggestions = matches;
            this.showLocationDropdown = true;
        },

        /**
         * Select a location suggestion (MAP-5)
         */
        selectLocation(item) {
            this.locationQuery = item.name;
            this.showLocationDropdown = false;
            this.selectedIndex = -1;

            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lng);
            this.flyToAndOpenMarker(item.id, lat, lng, 12);
        },

        /**
         * Keyboard navigation for location search suggestions
         */
        onLocationKeydown(event) {
            if (!this.showLocationDropdown || this.locationSuggestions.length === 0) {
                if (event.key === 'Enter') {
                    this.onLocationInput();
                }
                return;
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                this.selectedIndex = (this.selectedIndex + 1) % this.locationSuggestions.length;
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                this.selectedIndex = this.selectedIndex <= 0
                    ? this.locationSuggestions.length - 1
                    : this.selectedIndex - 1;
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (this.selectedIndex >= 0 && this.selectedIndex < this.locationSuggestions.length) {
                    this.selectLocation(this.locationSuggestions[this.selectedIndex]);
                } else if (this.locationSuggestions.length > 0) {
                    this.selectLocation(this.locationSuggestions[0]);
                }
            } else if (event.key === 'Escape') {
                this.showLocationDropdown = false;
            }
        },

        /**
         * Clear all active filters
         */
        clearAllFilters() {
            this.selectedCategory = '';
            this.selectedCountry = '';
            this.inDangerOnly = false;
            this.searchQuery = '';
            this.locationQuery = '';
            this.showLocationDropdown = false;
            this.applyFilters();

            // Reset map view to world overview
            if (this.map) {
                this.map.flyTo([20, 0], 3, { duration: 1.0 });
            }
        },

        /**
         * Check if any filter is active
         */
        get hasActiveFilters() {
            return !!(this.selectedCategory || this.selectedCountry || this.inDangerOnly || this.searchQuery);
        }
    };
}

/**
 * Alpine component for the Heritage Detail monochrome mini map (DET-3, DESIGN.md Section 4.8).
 */
export function heritageMiniMap(config = {}) {
    return {
        lat: config.lat !== undefined && config.lat !== null ? parseFloat(config.lat) : null,
        lng: config.lng !== undefined && config.lng !== null ? parseFloat(config.lng) : null,
        name: config.name || '',
        category: (config.category || 'Cultural').toLowerCase(),
        inDanger: !!config.inDanger,
        map: null,
        marker: null,

        get hasValidCoordinates() {
            return this.lat !== null &&
                   this.lng !== null &&
                   Number.isFinite(this.lat) &&
                   Number.isFinite(this.lng) &&
                   this.lat >= -90 && this.lat <= 90 &&
                   this.lng >= -180 && this.lng <= 180;
        },

        init() {
            if (!this.hasValidCoordinates) {
                return;
            }

            this.$nextTick(() => {
                this.initMap();
            });
        },

        initMap() {
            const container = this.$refs.miniMapContainer;
            if (!container || this.map) return;

            // Initialize compact Leaflet instance
            this.map = L.map(container, {
                center: [this.lat, this.lng],
                zoom: 11,
                minZoom: 3,
                maxZoom: 17,
                scrollWheelZoom: false, // Prevent wheel capture while scrolling the detail page
                zoomControl: true,
                attributionControl: true,
            });

            // Reposition zoom control to top-right
            if (this.map.zoomControl) {
                this.map.zoomControl.setPosition('topright');
            }

            // Tile Layer configuration (CARTO Positron or monochrome OSM fallback)
            const cartoKey = import.meta.env.VITE_CARTO_API_KEY || '';
            let tileUrl, tileAttribution, tileClassName = '';

            if (cartoKey) {
                tileUrl = `https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png?key=${encodeURIComponent(cartoKey)}`;
                tileAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions" target="_blank" rel="noopener">CARTO</a>';
            } else {
                tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                tileAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OSM</a>';
                tileClassName = 'monochrome-tile-layer';
            }

            L.tileLayer(tileUrl, {
                attribution: tileAttribution,
                subdomains: cartoKey ? 'abcd' : 'abc',
                maxZoom: 18,
                className: tileClassName,
            }).addTo(this.map);

            // Determine marker class based on category
            let markerClass = 'heritage-marker-cultural';
            if (this.category === 'natural') {
                markerClass = 'heritage-marker-natural';
            } else if (this.category === 'mixed') {
                markerClass = 'heritage-marker-mixed';
            }

            const dangerPulseHtml = this.inDanger ? '<div class="heritage-marker-danger-pulse"></div>' : '';

            const customIcon = L.divIcon({
                className: 'custom-heritage-div-icon',
                html: `
                    <div class="heritage-marker-pin ${markerClass}" title="${escapeHtml(this.name)}">
                        ${dangerPulseHtml}
                    </div>
                `,
                iconSize: [16, 16],
                iconAnchor: [8, 8],
            });

            this.marker = L.marker([this.lat, this.lng], {
                icon: customIcon,
                alt: this.name,
                keyboard: true,
            }).addTo(this.map);

            // Responsive size invalidation
            window.addEventListener('resize', () => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            });

            // Slight delay invalidation to ensure container geometry is stabilized
            setTimeout(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            }, 200);
        },

        destroy() {
            if (this.map) {
                this.map.remove();
                this.map = null;
            }
        }
    };
}

