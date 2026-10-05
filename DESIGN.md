# DESIGN.md — HeritageFinder Style Guide

| | |
| --- | --- |
| **Produk** | HeritageFinder — Semantic Explorer for World Heritage Sites |
| **Versi** | v0.1 (Draft) |
| **Tanggal** | 3 Oktober 2026 |
| **Dokumen Terkait** | PRD_HeritageFinder.md (v0.3) |

Dokumen ini adalah panduan gaya visual dan interaksi untuk seluruh antarmuka HeritageFinder. Tujuannya agar desainer dan developer memakai bahasa visual yang sama. Keputusan yang masih terbuka ditandai **[TBD]** dan merujuk ke Bab 12 PRD.

---

## 1. Filosofi Desain

**Monochrome Editorial Minimalism dengan Kinetic Motion.**

| Prinsip | Arti dalam praktik |
| --- | --- |
| **Monokrom pada antarmuka** | Seluruh elemen UI (teks, tombol, card, panel, ikon, peta) memakai hitam, putih, dan abu-abu. |
| **Warna datang dari konten** | Foto heritage boleh berwarna. Foto menjadi satu-satunya sumber warna sehingga terasa menonjol. Tidak ada warna aksen pada UI. |
| **Tipografi sebagai hierarki** | Hierarki dibentuk oleh ukuran dan ketebalan huruf, bukan warna. |
| **Minimalis dan proporsional** | Layout padat dan fungsional (Atlas Obscura, Vilcek Foundation, Smithsonian), bukan elemen raksasa. |
| **Gerak yang halus** | Animasi memberi arah dan kesan premium (Rijksmuseum Boerhaave), tidak pernah menghalangi tugas pengguna. |
| **Konsisten** | Satu komponen card, satu bahasa tombol, satu sistem animasi di semua halaman. |

Referensi:

| Aspek | Referensi |
| --- | --- |
| Navbar | Airbnb |
| Layout, card, detail | Atlas Obscura, Vilcek Foundation, Smithsonian, UNESCO |
| Animasi, hero, carousel Discover | Rijksmuseum Boerhaave (tanpa meniru ukuran layoutnya yang besar) |

---

## 2. Design Tokens

Semua nilai di bawah dipakai sebagai CSS custom properties agar mudah diubah di satu tempat.

### 2.1 Warna

#### Skala netral

| Token | Hex | Penggunaan |
| --- | --- | --- |
| `--gray-0` | `#FFFFFF` | Latar utama, teks di atas hitam |
| `--gray-50` | `#FAFAFA` | Latar section bergantian |
| `--gray-100` | `#F5F5F5` | Latar chip, bubble chatbot, skeleton |
| `--gray-200` | `#E5E5E5` | Border card, divider |
| `--gray-300` | `#D4D4D4` | Border input |
| `--gray-400` | `#A3A3A3` | Placeholder, ikon nonaktif |
| `--gray-500` | `#737373` | Teks tersier |
| `--gray-600` | `#525252` | Teks sekunder |
| `--gray-800` | `#262626` | Hover tombol hitam |
| `--gray-900` | `#171717` | Latar gelap sekunder |
| `--gray-950` | `#0A0A0A` | Teks utama, tombol utama, hero |

#### Token semantik

```css
:root {
  --color-bg:            var(--gray-0);
  --color-bg-alt:        var(--gray-50);
  --color-bg-inverse:    var(--gray-950);
  --color-surface:       var(--gray-0);
  --color-border:        var(--gray-200);
  --color-border-strong: var(--gray-300);

  --color-text:          var(--gray-950);
  --color-text-muted:    var(--gray-600);
  --color-text-subtle:   var(--gray-500);
  --color-text-inverse:  var(--gray-0);

  --color-primary:       var(--gray-950);
  --color-primary-hover: var(--gray-800);
  --color-focus:         var(--gray-950);
}
```

#### Gradient

Gradient hanya boleh berupa gradasi **abu-abu ke hitam**.

```css
--gradient-hero:  linear-gradient(180deg, #A3A3A3 0%, #0A0A0A 100%);
--gradient-dark:  linear-gradient(180deg, #525252 0%, #0A0A0A 100%);

/* Overlay fungsional di atas foto agar teks terbaca (hitam transparan ke hitam pekat) */
--overlay-image:  linear-gradient(180deg, rgba(10,10,10,0) 30%, rgba(10,10,10,0.85) 100%);
--overlay-scrim:  rgba(10, 10, 10, 0.45);   /* latar di belakang panel/modal */
```

Aturan:
- Gradient dipakai untuk latar hero, section gelap, dan overlay foto.
- Jangan dipakai pada teks, tombol, atau ikon.
- Jangan menambah warna ke gradient.

