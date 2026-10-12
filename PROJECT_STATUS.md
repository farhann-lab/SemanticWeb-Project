# HeritageFinder — Project Implementation Status & Codebase Audit

**Document:** `PROJECT_STATUS.md`  
**Audit Date:** 2026-10-12  
**Auditor:** Senior Software Engineer & Codebase Auditor  
**Baseline Documents:** `PRD.md` (v0.3 Draft), `ARCHITECTURE.md`, `DESIGN.md` (v0.1 Draft), `README.md`  

---

## 1. Project Overview

- **Project Name:** HeritageFinder — Semantic Explorer for World Heritage Sites
- **Purpose:** A semantic web exploration platform for UNESCO World Heritage Sites. The application models UNESCO data as RDF triples stored in Apache Jena Fuseki, exposes structured SPARQL queries through Laravel application services, and provides an editorial monochrome user interface for searching, filtering, geographic mapping, and relationship discovery.
- **Verified Technology Stack:**
  - **Backend:** Laravel 12.0 (`composer.json`), PHP 8.2+ (`php: ^8.2`), SQLite (local transactional database configured), Laravel HTTP Client / Guzzle.
  - **Semantic Layer:** Apache Jena Fuseki (SPARQL 1.1 endpoint configured at `http://localhost:3030/websemantik/query`), Turtle RDF dataset (`TEMPAT-GONG-DIDUNYA.ttl`, 16.3 MB, 179,987 lines) with custom ontology namespace `wh: <https://example.org/heritage/ontology/>`.
  - **Frontend:** Laravel Blade, Tailwind CSS v4 (`@tailwindcss/vite: ^4.0.0`), Alpine.js v3.17.4, Vite v7.3.6, GSAP v3.15.0 + ScrollTrigger, `@studio-freight/lenis` v1.0.42.
- **Architectural Flow:**
  $$\text{Browser} \longrightarrow \text{Laravel (Controllers/API)} \longrightarrow \text{Application Services} \longrightarrow \text{SPARQL Service} \longrightarrow \text{Apache Jena Fuseki} \longrightarrow \text{RDF Dataset}$$
  *Rule enforced: Browser never connects directly to Apache Jena Fuseki.*
- **Current Development Phase:** Phase 1 (MVP Implementation — approximately 55% functionally complete across core workflows).

---

## 2. Executive Summary

HeritageFinder has established a solid architectural foundation in its core backend service layer and primary Explore/Search user interface. The RDF dataset (`TEMPAT-GONG-DIDUNYA.ttl`) is comprehensive, ontology definitions are complete, and the Laravel service layer (`SparqlService`, `SearchService`, `FilterService`, `MapService`, `StatisticsService`, `HeritageService`) successfully translates user input into parameterized SPARQL templates without exposing raw SPARQL execution to the browser.

However, several critical MVP milestones specified in `PRD.md` remain either partially implemented, routed to placeholder templates, or entirely unbuilt:
1. **Interactive Map:** The `/map` route currently renders a parallax animation demo (`resources/views/parallax.blade.php`), with no Leaflet integration or interactive map container on the frontend, despite the backend `MapService` and `/api/map/heritages` being implemented.
2. **Discover:** The `/discover` route simply redirects to `/explore`. The curved card carousel interface specified in `PRD.md` (`DSC-1` through `DSC-7`) does not exist.
3. **Ask Heritage Chatbot:** Completely missing from both frontend and backend (`BOT-1` through `BOT-9`). No floating button, no controller, no service, and no LLM intent handling exist.
4. **Heritage Detail Page:** `resources/views/heritage/show.blade.php` presents core site attributes, components, and related sites, but lacks geographic coordinates, a "View on Map" action, RDFa / Open Graph metadata, and shared card component parity.
5. **Explore View Architecture:** `resources/views/explore/index.blade.php` does not extend `layouts/app.blade.php`; it pulls Tailwind CSS and Alpine.js from CDN script tags rather than using Vite compiled assets.
6. **Fuseki Availability:** Apache Jena Fuseki on port 3030 was offline during testing, causing endpoint timeouts during test suite runs.

---

## 3. Verified Project Structure

