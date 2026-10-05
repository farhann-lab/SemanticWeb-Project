# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## HeritageFinder

**STATUS: DRAFT SEMENTARA**

| | |
| --- | --- |
| **Nama Produk** | HeritageFinder — Semantic Explorer for World Heritage Sites |
| **Versi Dokumen** | v0.3 (Draft Sementara) |
| **Disusun oleh** | Tim Pengembang HeritageFinder (Pengembang) |
| **Untuk** | Pemberi Tugas / Penilai Mata Kuliah (Klien) |
| **Tanggal** | 3 Oktober 2026 |
| **Dokumen Terkait** | Dokumen Riset & Gambaran Produk HeritageFinder (HeritageFinder.md); Brief Arah Gaya Desain UI/UX (hasil diskusi) |

---

# 1. Ringkasan Produk (Overview)

Informasi tentang situs warisan dunia (World Heritage Sites) UNESCO tersebar dalam berbagai sumber dan umumnya hanya dapat ditelusuri melalui pencarian berbasis kata kunci pada daftar yang bersifat statis. Hubungan antar situs, misalnya kesamaan kriteria, kategori, wilayah, atau keterkaitan dengan entitas sejarah dan budaya, sulit diketahui pengguna karena data tidak saling terhubung. Selain itu, pengguna yang ingin menjelajah tanpa kata kunci tertentu hanya memiliki sedikit pilihan.

HeritageFinder adalah website pencarian dan eksplorasi World Heritage Sites berbasis Semantic Web. Data utama bersumber dari UNESCO, dimodelkan sebagai RDF, disimpan pada Apache Jena Fuseki, lalu dihubungkan dengan dataset eksternal sehingga membentuk knowledge graph. Website menyediakan halaman Home, Explore (pencarian dan filter), Heritage Detail, Related Heritage, Interactive Map, Discover, dan chatbot Ask Heritage yang tersedia di seluruh halaman. Seluruh data ditampilkan melalui query SPARQL, dengan tujuan membuat pengguna dapat menemukan, memahami, dan menelusuri hubungan antar heritage secara intuitif. Antarmuka dirancang bergaya monokrom, minimalis, dan modern dengan animasi yang halus (smooth scroll, animasi hover, dan transisi), serta responsif pada berbagai ukuran layar.

# 2. Tujuan & Sasaran (Goals)

- Menyediakan satu pintu pencarian dan eksplorasi World Heritage Sites yang mudah digunakan melalui keyword dan filter.
- Menghubungkan data UNESCO dengan dataset eksternal agar informasi heritage menjadi lebih kaya dan saling terhubung.
- Memperlihatkan hubungan antar heritage, negara, wilayah, kategori, dan kriteria melalui Connected Knowledge dan Related Heritage, dengan visualisasi knowledge graph menyusul pada fase lanjutan.
- Menyediakan cara eksplorasi alternatif tanpa kata kunci, yaitu berdasarkan negara, wilayah, kategori, kriteria, dan status "in danger".
- Menampilkan sebaran geografis heritage melalui peta interaktif.
- Memberikan akses informasi melalui bahasa natural lewat chatbot yang jawabannya bersumber dari knowledge graph, bukan dari jawaban bebas.
- Menghadirkan pengalaman visual yang konsisten, minimalis, dan modern dalam palet monokrom dengan animasi yang halus dan responsif.
- Memenuhi persyaratan komponen Semantic Web (RDF, RDFa, Open Graph Protocol, SPARQL endpoint, integrasi dataset dan layanan eksternal) serta memaksimalkan penggunaan query SPARQL.

# 3. Pengguna & Peran (Users & Roles)

- **Pengunjung (Pengguna Umum) :** Siapa pun yang mengakses website tanpa perlu login. Dapat mencari dan memfilter heritage, melihat detail, menelusuri related heritage dan connected knowledge, membuka peta, menjelajah melalui Discover, serta bertanya kepada chatbot Ask Heritage.
- **Pengelola Data / Pengembang :** Pihak yang menyiapkan dan memelihara dataset, ontologi, RDF, serta SPARQL endpoint (Apache Jena Fuseki). Peran ini bekerja di sisi data dan tidak memiliki panel administrasi pada website (lihat asumsi pada Bab 5).

# 4. Ruang Lingkup (Scope)

## 4.1 Termasuk (MVP)