#### Warna fungsional (opsional, minimal)

UI tidak memakai warna aksen. Jika nanti dibutuhkan umpan balik sistem yang harus jelas (misalnya error validasi), gunakan satu warna merah tua hanya pada teks atau border pesan error **[TBD]**. Status World Heritage in Danger **tidak** memakai warna (lihat bagian Badge).

### 2.2 Tipografi

**Rekomendasi font [TBD]:** Inter Tight, Archivo, Space Grotesk, atau Plus Jakarta Sans (Google Fonts). Pakai satu keluarga untuk heading dan body agar konsisten. Sediakan fallback `system-ui, -apple-system, "Segoe UI", sans-serif`.

| Peran | Ukuran (desktop → mobile) | Berat | Line-height | Letter-spacing |
| --- | --- | --- | --- | --- |
| Display (tagline hero) | `clamp(3rem, 7vw, 6rem)` | 800 | 1.0 | -0.03em |
| H1 | `clamp(2.25rem, 4.5vw, 3.5rem)` | 800 | 1.1 | -0.025em |
| H2 | `clamp(1.75rem, 3vw, 2.5rem)` | 700 | 1.15 | -0.02em |
| H3 | `1.5rem` | 700 | 1.25 | -0.015em |
| H4 / judul card | `1.125rem` | 700 | 1.3 | -0.01em |
| Body | `1rem` | 400 | 1.65 | 0 |
| Body besar (OUV, intro) | `1.25rem` | 500 | 1.55 | -0.005em |
| Small / meta | `0.875rem` | 500 | 1.5 | 0 |
| Label (badge, section index) | `0.75rem` | 700 | 1.2 | 0.08em, UPPERCASE |

Aturan:
- Heading selalu tebal (700–800). Hindari berat di bawah 400.
- Panjang baris teks bacaan maksimal sekitar 70 karakter.
- Angka statistik memakai `font-variant-numeric: tabular-nums` agar count-up tidak "bergoyang".

### 2.3 Spasi, Grid, dan Container

Basis spasi **4 px**: `4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96, 128`.

| Token | Nilai |
| --- | --- |
| `--container-max` | `1280px` (Explore dan Map boleh `1440px`) |
| `--container-pad` | `16px` (mobile), `24px` (tablet), `40px` (desktop) |
| `--section-gap` | `64px` (mobile), `96px` (desktop) |
| `--grid-gap` | `16px` (mobile), `24px` (desktop) |

### 2.4 Breakpoint

| Nama | Lebar minimum | Perilaku utama |
| --- | --- | --- |
| `sm` | 640px | Card 2 kolom |
| `md` | 768px | Navbar penuh, section 2 kolom |
| `lg` | 1024px | Sidebar filter tampil permanen, card 3 kolom |
| `xl` | 1280px | Card 4 kolom |

Pendekatan: **mobile-first**.

### 2.5 Radius

| Token | Nilai | Pemakaian |
| --- | --- | --- |
| `--radius-sm` | `8px` | Elemen kecil, tooltip |
| `--radius-md` | `12px` | Input, dropdown |
| `--radius-lg` | `20px` | Panel kecil, popup peta |
| `--radius-xl` | `24px` | **Card heritage** |
| `--radius-2xl` | `32px` | Panel filter navbar, card Discover, panel chatbot |
| `--radius-full` | `9999px` | Tombol, chip, badge, search pill |

### 2.6 Border dan Bayangan

```css
--border: 1px solid var(--color-border);
--shadow-sm: 0 1px 2px rgba(10,10,10,.06);
--shadow-md: 0 8px 24px rgba(10,10,10,.08);   /* hover card */
--shadow-lg: 0 20px 48px rgba(10,10,10,.14);  /* panel mengambang */
```

Bayangan harus lembut dan menyebar. Jangan memakai bayangan hitam pekat atau berwarna.

### 2.7 Ikon

- Pustaka garis tipis dengan gaya konsisten, misalnya **Lucide** (stroke 1.5–1.75).
- Ukuran standar 16, 20, 24 px. Warna mengikuti teks (`currentColor`).
- Contoh ikon: `search`, `map-pin`, `landmark` (Cultural), `leaf` (Natural), `layers` (Mixed), `alert-triangle` (In Danger), `arrow-up-right`, `chevron-left/right`, `message-circle`.

---

## 3. Sistem Animasi

Karakter gerak: **halus, tenang, sengaja, dan sinematik**.

### 3.1 Token gerak