```text
PROJECT-WebSemantik/
├── .env.example                          # Environment config (FUSEKI_ENDPOINT, FUSEKI_TIMEOUT)
├── ARCHITECTURE.md                       # Technical architecture & constraints specification
├── DESIGN.md                             # Style guide, design tokens, motion rules
├── PRD.md                                # Product Requirements Document (v0.3)
├── README.md                             # Default Laravel readme
├── TEMPAT-GONG-DIDUNYA.ttl               # Complete RDF Turtle dataset (16.3 MB, 179,987 lines)
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── FilterController.php      # JSON response for filter options
│   │   │   ├── HeritageController.php    # Heritage detail view controller
│   │   │   ├── SearchController.php      # Main Explore & Search view controller
│   │   │   ├── StatisticsController.php  # Statistics view controller
│   │   │   └── Api/
│   │   │       └── HeritageApiController.php # JSON API boundary (/api/heritages, /api/map, etc.)
│   │   └── Requests/
│   │       └── SearchRequest.php         # Strict validation whitelist for search/filter inputs
│   └── Services/
│       ├── SparqlService.php             # Low-level HTTP client to Fuseki endpoint with error guards
│       ├── HeritageService.php           # Site details, images, components, related & recommendations
│       ├── SearchService.php             # Search query builder, count, pagination & batch enrichment
│       ├── FilterService.php             # Cached filter options (countries, regions, criteria, year bounds)
│       ├── MapService.php                # Cached marker geographic projections
│       └── StatisticsService.php         # Overview stats (sites, countries, regions, categories)
├── config/
│   └── services.php                      # Fuseki configuration (endpoint, timeout)
├── resources/
│   ├── css/
│   │   ├── app.css                       # Tailwind v4 entry with custom theme tokens
│   │   └── global.css                    # DESIGN.md CSS custom properties (colors, radius, shadows, motion)
│   ├── js/
│   │   ├── app.js                        # GSAP ScrollTrigger + Lenis smooth scroll setup
│   │   └── bootstrap.js                  # Axios setup
│   ├── sparql/                           # 16 parameterized SPARQL query templates (.rq)
│   │   ├── filters.rq, heritage-detail.rq, heritage-images.rq, map.rq,
│   │   ├── overview.rq, recommendation.rq, related-heritage.rq, search.rq, etc.
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php             # Main site shell (Plus Jakarta Sans, x-Navbar, x-explore.Footer)
│       ├── components/
│       │   ├── Navbar.blade.php          # Morphing Airbnb-inspired navigation bar with filter modal
│       │   ├── HeroSection.blade.php     # GSAP parallax hero section with live stats
│       │   └── explore/
│       │       ├── FilterSidebar.blade.php # Sidebar with keyword, category, country, era, criteria
│       │       ├── HeritageCard.blade.php  # Editorial heritage card with badges & criteria chips
│       │       ├── Pagination.blade.php    # Server-driven pagination controls
│       │       └── Footer.blade.php        # Global footer
│       ├── welcome.blade.php             # Homepage view (HeroSection + Knowledge Graph overview)
│       ├── explore/
│       │   ├── index.blade.php           # Active Explore & Search view (Grid + Table view toggle)
│       │   └── ExplorePage.blade.php     # [Orphaned/Legacy] Standalone mock prototype
│       ├── heritage/
│       │   ├── show.blade.php            # Active Heritage Detail view
│       │   └── index.blade.php           # [Orphaned/Legacy] Barebones prototype
│       ├── parallax.blade.php            # [Placeholder] Parallax demo assigned to /map route
│       ├── search/
│       │   └── index.blade.php           # [Orphaned/Legacy] Dark prototype view
│       └── statistics/
│           └── index.blade.php           # World Heritage statistics page
├── routes/
│   ├── web.php                           # Web routes (/, /explore, /search, /map, /discover, /heritage/{id})
│   └── api.php                           # Throttled JSON API routes (/api/heritages, /api/map/heritages, etc.)
└── tests/
    ├── Feature/ExampleTest.php           # Tests GET /
    └── Unit/ExampleTest.php              # Basic PHPUnit test
```

---

## 4. Implementation Status Dashboard