- Navbar pada seluruh halaman dengan search bar yang membuka panel filter lengkap dan animasi saat scroll.
- Home: hero section dengan scroll animation (tagline, gambar heritage, statistik, dan tombol menuju Explore dan Map).
- Explore: sidebar filter di sisi kiri, card heritage acak sebagai tampilan awal, dan hasil pencarian dalam bentuk card.
- Heritage Detail beserta Connected Knowledge (daftar hubungan) dan Related Heritage.
- Interactive Map dengan marker, popup, dan filter.
- Discover dalam bentuk carousel card melengkung: By Country, By Region, By Category, By Criteria, dan World Heritage in Danger.
- Ask Heritage Chatbot yang tersedia di seluruh halaman, termasuk kesadaran konteks halaman.
- Halaman About (About HeritageFinder, Data Sources, Semantic Web) yang dibagi per section dengan animasi scroll.
- Gaya visual monokrom minimalis dengan animasi halus dan tampilan responsif pada seluruh halaman.
- Komponen Semantic Web: RDF/RDFa, Open Graph Protocol, SPARQL endpoint, integrasi dataset eksternal, dan integrasi layanan eksternal untuk peta.

## 4.2 Di Luar Lingkup Awal / Fase Lanjutan

Fitur berikut dikerjakan hanya setelah alur Search → Detail → Knowledge → Map → Chatbot berjalan dengan baik: Knowledge Graph (visualisasi hubungan antar entitas), Compare Heritage, Historical Period, Advanced Semantic Search, Recommendation, serta Favorite / Saved Heritage. Rincian ada pada Bab 11.

# 5. Asumsi & Batasan (Assumptions & Constraints)

- Data utama bersumber dari UNESCO dan diubah menjadi dataset RDF buatan sendiri (sesuai dokumen riset).
- Apache Jena Fuseki digunakan sebagai triple store dan penyedia SPARQL endpoint.
- Seluruh data yang tampil di website (termasuk statistik dan featured heritage di Home) diambil melalui SPARQL, tidak ditulis langsung (hardcoded).
- Peta menggunakan Leaflet dengan OpenStreetMap; MapLibre menjadi alternatif.
- Chatbot menggunakan data knowledge graph sebagai sumber fakta dan tidak menghasilkan jawaban secara bebas.
- Pengerjaan mengikuti urutan fase: (1) Dataset, Ontology, RDF, Fuseki; (2) Home, Header, Explore, Card; (3) Search, Filter, Detail; (4) Related Heritage dan Connected Knowledge; (5) Interactive Map; (6) Integrasi dataset eksternal; (7) Chatbot; (8) Fitur tambahan, termasuk Knowledge Graph.
- **Asumsi pengembang:** website bersifat publik dan hanya-baca (read-only) tanpa login atau akun pengguna.
- **Asumsi pengembang:** "Klien" pada dokumen ini adalah pemberi tugas/penilai mata kuliah, karena dokumen riset merujuk pada "requirement tugas".
- **Asumsi pengembang:** tidak ada panel admin; pembaruan data dilakukan di sisi dataset/Fuseki.
- Arah desain: monokrom (hitam, putih, abu-abu), minimalis, dan modern; gradient hanya berupa gradasi abu-abu ke hitam. Referensi layout: Atlas Obscura, Vilcek Foundation, dan Smithsonian. Referensi animasi: Rijksmuseum Boerhaave (tanpa mengikuti layoutnya yang berukuran besar). Referensi navbar: Airbnb.
- **Asumsi pengembang:** smooth scroll dan animasi berbasis scroll diimplementasikan dengan library animasi; pilihan library belum ditetapkan (lihat Bab 12).
- **Asumsi pengembang:** panel filter pada navbar dan sidebar filter pada Explore memakai kondisi filter yang sama melalui parameter URL.
- Penggunaan layanan pihak ketiga (peta, dataset eksternal, chatbot) dapat memiliki batasan akses atau biaya tambahan yang belum diketahui.

# 6. Kebutuhan Fungsional (Functional Requirements)