```css
--ease-out:      cubic-bezier(0.22, 1, 0.36, 1);   /* default, cepat lalu melambat */
--ease-in-out:   cubic-bezier(0.65, 0, 0.35, 1);   /* transisi panel/navbar */
--ease-spring:   cubic-bezier(0.34, 1.56, 0.64, 1);/* hanya untuk micro-interaction kecil */

--dur-fast:   200ms;   /* hover kecil, toggle */
--dur-base:   350ms;   /* panel, dropdown, chip */
--dur-slow:   600ms;   /* reveal section */
--dur-hero:   900ms;   /* hero dan transisi besar */
--stagger:    70ms;    /* jeda antar elemen dalam satu grup */
```

### 3.2 Pola animasi

| Pola | Perilaku |
| --- | --- |
| **Reveal saat scroll** | Elemen `opacity 0→1` dan `translateY 32px→0`, durasi `--dur-slow`, `--ease-out`. Dipicu saat 15–20% elemen masuk viewport. Grup elemen memakai stagger. Hanya jalan sekali. |
| **Hover card** | `translateY(-6px)`, bayangan `--shadow-md`, gambar di dalam `scale(1.06)` (overflow hidden), `--dur-base`. |
| **Hover tombol** | Warna latar bergeser halus, ikon panah bergerak 3–4 px, `--dur-fast`. |
| **Hover link** | Garis bawah tumbuh dari kiri ke kanan (`scaleX 0→1`). |
| **Smooth scroll** | Scroll dihaluskan secara global dengan inersia ringan. |
| **Parallax gambar** | Gambar bergerak lebih lambat dari konten (pergeseran maksimal sekitar 8–12%). |
| **Count-up angka** | Angka statistik naik dari 0 ke nilai akhir selama ~1.4 detik dengan `--ease-out`. |
| **Skeleton loading** | Latar `--gray-100` dengan kilau lembut bergerak, lalu card asli muncul dengan stagger. |

### 3.3 Aturan wajib

1. Animasikan hanya `transform` dan `opacity`. Hindari mengubah `width`, `height`, `top`, atau `left` secara langsung.
2. Hormati `prefers-reduced-motion: reduce`: matikan parallax, pin, dan count-up. Ganti reveal dengan fade pendek atau tanpa gerak.
3. Animasi tidak boleh memblokir input. Pengguna harus bisa berinteraksi di tengah animasi.
4. Tidak ada animasi berulang tanpa henti kecuali indikator loading.
5. Pin scroll dan efek berat dinonaktifkan atau disederhanakan di mobile.

**Rekomendasi library [TBD]:** Lenis (smooth scroll), GSAP + ScrollTrigger (hero dan reveal), Motion/Framer Motion (transisi komponen).

---

## 4. Komponen

### 4.1 Tombol

| Varian | Tampilan | Pemakaian |
| --- | --- | --- |
| **Primary** | Latar `--gray-950`, teks putih, pill | Aksi utama (Explore, Search, View Details) |
| **Secondary** | Transparan, border 1.5px hitam, teks hitam, pill | Aksi pendamping (Map) |
| **Ghost** | Tanpa border, teks hitam, hover latar `--gray-100` | Aksi tersier |
| **Inverse** | Latar putih, teks hitam | Di atas section gelap atau gambar |
| **Icon button** | Lingkaran 44px | Next/prev carousel, tutup panel |

- Tinggi: `44px` (default), `52px` (besar/hero), `36px` (kecil).
- Padding horizontal `24px`. Font berat 600.
- State: hover (latar `--gray-800`), active (`scale(0.98)`), focus (outline 2px hitam offset 2px), disabled (opasitas 0.4).

### 4.2 Input, Dropdown, Chip, Toggle

- **Input teks:** tinggi 48px, radius `--radius-md`, border `--gray-300`. Fokus: border hitam 1.5px. Placeholder `--gray-400`.
- **Dropdown/combobox:** panel dengan radius `--radius-md`, bayangan `--shadow-lg`, mendukung pencarian di dalamnya (penting untuk Country yang daftarnya panjang). Opsi terpilih ditandai ikon centang.
- **Chip (filter):** tinggi 36px, pill, border `--gray-300`. Terpilih: latar hitam, teks putih. Transisi `--dur-fast`.
- **Chip filter aktif:** chip hitam dengan ikon `x` untuk menghapus, ditambah tombol teks "Clear all".
- **Toggle (switch):** 44×24px. Aktif: latar hitam, bulatan putih bergeser halus.
- **Range slider (Inscription Year):** dua titik pegangan, garis aktif hitam, garis tidak aktif `--gray-200`. Nilai minimum dan maksimum ditampilkan sebagai teks.

### 4.3 Badge

Karena UI monokrom, perbedaan dibuat lewat **isian, garis, dan ikon**, bukan warna.

