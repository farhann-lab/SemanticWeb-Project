# HeritageFinder — Architecture & Tech Stack Context

## Current State

* Laravel 12 + PHP 8
* Blade + Tailwind CSS + Vite
* Alpine.js untuk interaksi UI ringan
* Apache Jena Fuseki + SPARQL
* RDF dataset **sudah selesai**. Jangan recreate atau mengubah RDF tanpa kebutuhan eksplisit.
* Codebase **sudah memiliki progress**. Inspect dan reuse struktur existing sebelum membuat/memindahkan file.
* MVP priority: Search → Detail → Connected Knowledge → Related Heritage → Map → Ask Heritage.

## Architecture

```text
Browser
  ↓
Laravel 12
  ├── Blade + Tailwind + Alpine
  ├── API / Controllers
  ├── Application Services
  ├── SPARQL Service
  ├── Chatbot Service
  └── Map Service
        ↓
Apache Jena Fuseki
        ↓
Existing RDF Dataset

External integrations:
- External SPARQL / Dataset
- Leaflet + map tile provider
- LLM API
```

Browser **tidak boleh mengakses Fuseki secara langsung**.

Flow utama:

```text
Browser → Laravel → Service → Fuseki → RDF
```

## Frontend Stack

```text
Blade
Tailwind CSS
Alpine.js
Vite
CSS Custom Properties
Lucide
GSAP + ScrollTrigger
Lenis (optional)
```

### Rules

* **Blade tetap menjadi rendering utama.**
* Tailwind digunakan untuk layout dan utility styling.
* CSS Custom Properties digunakan untuk design tokens dari `DESIGN.md`.
* Alpine.js digunakan untuk:

  * navbar
  * filter
  * accordion
  * modal / bottom sheet
  * chatbot UI
  * carousel sederhana
  * local UI state
* **GSAP + ScrollTrigger** hanya untuk motion kompleks:

  * hero pinned scroll
  * scroll-driven transition
  * stagger/reveal kompleks
  * parallax
* **Lenis optional**, hanya jika smooth-scroll native/CSS tidak menghasilkan UX yang cukup baik.
* Lucide digunakan sebagai icon system.
* Jangan menambahkan React/Vue/Framer Motion kecuali codebase existing memang sudah menggunakannya atau ada kebutuhan teknis nyata.
* Jangan membuat SPA hanya untuk memenuhi desain visual.

## Design System

Gunakan design tokens dari `DESIGN.md` sebagai CSS variables:

```text
colors
spacing
radius
shadows
motion/easing
container
```

UI harus mengikuti:

* monochrome editorial minimalism
* foto heritage menjadi sumber warna utama
* tidak ada accent color pada UI
* gradient hanya grayscale → black
* consistent card system
* consistent button system
* WCAG/accessibility requirements
* `prefers-reduced-motion`

Jangan membuat design system baru yang bertentangan dengan `DESIGN.md`.

## Animation Rules

Gunakan CSS/Alpine untuk motion sederhana.

Gunakan GSAP/ScrollTrigger hanya jika dibutuhkan oleh desain.

```text
Simple:
CSS transition / transform / opacity

Medium:
Alpine + CSS

Complex:
GSAP + ScrollTrigger
```

Animation wajib:

* mengutamakan `transform` dan `opacity`
* tidak memblokir interaction
* menghormati `prefers-reduced-motion`
* tidak infinite kecuali loading
* pin/parallax kompleks disederhanakan di mobile

Hero desktop boleh menggunakan pinned scroll ±300vh sesuai DESIGN.md.

Hero mobile **tanpa pin**.

## Backend Architecture

Gunakan pola:

```text
Controller
    ↓
Application / Domain Service
    ↓
SPARQL Service
    ↓
Fuseki
```

Jangan menaruh SPARQL kompleks langsung di Controller.

Adaptasikan pola ini ke struktur folder existing. **Jangan melakukan restructuring besar hanya untuk mengikuti pola ini.**

## API

Target endpoint:

```text
GET  /api/heritages
GET  /api/heritages/{id}
GET  /api/heritages/{id}/related
GET  /api/map/heritages
GET  /api/discover/{type}
POST /api/chat
```

API harus menjadi boundary antara frontend dan semantic data.

## Semantic Layer

Primary semantic source:

```text
Apache Jena Fuseki
+
Existing RDF dataset
+
SPARQL 1.1
```

Rules:

* RDF existing adalah source of truth untuk semantic heritage data.
* Jangan migrate RDF ke SQL sebagai pengganti Fuseki.
* SQL database hanya supplementary jika aplikasi benar-benar membutuhkan transactional/application data.
* Major semantic features harus menggunakan relationship/data yang tersedia di knowledge graph.
* Jangan menerima arbitrary SPARQL dari user.

## Redis / Caching

Redis dapat digunakan untuk data yang relatif stabil:

* homepage statistics
* Discover data
* heritage detail
* related heritage
* map data
* external SPARQL results

Invalidation/TTL harus disesuaikan dengan frekuensi perubahan data.

## Map

Recommended:

```text
Leaflet
+
Monochrome map tiles
+
Marker clustering
+
Laravel Map API
+
Fuseki
```

Flow:

```text
Fuseki
  ↓
Laravel /api/map/heritages
  ↓
JSON
  ↓
Leaflet
```

Map MVP:

* markers
* clustering
* popup
* category/country/In Danger filter
* location search
* View Details
* fly-to

Tile provider dan lisensi masih perlu ditentukan.

Jangan menambahkan MapLibre/WebGL kecuali kebutuhan map berkembang melampaui kemampuan Leaflet.