## 6.1 Pengunjung — Navigasi (Navbar)

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **NAV-1** | Sistem menampilkan navbar pada seluruh halaman dengan nama website di kiri, search bar di tengah, dan menu Home, Explore, Map, Discover, dan About di kanan. | **Wajib** |
| **NAV-2** | Navbar berubah dari tampilan lebar menjadi tampilan ringkas dengan animasi halus ketika halaman digulir, dan kembali melebar saat berada di bagian atas. | **Wajib** |
| **NAV-3** | Ketika search bar ditekan, sistem menampilkan panel filter lengkap dengan animasi halus. | **Wajib** |
| **NAV-4** | Panel filter pada navbar memuat filter yang sama dengan Explore: keyword, Country, Region, Category, Inscription Year, UNESCO Criteria, World Heritage in Danger, dan Transboundary. | **Wajib** |
| **NAV-5** | Setelah pengunjung menjalankan pencarian dari panel filter, sistem membuka halaman Explore dengan filter yang sudah terisi. | **Wajib** |
| **NAV-6** | Pada layar kecil, navbar dan panel filter menyesuaikan tampilan (menu ringkas dan panel yang dapat dibuka-tutup). | **Wajib** |

## 6.2 Pengunjung — Home

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **HOME-1** | Sistem menampilkan hero section berisi tagline website dan gambar dari satu atau beberapa heritage yang diambil melalui SPARQL. | **Wajib** |
| **HOME-2** | Hero section menggunakan scroll animation: konten awal (tagline dan gambar) perlahan tertimpa saat halaman digulir, lalu bagian statistik muncul dengan animasi halus. | **Wajib** |
| **HOME-3** | Sistem menampilkan statistik dari database (jumlah heritage dan statistik lainnya) yang diambil melalui SPARQL. | **Wajib** |
| **HOME-4** | Pada bagian paling bawah hero section, sistem menyediakan tombol menuju halaman Explore dan Map. | **Wajib** |

## 6.3 Pengunjung — Explore (Pencarian & Filter)

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **EXP-1** | Pengunjung dapat mencari heritage menggunakan keyword. | **Wajib** |
| **EXP-2** | Pengunjung dapat memfilter hasil berdasarkan Country. | **Wajib** |
| **EXP-3** | Pengunjung dapat memfilter hasil berdasarkan Region. | **Wajib** |
| **EXP-4** | Pengunjung dapat memfilter hasil berdasarkan Category (Cultural, Natural, Mixed). | **Wajib** |
| **EXP-5** | Pengunjung dapat memfilter hasil berdasarkan Inscription Year. | **Wajib** |
| **EXP-6** | Pengunjung dapat memfilter hasil berdasarkan UNESCO Criteria. | **Wajib** |
| **EXP-7** | Pengunjung dapat memfilter heritage yang berstatus World Heritage in Danger. | **Wajib** |
| **EXP-8** | Pengunjung dapat memfilter heritage Transboundary / Transnational. | **Penting** |
| **EXP-9** | Sistem menerjemahkan keyword dan filter menjadi query SPARQL dan menampilkan hasilnya. | **Wajib** |
| **EXP-10** | Halaman Explore dapat dibuka dengan filter yang sudah terisi melalui parameter URL (contoh: Explore?country=Indonesia). | **Wajib** |
| **EXP-11** | Halaman Explore menampilkan section filter langsung di sisi kiri dan hasil di sisi kanan. | **Wajib** |
| **EXP-12** | Tanpa keyword dan filter, halaman Explore menampilkan card heritage acak sebagai tampilan awal. | **Wajib** |
| **EXP-13** | Sistem menampilkan filter yang sedang aktif beserta cara menghapusnya (per filter maupun semua sekaligus). | **Penting** |
| **EXP-14** | Pada layar kecil, section filter dapat dibuka dan ditutup melalui tombol agar tidak memenuhi layar. | **Wajib** |

## 6.4 Pengunjung — Search Results

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **RES-1** | Sistem menampilkan hasil pencarian dalam bentuk card. | **Wajib** |
| **RES-2** | Setiap card menampilkan gambar, nama heritage, negara, region, kategori, tahun inscription, dan UNESCO criteria. | **Wajib** |
| **RES-3** | Setiap card menampilkan jumlah atau indikasi connected heritage. | **Penting** |
| **RES-4** | Setiap card memiliki tombol "View Details" yang membuka Heritage Detail. | **Wajib** |
| **RES-5** | Card berbentuk sudut membulat dan ditampilkan empat card per baris pada layar lebar (menyesuaikan jumlah kolom pada layar yang lebih kecil). | **Wajib** |
| **RES-6** | Card memiliki animasi hover yang halus. | **Penting** |