| Badge | Tampilan | Ikon |
| --- | --- | --- |
| **Cultural** | Latar hitam, teks putih | `landmark` |
| **Natural** | Latar putih, border hitam 1.5px, teks hitam | `leaf` |
| **Mixed** | Latar `--gray-200`, teks hitam | `layers` |
| **In Danger** | Latar hitam, teks putih, ikon peringatan, titik berdenyut halus | `alert-triangle` |
| **Transboundary** | Outline abu-abu, teks `--gray-600` | `globe` |

- Bentuk pill, tinggi 28px, label `0.75rem` UPPERCASE.
- Pada foto berwarna, badge diberi latar solid (bukan transparan) agar tetap terbaca.
- Badge "In Danger" ditempatkan konsisten di pojok kiri atas gambar card.

### 4.4 Navbar (terinspirasi Airbnb)

**Susunan:** nama website (kiri), search bar (tengah), menu Home, Explore, Map, Discover, About (kanan).

| Kondisi | Tinggi | Search bar | Latar |
| --- | --- | --- | --- |
| **Expanded** (di paling atas) | 88px | Pill lebar (~640px) dengan segmen: Keyword, Country, Category, Year | Putih, tanpa bayangan |
| **Compact** (setelah scroll) | 64px | Pill kecil (~320px) berisi "Search heritage" dan ikon | Putih semi-transparan 85% + blur 12px, bayangan `--shadow-sm` |
| **Search aktif** | 88px | Pill melebar dengan fokus | Panel filter terbuka di bawahnya |

**Animasi transisi expanded ↔ compact:** `--dur-base` (350ms), `--ease-in-out`. Search bar menyusut dan tinggi navbar berkurang secara bersamaan. Gunakan transform sebisa mungkin untuk menjaga performa.

**Panel filter lengkap (saat search bar ditekan):**
- Muncul dari search bar dengan animasi: panel naik dan melebar (`scaleY` dan `opacity`) selama 400ms.
- Latar halaman diredupkan dengan `--overlay-scrim` dan blur 6px.
- Panel: lebar maksimal ~960px, radius `--radius-2xl`, bayangan `--shadow-lg`, padding 32px.
- Isi (sama dengan sidebar Explore): Keyword, Country, Region, Category, Inscription Year, UNESCO Criteria, toggle In Danger, toggle Transboundary.
- Footer panel: "Clear all" (ghost) dan "Search" (primary) yang membuka Explore dengan parameter URL terisi.
- Ditutup dengan klik area gelap, tombol `Esc`, atau tombol `x`.

**Menu aktif:** ditandai garis bawah hitam yang bergeser halus antar item.

**Mobile (< 768px):** nama website di kiri, ikon hamburger di kanan. Search pill ditempatkan sebagai baris kedua. Panel filter berubah menjadi *bottom sheet* layar penuh dengan tombol aksi tetap di bawah.

### 4.5 Hero Section (scroll animation)

Struktur halaman Home berupa satu "adegan" yang berubah saat digulir (container pin setinggi ±300vh, di desktop).

| Tahap | Progres scroll | Yang terjadi |
| --- | --- | --- |
| **1. Pembuka** | 0% | Latar `--gradient-hero`. Tagline Display di sisi kiri atau tengah, satu atau beberapa gambar heritage berbentuk rounded (radius 32px) dengan parallax ringan. Indikator "Scroll" kecil di bawah. |
| **2. Transisi "tertimpa"** | 0–50% | Tagline dan gambar perlahan mengecil (`scale 1→0.9`), memudar, dan sedikit naik. Lapisan statistik naik dari bawah dengan sudut atas membulat (radius 40px) lalu menutupi hero. |
| **3. Statistik** | 50–85% | Angka count-up muncul bertahap (stagger). Setiap statistik: angka besar (H1) dan label kecil UPPERCASE. |
| **4. Penutup** | 85–100% | Dua tombol besar: **Explore** (primary) dan **Map** (secondary/inverse). |

Aturan:
- Statistik diambil dari SPARQL (jumlah heritage dan statistik lain **[TBD]**).
- Panjang scroll tidak melebihi sekitar 3 layar.
- Mobile: tanpa pin; setiap tahap menjadi section biasa dengan reveal saat scroll.
- Tagline **[TBD]**. Gambar hero perlu lisensi yang jelas (Bab 12 PRD).
- Teks di atas gambar wajib memakai `--overlay-image`.

### 4.6 Halaman Explore

**Layout desktop (≥ 1024px):**

```
┌───────────────┬───────────────────────────────────────────────┐
│ Sidebar Filter│  Hasil: "284 heritage"      [chip filter aktif]│
│ (sticky,      │  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐              │
│  lebar 280px) │  │Card │ │Card │ │Card │ │Card │              │
│               │  └─────┘ └─────┘ └─────┘ └─────┘              │
│               │  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐              │
│               │  └─────┘ └─────┘ └─────┘ └─────┘              │
└───────────────┴───────────────────────────────────────────────┘
```