## Chatbot — Ask Heritage

Gunakan:

```text
LLM API
+
Structured Intent / Tool Calling
+
Laravel Chatbot Service
+
Fuseki
```

Flow:

```text
User
 ↓
LLM
 ↓
Structured Intent
 ↓
Laravel validation
 ↓
Predefined SPARQL
 ↓
Fuseki
 ↓
Facts
 ↓
LLM response
```

Contoh intent:

```json
{
  "intent": "search_heritage",
  "country": "Indonesia",
  "category": "Cultural",
  "year_before": 2000
}
```

Rules:

* LLM tidak boleh membuat arbitrary SPARQL yang langsung dieksekusi.
* Laravel harus memvalidasi intent dan parameter.
* Chatbot harus grounded pada knowledge graph.
* Chatbot bukan general-purpose AI assistant.
* Support page context, misalnya heritage ID/name yang sedang dibuka.
* Action response dapat mengarah ke:

  * View Result
  * Show on Map
  * Open Heritage

Target chatbot response: **≤ 6 detik**.

## External Dataset

Pilih satu dataset terlebih dahulu:

```text
DBpedia
atau
Wikidata
```

Sebelum implementasi penuh:

1. lakukan 1 proof-of-concept SPARQL query
2. validasi kualitas dan relevansi data
3. cek latency dan availability
4. baru integrasikan lebih luas

External SPARQL failure **tidak boleh membuat core HeritageFinder rusak**.

## Image / Media

Gunakan:

```text
WebP / AVIF
srcset
lazy loading
fixed image dimensions
placeholder
```

Hero/above-the-fold image boleh eager load.

Semua image wajib memiliki descriptive `alt`.

Sumber, attribution, dan licensing image masih TBD.

## Testing / Debugging

Recommended:

```text
Pest / PHPUnit
Laravel Telescope
```

Test minimum:

* API response
* SPARQL service
* filter logic
* related heritage
* map data
* chatbot intent validation

## Deployment / Infrastructure

Recommended:

```text
Docker Compose
├── Laravel / PHP-FPM
├── Nginx
├── Fuseki
└── Redis
```

GitHub Actions dapat digunakan untuk CI/CD.

Fuseki **tidak boleh diekspos secara publik** tanpa kebutuhan dan security layer yang sesuai.

## Security

* Fuseki internal/private.
* Validate seluruh search/filter parameters.
* Jangan expose arbitrary SPARQL endpoint ke user.
* LLM API key hanya di backend environment.
* External service menggunakan timeout.
* Handle Fuseki/external SPARQL failure gracefully.
* Rate-limit chatbot/API jika diperlukan.

## Performance Targets

### Backend

```text
API p50          ≤ 300 ms
API p95          ≤ 800 ms
Complex SPARQL p95 ≤ 1.5 s
Map API p95      ≤ 1 s
External SPARQL p95 ≤ 3 s
Chatbot          ≤ 6 s
```

### Frontend

```text
Initial load     ≤ 3 s
LCP              ≤ 2.5 s
```

### Availability

```text
Uptime ≥ 99.5%
```

Performance harus diukur, bukan hanya diasumsikan.

## MVP Priority

Prioritaskan:

```text
1. Search
2. Explore + Filter
3. Heritage Detail
4. Connected Knowledge
5. Related Heritage
6. Interactive Map
7. Discover
8. Ask Heritage
9. About / Semantic Web
```

Core user journey:

```text
Search
  ↓
Explore
  ↓
Heritage Detail
  ↓
Connected Knowledge
  ↓
Related Heritage
  ↓
Map
  ↓
Ask Heritage
```

## Phase 2

Jangan implementasikan sebagai MVP kecuali existing codebase sudah mendukungnya:

* Knowledge Graph visualisasi
* Compare Heritage
* Historical Period
* Advanced Semantic Search
* Recommendation engine
* Favorites

RDF relationship data tetap merupakan bagian MVP. **Visual graph** yang dapat dieksplorasi adalah Phase 2.

## Agent Rules

1. Inspect existing codebase first.
2. Reuse existing controllers, services, views, components, routes, configs, and utilities.
3. Do not recreate or rewrite existing RDF dataset.
4. Do not perform large-scale restructuring without explicit need.
5. Preserve working features.
6. Make incremental changes.
7. Keep Laravel as the application/orchestration layer.
8. Keep Fuseki/SPARQL as the primary semantic source.
9. Use Blade + Tailwind + Alpine as the default frontend stack.
10. Use GSAP/ScrollTrigger only for complex motion required by DESIGN.md.
11. Treat Lenis as optional.
12. Do not introduce React, Vue, Framer Motion, microservices, Elasticsearch, vector DB, or another primary database without a concrete technical requirement.
13. Do not let the LLM execute arbitrary SPARQL.
14. Major semantic functionality should map to RDF relationships and SPARQL queries.
15. Follow `DESIGN.md` for visual behavior, responsive behavior, accessibility, motion, and design tokens.
16. Optimize for a working MVP before adding sophisticated effects.
17. Prefer simple, maintainable implementations over unnecessary dependencies.

## Core Success Criteria

```text
Working Search
      ↓
Explore + Filter
      ↓
Heritage Detail
      ↓
Connected Semantic Knowledge
      ↓
Related Heritage
      ↓
Interactive Map
      ↓
Ask Heritage
```

The system should demonstrate that HeritageFinder is not merely a heritage catalog, but a usable semantic exploration interface powered by the existing RDF knowledge graph.