## 6.5 Pengunjung — Heritage Detail

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **DET-1** | Sistem menampilkan halaman detail dengan bagian Hero, Quick Information, About, Outstanding Universal Value, UNESCO Criteria, Location, Connected Knowledge, dan Related Heritage. | **Wajib** |
| **DET-2** | Sistem menampilkan nama heritage, negara, region, category, tahun inscription, criteria, deskripsi, Outstanding Universal Value, dan koordinat lokasi. | **Wajib** |
| **DET-3** | Pengunjung dapat menekan tombol "View on Map" untuk melihat lokasi heritage pada peta. | **Wajib** |
| **DET-4** | Bagian Connected Knowledge menampilkan hubungan heritage (country, region, category, criteria, dan entitas dari dataset eksternal) dalam bentuk daftar yang dapat diklik. | **Wajib** |
| **DET-5** | Sistem menyematkan metadata RDFa dan Open Graph Protocol pada halaman detail. | **Wajib** |
| **DET-6** | Konten halaman detail dipisahkan menjadi beberapa section yang muncul dengan animasi halus saat digulir. | **Wajib** |

## 6.6 Pengunjung — Related Heritage

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **REL-1** | Sistem menampilkan daftar heritage yang berhubungan dengan heritage yang sedang dibuka. | **Wajib** |
| **REL-2** | Relasi ditentukan berdasarkan Country, Region, Category, Criteria, Theme, dan relasi dari dataset eksternal melalui data RDF dan query SPARQL. | **Wajib** |
| **REL-3** | Pengunjung dapat memilih heritage terkait untuk membuka Heritage Detail lainnya. | **Wajib** |
| **REL-4** | Related Heritage ditempatkan pada halaman Heritage Detail (tidak berupa halaman terpisah) dan memakai card yang sama dengan hasil pencarian Explore. | **Wajib** |

## 6.7 Pengunjung — Interactive Map

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **MAP-1** | Sistem menampilkan peta dunia dengan marker heritage berdasarkan latitude dan longitude hasil SPARQL. | **Wajib** |
| **MAP-2** | Pengunjung dapat membuka popup marker yang berisi ringkasan heritage. | **Wajib** |
| **MAP-3** | Popup menyediakan tautan menuju Heritage Detail. | **Wajib** |
| **MAP-4** | Pengunjung dapat memfilter marker berdasarkan category, country, dan status in-danger. | **Wajib** |
| **MAP-5** | Pengunjung dapat mencari lokasi pada peta. | **Penting** |

## 6.8 Pengunjung — Discover

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **DSC-1** | Sistem menyediakan jalur eksplorasi By Country. | **Wajib** |
| **DSC-2** | Sistem menyediakan jalur eksplorasi By Region. | **Wajib** |
| **DSC-3** | Sistem menyediakan jalur eksplorasi By Category. | **Wajib** |
| **DSC-4** | Sistem menyediakan jalur eksplorasi By Criteria. | **Wajib** |
| **DSC-5** | Sistem menyediakan jalur eksplorasi World Heritage in Danger. | **Wajib** |
| **DSC-6** | Setiap pilihan Discover mengarahkan pengunjung ke hasil Explore yang sudah terfilter. | **Wajib** |
| **DSC-7** | Halaman Discover menampilkan pilihan dalam bentuk carousel card bergaya melengkung yang dapat digeser ke berikutnya dan sebelumnya. | **Wajib** |