- **Sidebar:** `position: sticky`, top = tinggi navbar compact + 24px. Latar putih, border kanan `--gray-200`. Setiap grup filter berupa section yang dapat dibuka-tutup dengan animasi tinggi halus.
- **Urutan filter:** Keyword, Country, Region, Category, Inscription Year, UNESCO Criteria, World Heritage in Danger, Transboundary.
- **Criteria (i–x):** chip berukuran kecil dalam grid, mendukung pilihan ganda.
- **Logika filter:** pilihan ganda dalam satu filter bersifat OR, antar filter bersifat AND.
- **Default:** tanpa keyword dan filter, grid menampilkan **card acak** dari SPARQL. Hasil acak disimpan selama sesi agar tidak berubah saat pengguna kembali dari halaman detail.
- **Grid card:** 4 kolom di `xl`, 3 di `lg`, 2 di `sm`, 1 di bawahnya. Jarak `--grid-gap`.
- **Tampilan jumlah hasil** dan baris chip filter aktif di atas grid.
- **Keadaan khusus:** skeleton saat memuat, dan tampilan "Tidak ada hasil" dengan ilustrasi garis sederhana dan tombol "Clear filters".
- **Mobile:** sidebar menjadi tombol "Filters" (dengan jumlah filter aktif) yang membuka bottom sheet.

### 4.7 Card Heritage

Satu komponen yang dipakai di Explore, Discover (hasil), dan Related Heritage.

```
┌──────────────────────────┐
│ [In Danger]              │  ← badge di pojok kiri atas gambar
│        GAMBAR            │  ← rasio 4:3, radius atas 24px
│                          │
├──────────────────────────┤
│ 📍 Indonesia · Asia-Pacific │  ← meta kecil
│ Borobudur Temple         │  ← H4, maksimal 2 baris
│ Compounds                │
│ [Cultural]   1991        │  ← badge kategori + tahun
│ (i) (ii) (vi)            │  ← chip criteria kecil
│                       ↗  │  ← tombol View Details
└──────────────────────────┘
```

| Properti | Nilai |
| --- | --- |
| Radius | `--radius-xl` (24px), seluruh sudut membulat |
| Border | `1px solid --gray-200` |
| Padding konten | 16–20px |
| Rasio gambar | 4:3, `object-fit: cover` |
| Judul | H4, dibatasi 2 baris (`line-clamp: 2`) |
| Indikasi connected heritage | Teks kecil "12 connections" bila data tersedia |
| Tombol detail | Ikon `arrow-up-right` dalam lingkaran 40px, melebar menjadi "View Details" saat hover (desktop) |

**State:**
- **Default:** border tipis, tanpa bayangan.
- **Hover:** naik 6px, `--shadow-md`, gambar zoom 1.06, tombol detail aktif.
- **Focus (keyboard):** outline 2px hitam offset 3px.
- **Loading:** skeleton dengan proporsi sama.

**Perlakuan foto [TBD]:** default menampilkan foto berwarna. Opsi lain: foto hitam-putih yang menjadi berwarna saat hover (`filter: grayscale(1)` → `none`, transisi 400ms). Pilih salah satu dan terapkan konsisten.

### 4.8 Halaman Heritage Detail

Gabungan nuansa naratif Atlas Obscura dan struktur informasi UNESCO. Halaman dibagi ke section yang bergantian latar putih dan `--gray-50`.

| Urutan | Section | Catatan desain |
| --- | --- | --- |
| 1 | **Hero** | Gambar besar (tinggi ±70vh), sudut bawah membulat 32px, `--overlay-image`. Di atasnya: breadcrumb kecil, nama (H1/Display), negara, badge kategori dan In Danger. Tombol "View on Map". |
| 2 | **Quick Information** | Panel ringkas: negara, region, category, tahun inscription, criteria. Di desktop berupa kolom *sticky* di sisi kanan; di mobile berupa blok di atas About. |
| 3 | **About** | Deskripsi, lebar teks maksimal ~70 karakter per baris. |
| 4 | **Outstanding Universal Value** | Teks berukuran besar (Body besar) dengan garis vertikal hitam di kiri sebagai penekanan. |
| 5 | **UNESCO Criteria** | Daftar accordion. Tiap kriteria: kode (i, ii, ...) dalam lingkaran hitam, judul, dan deskripsi yang terbuka dengan animasi tinggi halus. |
| 6 | **Location** | Mini map monokrom dengan satu marker, koordinat dalam teks, tautan "View on Map". |
| 7 | **Connected Knowledge** | Daftar baris tautan (Country, Region, Category, Criteria, entitas eksternal); setiap baris memiliki ikon panah dan efek garis bawah saat hover. |
| 8 | **Related Heritage** | Lihat 4.9. |