| Requirement ID | Feature / Requirement | Priority | Status | Evidence | Remaining Work |
|---|---|---|---|---|---|
| **NAV-1** | Global navbar with logo, search pill, and menu links | Wajib (MVP) | **Implemented** | [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L154-L310), [app.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/layouts/app.blade.php#L19) | Complete. |
| **NAV-2** | Scroll-driven compact $\leftrightarrow$ expanded morphing search bar | Wajib (MVP) | **Implemented** | [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L18-L65), [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L124-L131) | Complete; CSS transition and Alpine scroll listener function seamlessly. |
| **NAV-3** | Expanding full filter panel upon search bar activation | Wajib (MVP) | **Implemented** | [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L136-L146), [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L210-L450) | Complete; includes overlay backdrop scrim and ESC key listener. |
| **NAV-4** | Parity of navbar filter panel with Explore filters | Wajib (MVP) | **Implemented** | [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L330-L440) | Complete; keyword, country, region, category, year, criteria, danger, transboundary. |
| **NAV-5** | Search submission navigates to Explore with populated query parameters | Wajib (MVP) | **Implemented** | [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L332) | Complete; form submits GET to `/search` which invokes `SearchController@index`. |
| **NAV-6** | Responsive mobile menu navigation and collapsible filter drawer | Wajib (MVP) | **Implemented** | [Navbar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/Navbar.blade.php#L454-L485) | Complete; mobile drawer toggled via Alpine. |
| **HOME-1** | Hero section with tagline, typography, and heritage imagery | Wajib (MVP) | **Implemented** | [HeroSection.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/HeroSection.blade.php#L10-L73) | Imagery currently references static local layers (`img/1.png`, `img/2.png`, `img/last layer.png`) rather than dynamic SPARQL image selection. |
| **HOME-2** | Pinned / scroll-driven layered parallax animation | Wajib (MVP) | **Implemented** | [app.js](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/js/app.js#L14-L64), [HeroSection.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/HeroSection.blade.php#L14-L70) | GSAP ScrollTrigger + Lenis implemented; verify mobile behavior disables complex pin. |
| **HOME-3** | Live SPARQL-backed statistics on homepage | Wajib (MVP) | **Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L20-L29), [StatisticsService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/StatisticsService.php#L13-L26), [overview.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/overview.rq) | Complete; includes fallback values if Fuseki is unreachable. |
| **HOME-4** | Navigation action buttons to Explore and Map | Wajib (MVP) | **Implemented** | [HeroSection.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/HeroSection.blade.php#L134-L144) | Complete; links to `/search` and `/map`. |
| **EXP-1** | Keyword search across heritage name and country | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L135-L144), [SearchRequest.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Http/Requests/SearchRequest.php#L17) | Complete; sanitized and matched via SPARQL `CONTAINS`. |
| **EXP-2** | Country filter (State Party) | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L146-L148), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L75-L86) | Complete; verified against `wh:country_XX`. |
| **EXP-3** | Region filter (Geographic Zone) | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L150-L152), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L89-L112) | Complete; verified against `wh:region_XX`. |
| **EXP-4** | Category filter (Cultural, Natural, Mixed) | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L154-L159), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L53-L72) | Complete. |
| **EXP-5** | Inscription Year range filter | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L176-L185), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L115-L136) | Complete; dual-range slider with dynamic min/max bounds. |
| **EXP-6** | UNESCO Criteria filter (i through x) | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L161-L166), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L140-L156) | Complete; formatted in Roman numeral sequence. |
| **EXP-7** | World Heritage in Danger toggle filter | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L168-L170), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L161-L169) | Complete. |
| **EXP-8** | Transboundary filter | Penting | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L172-L174), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L171-L179) | Complete. |
| **EXP-9** | SPARQL-backed search query execution | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L31-L88), [search.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/search.rq), [search-details.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/search-details.rq) | Complete; executes two-stage count and batch enrichment queries. |
| **EXP-10** | URL query parameter synchronization | Wajib (MVP) | **Implemented** | [SearchController.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Http/Controllers/SearchController.php#L13-L36), [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L72-L93) | Complete; URL bookmarks immediately hydrate filters and server query. |
| **EXP-11** | Left-hand filter sidebar and right-hand results grid | Wajib (MVP) | **Implemented** | [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L193-L265) | Complete; sticky desktop sidebar layout. |
| **EXP-12** | Initial heritage cards displayed when no filters applied | Wajib (MVP) | **Implemented** | [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L40), [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L220-L224) | Complete; defaults to latest inscription year sorting without filters. |
| **EXP-13** | Active filter indicator chips with single and all clear actions | Penting | **Implemented** | [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L174-L190) | Complete; individual chip removal via `$dropUrl` and global reset link. |
| **EXP-14** | Responsive mobile filter sheet / toggle button | Wajib (MVP) | **Implemented** | [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L200-L206), [FilterSidebar.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/FilterSidebar.blade.php#L21) | Complete; Alpine.js toggles drawer. |
| **RES-1** | Card-based search results display | Wajib (MVP) | **Implemented** | [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L220-L224), [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php) | Complete (also includes table view toggle). |
| **RES-2** | Card fields: image, name, country, region, category, year, criteria | Wajib (MVP) | **Implemented** | [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php#L36-L113) | Complete; all required semantic fields are rendered with fallback placeholders. |
| **RES-3** | Connected heritage count indicator on cards | Penting | **Partially Implemented** | [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php#L96-L101), [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php#L122) | UI logic exists in component, but `SearchService` hardcodes `'connections' => 0`. Count query not attached. |
| **RES-4** | "View Details" button linking to Heritage Detail | Wajib (MVP) | **Implemented** | [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php#L116-L126) | Complete; links to `/heritage/{id}`. |
| **RES-5** | Rounded cards in responsive grid (3-4 columns desktop) | Wajib (MVP) | **Implemented** | [explore/index.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/explore/index.blade.php#L220), [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php#L34) | Grid uses `grid-cols-1 md:grid-cols-2 xl:grid-cols-3` with `rounded-[24px]`. |
| **RES-6** | Smooth hover card micro-animations | Penting | **Implemented** | [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php#L34), [HeritageCard.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/components/explore/HeritageCard.blade.php#L49) | Uses Tailwind `transition-all duration-350 ease-out hover:-translate-y-1.5 hover:shadow-md`. |
| **DET-1** | Structured detail page sections (Hero, Quick Info, About, Criteria, etc.) | Wajib (MVP) | **Partially Implemented** | [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php) | Hero, Quick Info, Criteria, Description, Justification, Gallery, Components rendered. Location & OUV missing. |
| **DET-2** | Display of all primary heritage metadata and geographic coordinates | Wajib (MVP) | **Partially Implemented** | [HeritageService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/HeritageService.php#L55-L70), [heritage-detail.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/heritage-detail.rq) | Metadata fields rendered. Geographic coordinates (`lat`/`lng`) are NOT queried in `heritage-detail.rq` and not displayed. |
| **DET-3** | "View on Map" button on Heritage Detail | Wajib (MVP) | **Not Implemented** | [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php) | No map button or link to open site coordinates on `/map`. |
| **DET-4** | Connected Knowledge clickable relation lists | Wajib (MVP) | **Partially Implemented** | [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php#L102-L136) | Renders Components, related sites (same country), and recommendations. External entities missing. |
| **DET-5** | Embedded RDFa markup and Open Graph Protocol metadata | Wajib (MVP) | **Not Implemented** | [app.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/layouts/app.blade.php), [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php) | No RDFa (`vocab`, `typeof`, `property`) or Open Graph (`og:title`, etc.) tags present. |
| **DET-6** | Scroll-driven reveal section animations on detail page | Wajib (MVP) | **Not Implemented** | [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php) | Page renders standard static document flow without scroll animations. |
| **REL-1** | Related heritage list for current site | Wajib (MVP) | **Implemented** | [HeritageService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/HeritageService.php#L106-L128), [related-heritage.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/related-heritage.rq) | Backend SPARQL query returns sites sharing the same country. |
| **REL-2** | Multi-faceted relation criteria (Country, Category, Criteria) | Wajib (MVP) | **Implemented** | [related-heritage.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/related-heritage.rq), [recommendation.rq](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/sparql/recommendation.rq) | Implemented via two separate queries: same country (`related-heritage.rq`) and same category/criteria (`recommendation.rq`). External links absent. |
| **REL-3** | Navigation from related item to other Heritage Detail | Wajib (MVP) | **Implemented** | [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php#L127-L132) | Related items are clickable links to `/heritage/{id}`. |
| **REL-4** | Related items use the shared Heritage Card component | Wajib (MVP) | **Partially Implemented** | [heritage/show.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/heritage/show.blade.php#L127-L132) | Inconsistent: Uses basic plain text boxes instead of reusing `<x-explore.HeritageCard>`. |
| **MAP-1** | Interactive world map with markers derived from SPARQL coordinates | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L38-L40), [parallax.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/parallax.blade.php) | Backend service and API exist (`MapService.php`, `/api/map/heritages`), but route `/map` serves `parallax.blade.php`. Frontend Leaflet map is missing. |
| **MAP-2** | Marker popups with heritage summary | Wajib (MVP) | **Not Implemented** | Frontend Map absent | Needs Leaflet popup implementation consuming `/api/map/heritages`. |
| **MAP-3** | Marker popup link to Heritage Detail | Wajib (MVP) | **Not Implemented** | Frontend Map absent | Needs Leaflet popup link implementation. |
| **MAP-4** | Map filters by category, country, in-danger | Wajib (MVP) | **Partially Implemented** | [MapService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/MapService.php#L33-L41), [HeritageApiController.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Http/Controllers/Api/HeritageApiController.php#L54-L64) | Backend filtering supported in API; frontend controls not built. |
| **MAP-5** | Location / keyword search on map with fly-to | Penting | **Partially Implemented** | [MapService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/MapService.php#L36-L38) | Backend supports `q` parameter; frontend Leaflet search & fly-to missing. |
| **DSC-1** | Discover exploration By Country | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L43) | Route `/discover` redirects to `/explore`. Dedicated view absent. |
| **DSC-2** | Discover exploration By Region | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L43) | Redirects to `/explore`. Dedicated view absent. |
| **DSC-3** | Discover exploration By Category | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L43) | Redirects to `/explore`. Dedicated view absent. |
| **DSC-4** | Discover exploration By Criteria | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L43) | Redirects to `/explore`. Dedicated view absent. |
| **DSC-5** | Discover exploration for World Heritage in Danger | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L43) | Redirects to `/explore`. Dedicated view absent. |
| **DSC-6** | Discover selection opens pre-filtered Explore results | Wajib (MVP) | **Partially Implemented** | `/explore?country=...` URL filter capability exists | Discover entry points do not exist to initiate the navigation. |
| **DSC-7** | Curved card carousel interface with step navigation | Wajib (MVP) | **Not Implemented** | No carousel component found | Carousel UI component is unbuilt. |
| **BOT-1** | Shared floating Ask Heritage button on all pages | Wajib (MVP) | **Not Implemented** | [layouts/app.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/layouts/app.blade.php) | No floating trigger component in layout. |
| **BOT-2** | Chatbot search intent handling ("Cari heritage di Indonesia") | Wajib (MVP) | **Not Implemented** | No chatbot implementation found | Neither LLM integration nor rule-based intent router exists. |
| **BOT-3** | Chatbot filtered queries handling | Wajib (MVP) | **Not Implemented** | No chatbot implementation found | Intent handler absent. |
| **BOT-4** | Chatbot factual knowledge questions | Wajib (MVP) | **Not Implemented** | No chatbot implementation found | Intent handler absent. |
| **BOT-5** | Chatbot relationship queries | Penting | **Not Implemented** | No chatbot implementation found | Intent handler absent. |
| **BOT-6** | Chatbot map action queries | Penting | **Not Implemented** | No chatbot implementation found | Intent handler absent. |
| **BOT-7** | Query to SPARQL translation grounded in Knowledge Graph | Wajib (MVP) | **Not Implemented** | No chatbot implementation found | Intent handler absent. |
| **BOT-8** | Current page context awareness | Penting | **Not Implemented** | No chatbot implementation found | Context tracker absent. |
| **BOT-9** | Post-response action buttons (View Result, Show on Map, Open Heritage) | Penting | **Not Implemented** | No chatbot implementation found | Action dispatcher absent. |
| **ABT-1** | About HeritageFinder page | Wajib (MVP) | **Not Implemented** | [web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php#L46) | `/about` route serves `view('welcome', ['stats' => []])`. No dedicated About view. |
| **ABT-2** | Data Sources documentation section | Wajib (MVP) | **Not Implemented** | No Data Sources view found | Content describing UNESCO dataset and schema absent. |
| **ABT-3** | Semantic Web explanation section (RDF, RDFa, OG, SPARQL) | Penting | **Partially Implemented** | [welcome.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/welcome.blade.php#L8-L68) | Brief 3-card summary on homepage, but no comprehensive educational section. |
| **ABT-4** | Scroll-driven section reveal animations on About page | Wajib (MVP) | **Not Implemented** | No dedicated About view | Section animations absent. |
| **SEM-1** | Custom RDF ontology and converted dataset | Wajib (MVP) | **Implemented** | [TEMPAT-GONG-DIDUNYA.ttl](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/TEMPAT-GONG-DIDUNYA.ttl#L1-L60) | Complete; 16.3 MB Turtle file with valid `wh:` ontology definitions. |
| **SEM-2** | Apache Jena Fuseki storage and SPARQL endpoint access | Wajib (MVP) | **Implemented** | [SparqlService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SparqlService.php), [config/services.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/config/services.php#L38-L41) | Code complete. Requires active Fuseki server at runtime. |
| **SEM-3** | External SPARQL endpoint / dataset integration (DBpedia/Wikidata) | Wajib (MVP) | **Not Implemented** | [SparqlService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SparqlService.php) | No external SPARQL endpoint or dataset linking implemented. |
| **SEM-4** | External service integration for map tiles | Wajib (MVP) | **Not Implemented** | No map tile provider configured | Leaflet / OpenStreetMap tiles not integrated. |
| **SEM-5** | RDFa and Open Graph Protocol markup on public pages | Wajib (MVP) | **Not Implemented** | [app.blade.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/resources/views/layouts/app.blade.php) | Markup completely absent from all Blade templates. |
| **SEM-6** | SPARQL queries utilized for all data retrieval without hardcoding | Wajib (MVP) | **Implemented** | [routes/web.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/routes/web.php), [SearchService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/SearchService.php), [StatisticsService.php](file:///d:/Collage/SEMESTER%203/WEB%20SEMANTIK/TUBES/PROJECT-WebSemantik/app/Services/StatisticsService.php) | Fully query-driven; all application features query Fuseki templates. |

---

## 5. Backend and Semantic Data Status

### 5.1 Architecture & Flow
The backend conforms strictly to the technical constraints specified in `ARCHITECTURE.md`:
$$\text{Controller / API} \longrightarrow \text{Domain Service} \longrightarrow \text{SparqlService} \longrightarrow \text{Fuseki Endpoint}$$
No direct SPARQL queries are written in controllers, and free-form user SPARQL strings are rejected.

### 5.2 Routes & Controllers
- **Web Routes (`routes/web.php`):**
  - `/` $\rightarrow$ `welcome.blade.php` with `$statistics->overview()`
  - `/explore` & `/search` $\rightarrow$ `SearchController@index`
  - `/heritage/{id}` $\rightarrow$ `HeritageController@show` (restricted via regex `site_[0-9]+`)
  - `/heritage/{id}/images` $\rightarrow$ JSON response of `HeritageService@getImages`
  - `/statistics` $\rightarrow$ `StatisticsController@index`
  - `/test-fuseki` $\rightarrow$ Local environment triple count test
  - `/map` $\rightarrow$ **Defect:** returns `parallax.blade.php` instead of map view
  - `/discover` $\rightarrow$ **Defect:** redirects to `/explore`
  - `/about` $\rightarrow$ **Defect:** returns `view('welcome', ['stats' => []])`
- **API Routes (`routes/api.php`):**
  - Throttled at 120 requests/minute via `HeritageApiController`:
    - `GET /api/heritages` $\rightarrow$ Search & pagination
    - `GET /api/heritages/{id}` $\rightarrow$ Complete site details & relations
    - `GET /api/heritages/{id}/related` $\rightarrow$ Related & recommendations
    - `GET /api/map/heritages` $\rightarrow$ Geographic marker projection
    - `GET /api/filters` $\rightarrow$ Available filter facets
    - `GET /api/statistics` $\rightarrow$ Summary statistics
  - **Missing API Route:** `POST /api/chat` is not registered.

### 5.3 SPARQL Services & Query Templates
Located in `resources/sparql/`:
- `overview.rq`: Aggregates distinct sites, countries, and regions.
- `filters.rq`: Aggregates distinct country, region, category, and criterion labels.
- `year-bounds.rq`: Determines minimum and maximum inscription years.
- `search-count.rq`: Computes total matching sites for dynamic filter criteria.
- `search.rq`: Retrieves paginated site IRIs, names, and years.
- `search-details.rq`: Enriches current page site IDs with categories, countries, criteria, and images in a single batch query.
- `heritage-detail.rq`: Queries comprehensive properties of a specific site. *(Missing geographic coordinates `geo:lat` and `geo:long`)*.
- `heritage-images.rq`: Fetches associated gallery images (`wh:hasImage`, `wh:imageUrl`).
- `heritage-components.rq`: Retrieves constituent site components (`wh:hasComponent`).
- `related-heritage.rq`: Finds sites sharing the same country party.
- `recommendation.rq`: Finds sites sharing category and criteria in different countries.
- `map.rq`: Extracts coordinates from constituent components (`geo:lat`, `geo:long`), along with danger status and category.
- `statistics.rq`: Calculates sites grouped by category.

### 5.4 RDF Dataset & Ontology
- **File:** `TEMPAT-GONG-DIDUNYA.ttl` (16,282,417 bytes, 179,987 lines).
- **Ontology Namespace:** `wh: <https://example.org/heritage/ontology/>`.
- **Classes:** `wh:WorldHeritageSite`, `wh:Region`, `wh:Category`, `wh:Criterion`, `wh:HeritageComponent`, `wh:Image`.
- **Properties:** `wh:areaHectares`, `wh:belongsToRegion`, `wh:hasCategory`, `wh:hasComponent`, `wh:hasCriterion`, `wh:hasMainImage`, `wh:imageCaption`, `wh:imageUrl`, `wh:inscriptionYear`, `wh:isInDanger`, `wh:isTransboundary`, `wh:justification`, `wh:locatedIn`.
- **External Links:** Dataset does not currently define `owl:sameAs` links to DBpedia or Wikidata.

### 5.5 Security & Parameter Validation
- Strict validation via `SearchRequest`:
  - `q`: string, max 100 characters.
  - `country`, `region`, `criterion`: alphanumeric whitelist, max 10 characters.
  - `category`: max 20 characters mapped to static whitelist (`Cultural`, `Natural`, `Mixed`).
  - `year_min`, `year_max`: integers 1 to 2100.
  - `danger`, `transboundary`: boolean strings (`true`, `false`).
  - `sort`: whitelisted enum (`name_asc`, `name_desc`, `year_asc`, `year_desc`).
- In `HeritageService`: IDs are validated strictly with regex `/^site_\d+$/` before template interpolation.
- In `SearchService`: string literals in keyword searches escape backslashes, double quotes, and control characters (`SearchService::lit()`).

---

## 6. Frontend and Design Status

### 6.1 Design System & Tokens (`DESIGN.md` Compliance)
- **CSS Architecture:**
  - `resources/css/global.css` specifies all CSS custom properties from `DESIGN.md`:
    - Full neutral monochrome scale (`--gray-0` `#FFFFFF` to `--gray-950` `#0A0A0A`).
    - Semantic tokens (`--color-bg`, `--color-border`, `--color-text`, `--color-primary`).
    - Grayscale gradients (`--gradient-hero`, `--gradient-dark`, `--overlay-image`, `--overlay-scrim`).
    - Border radius tokens (`--radius-sm: 8px` to `--radius-2xl: 32px`, `--radius-full: 9999px`).
    - Shadows (`--shadow-sm`, `--shadow-md`, `--shadow-lg`).
    - Motion tokens (`--ease-out`, `--ease-in-out`, `--ease-spring`, `--dur-fast`, `--dur-hero`).
  - `resources/css/app.css` maps tokens to Tailwind CSS v4 `@theme` block.
- **Typography:** Uses Google Font `Plus Jakarta Sans` with weights 400 through 800, using `tabular-nums` for stat alignments.

### 6.2 Component Analysis
1. **`<x-Navbar />` (`resources/views/components/Navbar.blade.php`):**
   - Excellent implementation conforming to Airbnb-inspired editorial layout.
   - Morphing single-element search pill (`.search-morph`) expands from 340px to 580px on scroll or click.
   - Modal backdrop scrim and comprehensive filter drawer with synced form inputs.
   - Mobile navigation drawer included.
2. **`<x-HeroSection />` (`resources/views/components/HeroSection.blade.php`):**
   - Multi-layer visual depth effect using GSAP parallax tags.
   - SPARQL summary statistics cards with large bold numbers.
   - Pinned / layered transition effect into the next section.
3. **`<x-explore.FilterSidebar />` (`resources/views/components/explore/FilterSidebar.blade.php`):**
   - Filter controls for keyword, category pills, country select, region radio list, dual-handle inscription era range sliders, UNESCO criteria buttons, and status toggles.
   - Sticky positioning on desktop, hidden behind button toggle on mobile.
4. **`<x-explore.HeritageCard />` (`resources/views/components/explore/HeritageCard.blade.php`):**
   - Clean editorial card design with category pill, in-danger alert badge, metadata row, criteria chips, lazy loaded image with fallback placeholder, and hover animations.
5. **View Toggle in Explore:**
   - Supports seamless client-side toggling between 3-column Grid and structured Table view via Alpine.js.

### 6.3 Frontend Inconsistencies & Deficiencies
1. **Explore View Isolation:** `resources/views/explore/index.blade.php` is an independent HTML file that does not extend `layouts/app.blade.php`. It injects Tailwind CSS and Alpine.js via CDN `<script>` tags instead of the compiled Vite bundle (`@vite(['resources/css/app.css', 'resources/js/app.js'])`).
2. **Missing Floating Chatbot:** No chatbot launcher button in `layouts/app.blade.php` or `explore/index.blade.php`.
3. **Detail Page Visual Simplicity:** `heritage/show.blade.php` does not use GSAP scroll reveals and renders simple text links for related sites rather than `<x-explore.HeritageCard>`.
4. **Reduced Motion:** Configured in `global.css` (`@media (prefers-reduced-motion: reduce)` sets animation durations to 0.01ms), but GSAP scripts in `app.js` do not explicitly query `window.matchMedia('(prefers-reduced-motion: reduce)')` to disable ScrollTrigger timelines.

---

## 7. Testing and Verification

| Test / Check | Execution Method | Result | Notes & Limitations |
|---|---|---|---|
| **Vite Asset Build** | `npm run build` | **Passed** | Built client environment in 10.34s without errors (CSS: 54.35 kB, JS: 233.27 kB). |
| **Unit Test Suite** | `php artisan test` (`Tests\Unit\ExampleTest`) | **Passed** | 1 test, 1 assertion passed in 0.75s. |
| **Feature Test Suite** | `php artisan test` (`Tests\Feature\ExampleTest`) | **Passed** | 1 test, 1 assertion passed. Took 20.22s because Fuseki timeout occurred during `$this->get('/')`. |
| **Fuseki Port 3030 Check** | `Test-NetConnection -Port 3030` | **Failed / Offline** | Connection to `127.0.0.1:3030` failed. Apache Jena Fuseki is not currently active on the host machine. |
| **SPARQL Service Unit Tests** | Automated PHPUnit | **Not Run** | No unit tests exist for `SparqlService`, `SearchService`, `FilterService`, or `MapService`. |
| **API Endpoints Automated Tests**| Automated PHPUnit | **Not Run** | No feature tests exist for `/api/heritages`, `/api/map/heritages`, or `/api/filters`. |
| **Static Code & Syntax Audit** | Static Inspection | **Passed** | All PHP services, controllers, requests, and Blade components inspected without syntax errors. |

---

## 8. Phase 2 Status

Phase 2 requirements are deferred until MVP completion per `PRD.md` (Section 4.2 & 11) and `ARCHITECTURE.md`:

| Requirement ID | Feature Name | Status | Evidence / Notes |
|---|---|---|---|
| **OPT-1** | Interactive Knowledge Graph Visualization | **Not Implemented** | Deferred. No graph visualization library (Cytoscape, D3, Vis.js) installed. |
| **OPT-2** | "Explore Connections" Graph Navigation | **Not Implemented** | Deferred. |
| **OPT-3** | Compare Heritage Sites | **Not Implemented** | Deferred. No comparison controller or view exists. |
| **OPT-4** | Historical Period Categorization | **Not Implemented** | Deferred. `TEMPAT-GONG-DIDUNYA.ttl` does not contain `wh:historicalPeriod` triples. |
| **OPT-5** | Advanced Natural Language Semantic Search | **Not Implemented** | Deferred. |
| **OPT-6** | Semantic Recommendation Engine | **Partially Implemented** | Basic SPARQL-based recommendation query (`recommendation.rq`) and backend method `HeritageService::getRecommendations()` exist and are rendered on detail pages, though complex recommendation logic is deferred. |
| **OPT-7** | Favorite / Saved Heritage Properties | **Not Implemented** | Deferred. No local storage or session persistence for saved sites. |

---

## 9. Known Issues and Blockers

### Confirmed Technical Defects & Bugs
1. **Broken `/map` Route:** `routes/web.php` maps `/map` to `view('parallax')`. This displays an unrelated GSAP demo with static text ("Konten Setelah Scroll") rather than an interactive map.
2. **Explore View Bypass of Asset Pipeline:** `resources/views/explore/index.blade.php` does not extend `layouts/app.blade.php` and loads Tailwind CSS and Alpine.js via CDN instead of `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
3. **Missing Coordinates on Heritage Detail:** `resources/sparql/heritage-detail.rq` omits coordinates (`geo:lat`, `geo:long`), preventing `HeritageService::getById()` from providing coordinates to `heritage/show.blade.php` and blocking the "View on Map" feature.
4. **Hardcoded Connection Count:** `SearchService::enrich()` hardcodes `'connections' => 0`, causing the connections indicator badge in `HeritageCard.blade.php` to remain hidden on all cards.
5. **Inconsistent Related Card Layout:** `heritage/show.blade.php` renders basic border boxes for related heritage instead of reusing `<x-explore.HeritageCard>`.
6. **Missing Redirect Targets:** `/discover` redirects to `/explore` and `/about` renders `welcome.blade.php`, leaving core PRD pages unbuilt.

### Environmental & Operational Blockers
1. **Fuseki Server Offline:** The local Apache Jena Fuseki service is not running on port 3030. Live SPARQL queries cannot execute until the Fuseki triple store process is launched and `TEMPAT-GONG-DIDUNYA.ttl` is mounted at `/websemantik`.
2. **Missing Test Coverage:** There are zero automated tests covering SPARQL query generation, search filtering, API responses, or error handling.

---

## 10. Next Steps

### Priority 0 (P0) — Critical Blockers & Architecture Fixes
1. **Start Apache Jena Fuseki Service:** Launch Fuseki on port 3030 with the dataset `/websemantik` loading `TEMPAT-GONG-DIDUNYA.ttl` to enable live SPARQL queries and eliminate 20-second request timeouts.
2. **Refactor `explore/index.blade.php` to use Vite:** Remove CDN script tags from `resources/views/explore/index.blade.php`, ensure it extends `layouts/app.blade.php` or imports Vite assets (`@vite`), unifying styling with the rest of the application.
3. **Add Coordinates to Heritage Detail Query:** Update `resources/sparql/heritage-detail.rq` and `HeritageService::getById()` to query `wh:hasComponent` coordinates (`geo:lat`, `geo:long`) so coordinates and map links can function on the detail page.

### Priority 1 (P1) — Essential Missing MVP Functionality
1. **Implement Interactive Map View (`MAP-1` through `MAP-5`):**
   - Replace the `/map` route callback to render a dedicated Blade view (e.g., `resources/views/map/index.blade.php` extending `layouts/app.blade.php`).
   - Integrate Leaflet via npm or CDN with monochrome tiles.
   - Fetch markers from `GET /api/map/heritages`.
   - Implement marker popups with heritage name, country, thumbnail, and link to `/heritage/{id}`.
   - Implement client-side category and danger status filtering.
2. **Add "View on Map" Link on Detail Page (`DET-3`):** Add button on `heritage/show.blade.php` linking to `/map?id={id}&lat={lat}&lng={lng}` with map fly-to focus.
3. **Implement Ask Heritage Chatbot Floating UI & Intent Handler (`BOT-1` through `BOT-9`):**
   - Create shared floating button and modal in `resources/views/layouts/app.blade.php`.
   - Implement `ChatbotService` and register `POST /api/chat`.
   - Implement structured intent parsing for search, filter, and site queries grounded in predefined SPARQL queries.
4. **Implement Discover Page with Carousel (`DSC-1` through `DSC-7`):**
   - Replace `/discover` redirect with a dedicated view.
   - Build curved carousel cards for By Country, By Region, By Category, By Criteria, and In Danger, linking to pre-filtered `/explore` queries.
5. **Implement Dedicated About & Semantic Web Page (`ABT-1` through `ABT-4`):**
   - Replace `/about` route callback with a dedicated view.
   - Detail HeritageFinder, UNESCO data provenance, and Semantic Web principles (RDF, SPARQL, Linked Data).
6. **Embed RDFa and Open Graph Metadata (`DET-5`, `SEM-5`):**
   - Add schema.org / RDFa attributes (`typeof="schema:Place"`, `property="schema:name"`, etc.) in `layouts/app.blade.php` and `heritage/show.blade.php`.
   - Add Open Graph meta tags (`og:title`, `og:description`, `og:image`, `og:type`).

### Priority 2 (P2) — MVP Refinements & Card Consistency
1. **Card Component Parity on Detail Page (`REL-4`):** Refactor Related Heritage and Recommendations in `heritage/show.blade.php` to render `<x-explore.HeritageCard>`.
2. **Dynamic Connections Count (`RES-3`):** Update `SearchService::enrich()` or write a query to populate the actual count of connected triples/components rather than hardcoding `0`.
3. **Automated Test Suite:** Create PHPUnit feature tests for `SearchController`, `HeritageApiController`, and unit tests for `SearchService` filter conditions.
4. **Cleanup Orphaned Prototype Files:** Safely archive or remove legacy mock files `ExplorePage.blade.php`, `search/index.blade.php`, and `heritage/index.blade.php`.

### Priority 3 (P3) — Deferred Phase 2 Features
1. Interactive Knowledge Graph network visualization using Cytoscape or D3 (`OPT-1`, `OPT-2`).
2. Multi-site Comparison tool (`OPT-3`).
3. External SPARQL / dataset linking to DBpedia / Wikidata (`SEM-3`).

---

## 11. Recommended Next Task

### Task: Implement the Interactive Map Page (`MAP-1` through `MAP-5`)

#### Why this task should be next:
1. The backend infrastructure for the map is already completely implemented: `resources/sparql/map.rq`, `app/Services/MapService.php`, and `GET /api/map/heritages` in `HeritageApiController.php` already exist, validate inputs, and format marker JSON.
2. The current `/map` route is a high-visibility broken route that serves an unrelated GSAP demo (`parallax.blade.php`), breaking the core user journey (`Home` $\rightarrow$ `Explore` $\rightarrow$ `Detail` $\rightarrow$ `Map`).
3. Completing the map unblocks `DET-3` ("View on Map" button from Heritage Detail) and `BOT-6` (Chatbot map integration).

#### What must change:
1. In `routes/web.php`: Update `Route::get('/map', ...)` to render a new view `resources/views/map/index.blade.php`.
2. Create `resources/views/map/index.blade.php` extending `layouts/app.blade.php`:
   - Include Leaflet CSS and JS (or install via package manager and bundle in Vite).
   - Set up an Alpine.js component initializing the Leaflet map with a monochrome tile layer (e.g. CartoDB Positron / OpenStreetMap).
   - Fetch marker data asynchronously from `/api/map/heritages`.
   - Render custom markers with popups displaying the site name, country, category badge, and a "View Details" button linking to `/heritage/{id}`.
   - Add category, danger, and keyword filter controls in a floating overlay panel.
   - Support URL parameters (e.g., `?lat=...&lng=...&id=...`) to immediately fly to a specific site when navigated from Heritage Detail.

#### Existing files to reuse:
- `app/Services/MapService.php` (already extracts sampled coordinates from `wh:hasComponent`).
- `app/Http/Controllers/Api/HeritageApiController.php@map` (already handles `/api/map/heritages`).
- `resources/sparql/map.rq` (already queries `geo:lat` and `geo:long`).
- `resources/views/layouts/app.blade.php` (main layout shell).
- `resources/views/components/Navbar.blade.php` (global navigation bar).

#### Acceptance Criteria:
1. Navigating to `/map` in the browser renders a full-screen interactive world map.
2. Markers appear across global coordinates sourced from the RDF dataset via `/api/map/heritages`.
3. Clicking a marker displays a popup with site information and a link to `/heritage/{id}`.
4. Toggling category or in-danger filters updates the visible markers without reloading the page.
5. If query parameters `lat` and `lng` are present in the URL, the map initializes with a fly-to animation focused on that location.

#### Tests / Checks to run afterward:
- Run `npm run build` to verify asset compilation.
- Run `curl -s http://localhost:8000/api/map/heritages` (with Fuseki active) to verify marker JSON structure.
- Verify in browser that no JavaScript console errors occur when panning and zooming the map.

---

## 12. Audit Limitations

1. **Fuseki Offline During Audit:** Apache Jena Fuseki was not running on `localhost:3030`. As a result, live end-to-end SPARQL execution could not be evaluated in the browser during this audit. SPARQL templates were audited via static analysis and schema cross-referencing with `TEMPAT-GONG-DIDUNYA.ttl`.
2. **No Browser Rendering Subagent Executed:** Inspection was performed via static codebase analysis, dependency audits, build checks, and CLI tests. Visual inspection of CSS render bugs and browser-specific GSAP animation performance was not conducted via browser screenshot tools.
3. **External Network Constraints:** External SPARQL endpoints (DBpedia, Wikidata) were not queried over the live network.

---

## 13. Audit Update Metadata

- **Date:** 2026-10-12
- **Audit Scope:** Full repository scan of routes, controllers, services, SPARQL templates, Blade views, CSS/JS assets, RDF dataset, tests, and configuration.
- **Auditor Role:** Senior Software Engineer & Codebase Auditor
- **Status Baseline Version:** v1.0