## 6.9 Pengunjung — Ask Heritage Chatbot

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **BOT-1** | Sistem menampilkan floating button Ask Heritage pada seluruh halaman. | **Wajib** |
| **BOT-2** | Chatbot dapat menjawab pertanyaan pencarian (contoh: "Cari heritage di Indonesia."). | **Wajib** |
| **BOT-3** | Chatbot dapat menjawab pertanyaan dengan filter (contoh: "Cari cultural heritage di Asia sebelum tahun 2000."). | **Wajib** |
| **BOT-4** | Chatbot dapat menjawab pertanyaan pengetahuan (contoh: "Apa kriteria Borobudur?"). | **Wajib** |
| **BOT-5** | Chatbot dapat menjawab pertanyaan relasi (contoh: "Heritage apa yang memiliki kriteria yang sama?"). | **Penting** |
| **BOT-6** | Chatbot dapat menampilkan hasil di peta (contoh: "Tampilkan heritage Indonesia di map."). | **Penting** |
| **BOT-7** | Chatbot memproses pertanyaan menjadi intent/query SPARQL, mengambil fakta dari knowledge graph, lalu menyusun jawaban bahasa natural. | **Wajib** |
| **BOT-8** | Chatbot mengenali konteks halaman yang sedang dibuka (contoh: Borobudur Temple Compounds) sehingga pertanyaan lanjutan seperti "Apa kriterianya?" dapat dijawab. | **Penting** |
| **BOT-9** | Setelah menjawab, chatbot menyediakan aksi lanjutan: View Result, Show on Map, dan Open Heritage. | **Penting** |

## 6.10 Pengunjung — About & Semantic Web

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **ABT-1** | Sistem menyediakan halaman About HeritageFinder. | **Wajib** |
| **ABT-2** | Sistem menyediakan halaman Data Sources yang menjelaskan sumber data UNESCO dan dataset eksternal. | **Wajib** |
| **ABT-3** | Sistem menyediakan halaman Semantic Web yang menjelaskan penggunaan RDF, RDFa, Open Graph, dan SPARQL. | **Penting** |
| **ABT-4** | Halaman About dibagi per section dengan animasi scroll yang halus. | **Wajib** |

## 6.11 Sistem — Semantic Web & Data

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **SEM-1** | Dataset UNESCO dimodelkan dengan ontologi dan dikonversi menjadi dataset RDF buatan sendiri. | **Wajib** |
| **SEM-2** | Dataset RDF disimpan pada Apache Jena Fuseki dan diakses melalui SPARQL endpoint. | **Wajib** |
| **SEM-3** | Sistem terintegrasi dengan SPARQL endpoint eksternal dan dataset eksternal untuk memperkaya data heritage. | **Wajib** |
| **SEM-4** | Sistem menggunakan layanan eksternal untuk peta. | **Wajib** |
| **SEM-5** | Halaman publik memuat markup RDFa dan Open Graph Protocol. | **Wajib** |
| **SEM-6** | Seluruh pengambilan data pada website memaksimalkan penggunaan query SPARQL. | **Wajib** |

## 6.12 Pengunjung — Fitur Tambahan

| **ID** | **Kebutuhan Fungsional** | **Prioritas** |
| --- | --- | --- |
| **OPT-1** | Sistem menampilkan hubungan antar entitas secara visual dalam bentuk knowledge graph, mencakup relasi Heritage → Country, Region, Category, Criteria, entitas historis/budaya, dan Related Heritage. | **Fase 2** |
| **OPT-2** | Pengunjung dapat menekan tombol "Explore Connections" pada Heritage Detail untuk membuka knowledge graph, dan berpindah dari node heritage pada graf ke Heritage Detail terkait. | **Fase 2** |
| **OPT-3** | Pengunjung dapat membandingkan dua atau lebih heritage berdasarkan country, region, category, inscription year, criteria, dan informasi terkait. | **Fase 2** |
| **OPT-4** | Pengunjung dapat menjelajah heritage berdasarkan periode sejarah (Ancient, Medieval, Early Modern, Modern). | **Fase 2** |
| **OPT-5** | Pengunjung dapat mencari menggunakan bahasa natural yang kompleks yang diterjemahkan menjadi query SPARQL. | **Fase 2** |
| **OPT-6** | Sistem menampilkan rekomendasi heritage berdasarkan relasi semantic. | **Fase 2** |
| **OPT-7** | Pengunjung dapat menyimpan heritage favorit untuk dilihat kembali. | **Fase 2** |

# 7. Alur Pengguna Utama (Key User Flows)

## 7.1 Pencarian Heritage

1. Pengunjung membuka Home dan menggulir hero section hingga statistik dan tombol Explore terlihat.
2. Pengunjung menekan search bar pada navbar (atau tombol Explore).
3. Sistem membuka panel filter lengkap; pengunjung mengisi keyword dan filter (country, region, category, tahun, criteria, in danger).
4. Sistem membuka halaman Explore dengan filter terisi dan menjalankan query SPARQL.
5. Sistem menampilkan Search Results dalam bentuk card.
6. Pengunjung memilih salah satu Heritage Card dan menekan "View Details".
7. Sistem menampilkan Heritage Detail.