**Penanda section:** setiap judul section diawali indeks editorial kecil, misalnya `03 — ABOUT` (label UPPERCASE) diikuti H2.

**Animasi:** reveal per section saat digulir, parallax ringan pada gambar hero, dan accordion yang halus. *Sticky section navigation* (daftar tautan section di atas halaman) bersifat opsional **[TBD]**.

**Metadata:** halaman memuat RDFa dan Open Graph (lihat PRD, DET-5).

### 4.9 Related Heritage

- Ditempatkan di bagian bawah Heritage Detail, memakai **komponen Card yang sama** (4.7).
- Tampilan: grid 4 kolom di desktop, atau baris yang dapat digeser horizontal (scroll-snap) di layar kecil.
- Tiap card diberi label alasan relasi di atas gambar atau bawah judul, misalnya "Same country" atau "Shared criteria", dalam chip kecil.
- Judul section: H2 "Related Heritage", dengan indeks section seperti section lain.

### 4.10 Interactive Map

| Aspek | Spesifikasi |
| --- | --- |
| **Layout** | Peta penuh lebar di bawah navbar (tinggi `100vh − tinggi navbar`). |
| **Tile** | Gaya terang monokrom (abu-abu) agar menyatu dengan UI. Penyedia tile dan lisensinya **[TBD]**. |
| **Marker** | Lingkaran 14px, latar hitam, ring putih 2px. Bentuk dibedakan kecil per kategori (isian untuk Cultural, outline untuk Natural, setengah terisi untuk Mixed). Marker In Danger memiliki ring luar berdenyut halus. |
| **Cluster** | Lingkaran hitam dengan angka putih; ukuran tumbuh mengikuti jumlah. Klik memperbesar peta dengan animasi *fly-to* halus. |
| **Popup** | Card mini radius `--radius-lg`, lebar ±280px: gambar kecil, nama, negara, badge kategori, tautan "View Details". Muncul dengan `scale 0.95→1` dan fade. |
| **Filter** | Panel pill mengambang di kiri atas: Category, Country, In Danger. Panel dapat diciutkan. |
| **Pencarian lokasi** | Kolom pencarian mengambang di tengah atas. Saat memilih hasil, peta terbang ke lokasi. |
| **Kontrol zoom** | Tombol bulat putih bayangan lembut di kanan bawah. |
| **Mobile** | Panel filter menjadi tombol yang membuka bottom sheet. Popup tampil sebagai card di bagian bawah layar. |

Fitur tambahan dari peta UNESCO selain yang ada di PRD **[TBD]**.

### 4.11 Discover (carousel melengkung)

Terinspirasi carousel Rijksmuseum Boerhaave, dengan ukuran lebih ringkas.

```
        ┌───┐
   ┌───┐│   │┌───┐
 ┌─┘   ││ ★ ││   └─┐
 │ ... ││   ││ ... │      ← card aktif di tengah, sisi mengecil
 └─────┘└───┘└─────┘        dan melengkung ke bawah (arc)
        ◀    ▶
```

| Properti | Nilai |
| --- | --- |
| Card | Lebar ±300px, tinggi ±420px, radius `--radius-2xl`, gambar penuh dengan `--overlay-image` |
| Isi | Label kecil (`01 — BY COUNTRY`), judul besar (H3, putih), jumlah heritage |
| Posisi relatif ke tengah (offset `n`) | `scale = 1 − 0.12·|n|`, `rotateZ = 6°·n`, `translateY = 16px·n²`, `opacity = 1 − 0.25·|n|` |
| Kontrol | Tombol bulat prev/next (48px), geser dengan drag atau swipe, tombol panah keyboard, indikator titik |
| Animasi | Perpindahan 600ms `--ease-out` dengan snap ke card terdekat |
| Hover (desktop) | Card aktif naik 8px dan gambar zoom ringan |
| Klik | Membuka Explore dengan filter terisi (atau langkah pemilihan nilai **[TBD]**) |

Pilihan Discover: By Country, By Region, By Category, By Criteria, dan World Heritage in Danger.

Aksesibilitas: carousel dapat dioperasikan dengan keyboard, card aktif diumumkan ke pembaca layar (`aria-live`), dan tombol prev/next memiliki label.

### 4.12 Halaman About