## 7.2 Eksplorasi Hubungan Antar Heritage

1. Pengunjung membuka Heritage Detail.
2. Pengunjung melihat bagian Connected Knowledge yang menampilkan hubungan heritage (negara, region, kategori, criteria, entitas eksternal).
3. Pengunjung memilih salah satu hubungan atau menggulir ke bagian Related Heritage di bagian bawah halaman.
4. Pengunjung memilih heritage terkait.
5. Sistem menampilkan Heritage Detail lainnya.

## 7.3 Eksplorasi Geografis

1. Pengunjung membuka Map dari Home atau header.
2. Sistem memuat data heritage melalui SPARQL dan menampilkan marker.
3. Pengunjung menerapkan filter (category, country, in danger).
4. Pengunjung memilih marker.
5. Sistem menampilkan popup heritage.
6. Pengunjung menekan tautan di popup dan sistem membuka Heritage Detail.

## 7.4 Bertanya kepada Chatbot

1. Pengunjung menekan floating button Ask Heritage.
2. Pengunjung mengetik pertanyaan.
3. Sistem menerjemahkan pertanyaan menjadi query SPARQL dan mengambil fakta dari knowledge graph.
4. Sistem menampilkan jawaban dalam bahasa natural.
5. Pengunjung memilih aksi lanjutan: View Result, Show on Map, atau Open Heritage.

## 7.5 Eksplorasi melalui Discover

1. Pengunjung membuka Discover.
2. Pengunjung menggeser carousel dan memilih jalur: Country, Region, Category, Criteria, atau World Heritage in Danger.
3. Pengunjung memilih nilai (contoh: Indonesia).
4. Sistem membuka Explore dengan filter terisi (contoh: Explore?country=Indonesia).
5. Pengunjung memilih heritage dan sistem menampilkan Heritage Detail.

# 8. Model Data (High-Level)

| **Entitas** | **Field Utama** | **Keterangan** |
| --- | --- | --- |
| **Heritage** | heritage_id, name, description, outstanding_universal_value, inscription_year, image_url, is_in_danger, is_transboundary, [historical_period] | Entitas utama World Heritage Site |
| **Country** | country_id, name | Negara tempat heritage berada; heritage transboundary dapat memiliki lebih dari satu negara |
| **Region** | region_id, name | Wilayah heritage |
| **Category** | category_id, name | Cultural, Natural, atau Mixed |
| **Criterion** | criterion_id, code, description | Kriteria UNESCO (contoh: i, ii, vi) |
| **Location** | location_id, latitude, longitude | Koordinat untuk marker peta |
| **Related Entity** | entity_id, name, entity_type, source_dataset | Entitas historis/budaya dari dataset eksternal |
| **Heritage Relation** | heritage_id, related_heritage_id, relation_type | Relasi antar heritage (country, region, category, criteria, theme, eksternal) |
| **Saved Heritage** | [saved_id], [heritage_id] | Penyimpanan heritage favorit |

**Catatan:** field dalam [tanda kurung siku] merupakan bagian dari fitur usulan/Fase Lanjutan (Bab 11).

# 9. Kebutuhan Non-Fungsional (Non-Functional Requirements)

- **Akurasi Data :** Chatbot dan seluruh tampilan data harus bersumber dari knowledge graph sehingga jawaban dapat ditelusuri ke data.
- **Interoperabilitas :** Data tersedia dalam format RDF dan dapat diakses melalui SPARQL endpoint standar.
- **Keterlihatan Semantik :** Halaman publik memuat markup RDFa dan Open Graph Protocol.
- **Performa :** Query SPARQL pada pencarian, peta, dan Discover harus mengembalikan hasil dalam waktu yang wajar bagi pengguna. Target angka belum ditetapkan (lihat Bab 12).
- **Keandalan Layanan Eksternal :** Kegagalan layanan eksternal (peta, SPARQL endpoint eksternal) tidak boleh membuat seluruh website tidak dapat digunakan.
- **Penggunaan :** Alur utama Search → Detail → Knowledge → Map → Chatbot harus dapat dilalui tanpa kebingungan, dengan navigasi konsisten pada setiap halaman.
- **Responsivitas :** Website responsif dan dapat digunakan dengan baik pada layar desktop, tablet, dan perangkat seluler, termasuk navbar, panel filter, card, peta, dan carousel Discover.
- **Gaya Visual :** Monokrom (hitam, putih, abu-abu), minimalis, dan modern. Gradient hanya menggunakan gradasi abu-abu ke hitam.
- **Tipografi :** Menggunakan font dengan huruf tebal (bold) sebagai identitas visual.
- **Layout :** Proporsional dan tidak berukuran terlalu besar; referensi layout adalah Atlas Obscura, Vilcek Foundation, dan Smithsonian. Card berbentuk sudut membulat dan konsisten pada seluruh halaman.
- **Animasi :** Animasi halus pada scroll, hover, transisi navbar, hero section, reveal section, dan carousel Discover. Referensi gaya animasi adalah Rijksmuseum Boerhaave.
- **Konsistensi Desain :** Peta interaktif, halaman Heritage Detail, Related Heritage, dan About mengikuti gaya visual utama yang sama.
- **Kenyamanan Gerak :** Animasi tidak boleh mengganggu keterbacaan atau kinerja halaman; pengunjung yang menonaktifkan animasi pada perangkatnya mendapat tampilan dengan animasi dikurangi (asumsi pengembang).
- **Keterbacaan :** Teks di atas gambar atau gradient harus tetap terbaca dengan kontras yang cukup (asumsi pengembang).

# 10. Integrasi Pihak Ketiga

| **Layanan** | **Fungsi** | **Catatan** |
| --- | --- | --- |
| **UNESCO World Heritage Dataset** | Sumber data utama heritage | Diubah menjadi dataset RDF |
| **Apache Jena Fuseki** | Triple store dan SPARQL endpoint | Inti penyimpanan dan query data |
| **SPARQL Endpoint Eksternal** | Mengambil data tambahan dari sumber lain | Sumber spesifik belum ditentukan |
| **Dataset Eksternal** | Memperkaya relasi dan informasi heritage | Sumber spesifik belum ditentukan |
| **Leaflet + OpenStreetMap** | Menampilkan peta interaktif | MapLibre sebagai alternatif |
| **Open Graph Protocol** | Metadata berbagi halaman | Diterapkan pada halaman publik |

# 11. Fitur Usulan / Fase Lanjutan

- **Knowledge Graph.** Visualisasi hubungan antar entitas (Heritage → Country, Region, Category, Criteria, entitas historis/budaya, Related Heritage) dalam bentuk graf interaktif, diakses melalui tombol "Explore Connections" pada Heritage Detail. Data tetap dimodelkan sebagai RDF pada MVP; yang ditunda hanya tampilan visualnya. Pada MVP hubungan ditampilkan lewat Connected Knowledge dan Related Heritage.
- **Compare Heritage.** Membandingkan dua atau lebih heritage berdasarkan country, region, category, inscription year, criteria, dan informasi terkait. Memanfaatkan data pada Heritage Detail.
- **Historical Period.** Eksplorasi berdasarkan periode sejarah (Ancient, Medieval, Early Modern, Modern). Membutuhkan data periode tambahan karena tidak tersedia sebagai field utama UNESCO.
- **Advanced Semantic Search.** Pencarian bahasa natural yang lebih kompleks (contoh: cultural heritage di Asia dengan kriteria ii dan ditetapkan sebelum tahun 2000) yang diterjemahkan menjadi query SPARQL. Memperluas fitur Explore dan Chatbot.
- **Recommendation.** Rekomendasi heritage berdasarkan relasi semantic, bukan hanya popularitas. Memperluas fitur Related Heritage.
- **Favorite / Saved Heritage.** Pengunjung dapat menyimpan heritage untuk dilihat kembali. Bersifat tambahan dan tidak berhubungan langsung dengan kebutuhan utama Semantic Web.

# 12. Pertanyaan Terbuka / TBD