- Tiga section: **About HeritageFinder**, **Data Sources**, **Semantic Web**. Latar bergantian putih, `--gray-50`, dan satu section gelap (`--gradient-dark`) dengan teks putih.
- Setiap section: label indeks, H1/H2 besar, paragraf pendek, dan reveal dengan stagger.
- **Data Sources:** grid card sumber data (UNESCO dan dataset eksternal) dengan nama dan deskripsi singkat.
- **Semantic Web:** diagram alur horizontal (Dataset → RDF → Apache Jena Fuseki → SPARQL → HeritageFinder). Node muncul berurutan dan garis penghubung tergambar (animasi `stroke-dashoffset`). Di mobile berubah menjadi alur vertikal.
- Navigasi section (tautan anchor) berupa daftar kecil yang menandai section aktif.

### 4.13 Chatbot "Ask Heritage"

| Elemen | Spesifikasi |
| --- | --- |
| **Floating button** | Lingkaran 56px, latar hitam, ikon putih, kanan bawah (24px dari tepi), bayangan `--shadow-lg`. Tooltip "Ask Heritage" saat hover. |
| **Panel (desktop)** | 380×560px, radius `--radius-2xl`, muncul dari posisi tombol dengan `scale 0.9→1` dan fade 350ms. |
| **Panel (mobile)** | Layar penuh sebagai bottom sheet. |
| **Header** | Judul "Ask Heritage", konteks halaman aktif (misalnya "Borobudur Temple Compounds") dalam chip, tombol tutup. |
| **Bubble pengguna** | Latar hitam, teks putih, radius 20px (sudut kanan bawah lebih kecil). |
| **Bubble chatbot** | Latar `--gray-100`, teks hitam. |
| **Aksi lanjutan** | Chip: **View Result**, **Show on Map**, **Open Heritage**. |
| **Loading** | Tiga titik berdenyut halus. |
| **Contoh pertanyaan** | Chip saran di awal percakapan, misalnya "Cari heritage di Indonesia". |

### 4.14 Footer

Latar `--gray-950`, teks putih dan `--gray-400`. Berisi nama website, tautan Explore, Map, Discover, About, serta atribusi sumber data (UNESCO dan dataset eksternal). Tata letak 3–4 kolom di desktop dan satu kolom di mobile.

---

## 5. Gambar dan Konten Visual

- Foto heritage **boleh berwarna**. Warna foto menjadi satu-satunya warna di halaman; elemen UI tetap monokrom.
- Rasio standar: card 4:3, hero detail 16:9 (atau lebih lebar), Discover 3:4.
- `object-fit: cover`, `loading="lazy"` (kecuali gambar di layar pertama), sediakan placeholder abu-abu (`--gray-100`) atau blur sebelum gambar dimuat.
- Foto tidak diberi filter warna tambahan, kecuali opsi grayscale hover **[TBD]**.
- Setiap gambar wajib memiliki teks alternatif (`alt`) yang deskriptif. Atribusi dan lisensi gambar **[TBD]**.
- Jika gambar tidak tersedia: tampilkan placeholder abu-abu dengan ikon `landmark`, bukan kotak kosong.

---

## 6. Perilaku Responsif

| Komponen | Mobile (<768) | Tablet (768–1023) | Desktop (≥1024) |
| --- | --- | --- | --- |
| Navbar | Hamburger + search pill baris kedua | Menu penuh, search pill kompak | Expanded ↔ compact |
| Panel filter navbar | Bottom sheet layar penuh | Panel tengah | Panel dropdown lebar |
| Hero | Tanpa pin, reveal biasa | Pin disederhanakan | Scroll animation penuh |
| Explore | Filters via bottom sheet, 1 kolom | Filters via sheet, 2 kolom | Sidebar sticky, 3–4 kolom |
| Card | Satu kolom, penuh lebar | 2 kolom | 3–4 kolom |
| Detail | Quick Info di atas About | Dua kolom | Dua kolom, Quick Info sticky |
| Map | Filter dan popup via sheet | Panel mengambang kecil | Panel mengambang |
| Discover | Satu card tampak, swipe | Tiga card tampak | Lima card tampak dengan lengkung |
| Chatbot | Layar penuh | Panel 380px | Panel 380px |

Target sentuh minimal 44×44px. Hover tidak boleh menjadi satu-satunya cara mengakses fungsi (sediakan alternatif tap).

---

## 7. Aksesibilitas