- Apa tagline yang digunakan pada hero section?
- Statistik apa saja selain jumlah heritage yang ditampilkan pada hero section (misalnya jumlah negara atau jumlah per kategori)?
- Berapa banyak gambar heritage pada hero section dan bagaimana cara memilihnya?
- Font apa yang digunakan, dan library animasi/smooth scroll apa yang dipilih?
- Apakah foto heritage ditampilkan berwarna, hitam-putih, atau hitam-putih yang berubah menjadi berwarna saat hover?
- Bagaimana membedakan kategori (Cultural, Natural, Mixed) dan status in danger tanpa warna selain monokrom?
- Pada Discover, apakah pilihan card langsung membuka Explore terfilter atau ada langkah memilih nilai terlebih dahulu (misalnya daftar negara)?
- Fitur peta apa saja dari website UNESCO yang akan diikuti selain yang tercantum pada PRD, dan apakah tampilan peta memakai tile monokrom?
- Siapa nama tim/pengembang dan nama klien (dosen/pemberi tugas) yang akan dicantumkan pada dokumen?
- Dataset eksternal dan SPARQL endpoint eksternal apa yang akan digunakan?
- Bagaimana sumber gambar heritage pada card dan halaman detail, serta lisensinya?
- Bagaimana ketentuan lisensi dan penggunaan data UNESCO?
- Bahasa antarmuka website apa yang digunakan (Indonesia, Inggris, atau keduanya)? Contoh chatbot berbahasa Indonesia, sedangkan label menu berbahasa Inggris.
- Teknologi implementasi chatbot (aturan/intent berbasis template atau model bahasa) belum ditentukan.
- Teknologi frontend/backend dan pilihan akhir Leaflet atau MapLibre belum ditentukan. Library visualisasi knowledge graph ditentukan saat fitur tersebut dikerjakan.
- Bagaimana hosting Fuseki dan website, serta apakah ada biaya yang perlu dipertimbangkan?
- Bagaimana tenggat waktu dan timeline pengerjaan setiap fase?
- Apakah ada target performa (misalnya waktu respons) dan target perangkat/browser?
- Definisi "Theme" pada Related Heritage dan sumber datanya belum dijelaskan.
- Apakah fitur tambahan (termasuk Knowledge Graph) akan dikerjakan dalam periode proyek ini?

# 13. Glosarium

- **World Heritage Site :** Situs yang ditetapkan UNESCO sebagai warisan dunia karena nilai budaya dan/atau alamnya.
- **UNESCO :** Organisasi PBB untuk pendidikan, ilmu pengetahuan, dan kebudayaan yang menetapkan World Heritage Sites.
- **Outstanding Universal Value :** Pernyataan nilai penting universal suatu situs yang menjadi dasar penetapannya.
- **UNESCO Criteria :** Kriteria penetapan situs warisan dunia (contoh: i, ii, vi).
- **Cultural / Natural / Mixed :** Kategori heritage berdasarkan nilai budaya, alam, atau gabungan keduanya.
- **Inscription Year :** Tahun situs ditetapkan sebagai warisan dunia.
- **World Heritage in Danger :** Daftar situs yang terancam kerusakan atau kehilangan nilainya.
- **Transboundary / Transnational :** Heritage yang mencakup lebih dari satu negara.
- **Semantic Web :** Pendekatan web yang memberi makna dan hubungan pada data agar dapat diproses mesin.
- **RDF :** Resource Description Framework, model data berbasis triple (subjek–predikat–objek).
- **RDFa :** Cara menyisipkan data RDF ke dalam markup HTML.
- **Open Graph Protocol :** Standar metadata untuk menampilkan pratinjau halaman saat dibagikan.
- **SPARQL :** Bahasa query untuk data RDF.
- **Apache Jena Fuseki :** Server SPARQL dan triple store yang digunakan untuk menyimpan serta mengakses dataset RDF.
- **Knowledge Graph :** Representasi data berupa graf entitas dan hubungannya.
- **Ontologi :** Model formal yang mendefinisikan konsep dan relasi dalam suatu domain.
- **Hero Section :** Bagian pembuka halaman Home yang berisi tagline, gambar, statistik, dan tombol aksi.
- **Navbar :** Bilah navigasi di bagian atas halaman.
- **Card :** Kotak ringkasan satu heritage yang menampilkan gambar dan informasi utama.
- **Carousel :** Tampilan kartu yang dapat digeser ke berikutnya atau sebelumnya.
- **Monokrom :** Palet warna yang hanya menggunakan hitam, putih, dan abu-abu.
- **Leaflet / MapLibre :** Library peta interaktif untuk web.

---

*Dokumen ini merupakan draft sementara dan dapat berubah seiring pembahasan lebih lanjut dengan klien.*