- **Kontras:** teks utama hitam di atas putih memenuhi WCAG AA/AAA. Teks di atas gambar wajib memakai overlay agar kontras minimal 4.5:1.
- **Fokus:** semua elemen interaktif memiliki indikator fokus yang jelas (outline 2px hitam, offset 2px; putih pada latar gelap).
- **Keyboard:** navbar, panel filter (fokus terjebak di dalam panel dan `Esc` menutup), accordion, carousel Discover, dan chatbot dapat dioperasikan penuh dengan keyboard.
- **Pembaca layar:** gunakan elemen semantik (`nav`, `main`, `section`, `button`), `aria-expanded` pada accordion dan panel, `aria-live` pada hasil pencarian dan carousel.
- **Tanpa warna sebagai satu-satunya penanda:** status dan kategori memakai ikon, isian, dan teks.
- **Gerak:** hormati `prefers-reduced-motion`.
- **Bahasa:** atribut `lang` diatur sesuai bahasa antarmuka **[TBD]**.

---

## 8. Performa Visual

- Muat font dengan `font-display: swap`, batasi 2–3 varian berat.
- Gambar: gunakan format modern (WebP/AVIF), ukuran responsif (`srcset`), dan dimensi tetap untuk mencegah *layout shift*.
- Animasi hanya dengan `transform` dan `opacity`; gunakan `will-change` secukupnya.
- Jangan menjalankan banyak ScrollTrigger sekaligus pada satu viewport; cukup satu pin besar (hero) per halaman.
- Query SPARQL yang berat (peta, statistik) dimuat dengan indikator loading dan, jika memungkinkan, di-cache.

---

## 9. Implementasi (Ringkas)

### 9.1 Token CSS dasar

```css
:root {
  /* warna */
  --gray-0:#FFFFFF; --gray-50:#FAFAFA; --gray-100:#F5F5F5; --gray-200:#E5E5E5;
  --gray-300:#D4D4D4; --gray-400:#A3A3A3; --gray-500:#737373; --gray-600:#525252;
  --gray-800:#262626; --gray-900:#171717; --gray-950:#0A0A0A;

  /* radius */
  --radius-sm:8px; --radius-md:12px; --radius-lg:20px;
  --radius-xl:24px; --radius-2xl:32px; --radius-full:9999px;

  /* gerak */
  --ease-out:cubic-bezier(0.22,1,0.36,1);
  --ease-in-out:cubic-bezier(0.65,0,0.35,1);
  --dur-fast:200ms; --dur-base:350ms; --dur-slow:600ms; --dur-hero:900ms;
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

### 9.2 Pola reveal

```css
.reveal { opacity: 0; transform: translateY(32px);
  transition: opacity var(--dur-slow) var(--ease-out),
              transform var(--dur-slow) var(--ease-out); }
.reveal.is-visible { opacity: 1; transform: none; }
```

Tambahkan kelas `is-visible` melalui `IntersectionObserver`, dan beri `transition-delay` bertahap (kelipatan `--stagger`) untuk grup elemen.

### 9.3 Struktur komponen yang disarankan

`Navbar`, `SearchPanel`, `Hero`, `StatCounter`, `FilterSidebar`, `FilterChip`, `HeritageCard`, `Badge`, `Accordion`, `MapView`, `MapPopup`, `DiscoverCarousel`, `ChatWidget`, `SectionHeading`, `Footer`.

---

## 10. Do dan Don't

| Lakukan | Hindari |
| --- | --- |
| Biarkan foto menjadi satu-satunya sumber warna | Menambah warna aksen pada tombol, tautan, atau badge |
| Pakai heading tebal dengan ukuran kontras | Memakai banyak berat dan ukuran huruf tanpa sistem |
| Gunakan satu komponen Card di seluruh situs | Membuat gaya card berbeda di tiap halaman |
| Animasi halus dengan easing konsisten | Animasi cepat, memantul, atau berulang tanpa henti |
| Gradient abu-abu ke hitam untuk latar dan overlay | Gradient berwarna atau gradient pada teks dan tombol |
| Pakai ikon, isian, dan teks untuk membedakan status | Mengandalkan warna saja untuk status |
| Ruang kosong yang cukup antar section | Memadatkan konten atau membuat elemen berukuran raksasa |
| Sediakan alternatif untuk animasi (reduced motion) | Memaksa pin dan parallax di semua perangkat |

---

## 11. Keputusan yang Masih Terbuka

Mengacu pada Bab 12 PRD:

- Font final dan library animasi/smooth scroll.
- Tagline hero, statistik yang ditampilkan, jumlah dan pemilihan gambar hero.
- Perlakuan foto: berwarna, hitam-putih, atau hitam-putih yang berwarna saat hover.
- Tile peta monokrom dan lisensinya, serta fitur peta UNESCO yang diadopsi.
- Perilaku klik pada Discover: langsung ke Explore terfilter atau ada langkah memilih nilai.
- Sticky section navigation pada Heritage Detail.
- Bahasa antarmuka (Indonesia, Inggris, atau keduanya).
- Sumber, atribusi, dan lisensi gambar heritage.
