# Business Requirements Document (BRD)

## Website E-Commerce Pelanggan Terverifikasi

| Atribut | Nilai |
|---|---|
| Proyek | Website E-Commerce Custom Pixel Komunika |
| Klien | Sylvi / pihak pemilik usaha |
| Pengembang | PT Webekspres Teknologi Indonesia |
| Versi | 0.16 - Klarifikasi Klien 7-11 Agustus 2026 |
| Tanggal | Selasa, 11 Agustus 2026 |
| Status | Approved Working Baseline - klarifikasi klien diterapkan bertahap |
| Dokumen sumber | `../references/proposal-klien-sylvi-update-1.pdf` |
| MVP delivery | `MVP.md` |

## 1. Tujuan Dokumen

Dokumen ini mendefinisikan kebutuhan bisnis, ruang lingkup, aktor, aturan bisnis,
risiko, dan ukuran keberhasilan proyek. BRD tidak menetapkan detail implementasi
teknis; detail fungsi berada di `FRD.md` dan spesifikasi teknis berada di
`SRS.md`.

## 2. Hierarki Sumber Requirement

Apabila terdapat konflik, requirement diprioritaskan dengan urutan berikut:

1. Keputusan tertulis terbaru yang telah disetujui klien.
2. Catatan klarifikasi terbaru dari stakeholder proyek.
3. Proposal penawaran versi 10 Juli 2026.
4. Asumsi analisis sistem.

Status requirement:

- **Baseline**: tertulis pada proposal atau telah dikonfirmasi stakeholder.
- **Amendment**: perubahan terhadap proposal berdasarkan catatan terbaru.
- **Proposed**: rekomendasi yang menunggu persetujuan.
- **TBD**: keputusan belum tersedia dan dapat memengaruhi desain.
- **ON_HOLD**: tidak boleh diimplementasikan sampai keputusan tertulis tersedia.

### 2.1 Riwayat Perubahan

| ID | Diterima | Sumber | Perubahan | Dampak Sementara | Status |
|---|---|---|---|---|---|
| CR-001 | Senin, 27 Juli 2026, 11.54-11.56 WIB | Pesan klien Sylvi | Klien menginformasikan perubahan pada PPh 22 dan batas maksimal penjualan; kedua poin kemungkinan besar dihilangkan, dengan rincian lanjutan menyusul. | Requirement terkait ditempatkan `ON_HOLD`; tidak dihapus dan tidak diimplementasikan sebelum klarifikasi Selasa, 28 Juli 2026. | Under clarification |
| CR-002 | Senin, 27 Juli 2026 | Klarifikasi governance proyek | Sylvi ditetapkan sebagai perwakilan klien, Sultan sebagai System Analyst Webekspres, dan Pak Endang sebagai Project Manager Webekspres. Persetujuan final berada pada klien. | OPN-017 diselesaikan dan aturan efektivitas baseline diperjelas. | Resolved |
| CR-003 | Senin, 27 Juli 2026 | Persetujuan working baseline dan fallback POS | Working baseline disetujui dan ditandatangani. Jika koneksi POS belum disediakan, pengembangan serta pengujian menggunakan data contoh. | Tanggal persetujuan ditetapkan dan data contoh non-production ditambahkan; kesiapan koneksi POS production tetap dilacak. | Approved |
| CR-004 | Selasa, 28 Juli 2026 | Pesan klien Sylvi | Klien meminta pengembangan dilanjutkan sesuai plan yang telah dibuat. Sultan mengonfirmasi interpretasi bahwa scope kembali mengikuti proposal awal. | Batas maksimal penjualan serta komponen PPh 22/surcharge tetap berada dalam MVP. Detail formula dan mapping data tetap terbuka pada OPN-006. | Approved working direction |
| CR-005 | Selasa, 28 Juli 2026, 12.42-13.43 WIB | Klarifikasi bertahap klien | Klien menjelaskan tiga jenis harga wajib dari POS, ambang nilai belanja per klasifikasi yang memicu PPh 22, master produk dari POS, pola sinkronisasi stok, kesiapan akses POS, dan masa berlaku pesanan belum dibayar. | Konsep batas maksimal dikoreksi menjadi ambang nilai belanja; OPN-003 dan OPN-007 diselesaikan, sedangkan OPN-004, OPN-005, OPN-006, OPN-013, dan OPN-019 diperbarui sebagai keputusan parsial. | Partially resolved |
| CR-006 | Selasa, 28 Juli 2026 | Diagram dan hasil meeting skema API POS | Master produk dan inventory ditarik sekali sehari; stok per produk dapat dicek berkala untuk rekonsiliasi. Diagram awal menggambarkan transaksi web membuat sales order POS yang mengurangi stok dan menerbitkan invoice, serta pembatalan menerbitkan retur. Seluruh field yang tersedia di POS menjadi master, sedangkan website melengkapi field yang belum tersedia. | Menjadi working scheme awal; ownership transaksi/invoice dan arah pelaporan kemudian digantikan oleh klarifikasi CR-012. | Partially superseded by CR-012 |
| CR-007 | Selasa, 28 Juli 2026 | Keputusan working baseline System Analyst Webekspres | Konfigurasi klasifikasi, ambang, dan tarif PPh 22 dikelola melalui website. Nilai PPh 22 ditampilkan sebagai komponen terpisah pada cart, checkout, invoice, dan laporan. | Q-008 dan Q-009 diselesaikan; BR-007, BR-008, BR-015, BR-026, RULE-006, serta requirement turunannya diperjelas. Dasar pengenaan tetap terbuka pada OPN-006. | Resolved for working baseline |
| CR-008 | Selasa, 28 Juli 2026 | Klarifikasi klien mengenai akses POS | Kak Rio ditetapkan sebagai PIC POS. Akses POS dibuka setelah alur website berbasis data contoh sudah berjalan. | Q-014 diselesaikan; OPN-005 dan OPN-019 diperjelas. Kontak dan trigger akses telah tersedia, sedangkan kontrak teknis API serta contract test tetap terbuka. | Resolved access owner and trigger |
| CR-009 | Selasa, 28 Juli 2026 | Klarifikasi klien mengenai isi invoice | Invoice wajib menampilkan nama, alamat, nomor kontak, dan NPWP toko; jumlah barang, nama barang, SKU, harga satuan, dan total harga item; total pembelian keseluruhan; serta nilai rupiah PPh 22 jika berlaku. | Isi minimum invoice ditetapkan pada BR-016 dan requirement turunannya. OPN-022 tetap parsial untuk sumber data identitas toko, PDF, channel, dan kepemilikan dokumen invoice. | Required fields resolved; delivery format partial |
| CR-010 | Selasa, 28 Juli 2026 | Klarifikasi klien mengenai syarat harga partai | Dalam satu pembelian, sedikitnya satu produk/SKU harus berjumlah minimal lima unit agar transaksi memenuhi syarat harga partai. Kuantitas produk/SKU berbeda tidak dijumlahkan. | Kriteria kelayakan harga partai pada BR-006, RULE-018, OPN-013, dan Q-015 diperjelas. Cakupan item yang mendapat harga partai serta prioritas terhadap harga grosir tetap terbuka. | Eligibility resolved; application partial |
| CR-011 | Selasa, 28 Juli 2026 | Dokumentasi resmi Biteship | Biteship diidentifikasi sebagai API pengiriman multi-kurir eksternal. Scope proyek memakai Maps API untuk standardisasi area dan Rates API untuk memperoleh pilihan layanan serta estimasi ongkir; API order, pickup, label, tracking, dan webhook Biteship tidak termasuk baseline. | Kontrak teknis dasar BR-024 dan requirement turunannya diperjelas. Nilai origin, sumber berat, daftar kurir, metode lokasi, serta fallback tetap dibahas melalui OPN-016 dan OPN-021. | Technical reference adopted; business scope unchanged |
| CR-012 | Selasa, 28 Juli 2026, 20.03-20.04 WIB | Klarifikasi klien Sylvi | PPh 22 dari lebih dari satu klasifikasi digabungkan. Transaksi dan invoice web dibuat oleh website; POS menerima laporan penjualan untuk mencatat transaksi dan mengurangi stok, serta laporan retur untuk mencatat retur dan menambah stok. Setelah pembayaran diverifikasi, alur berlanjut ke diproses, dikemas, dikirim, dan selesai; nomor resi ditampilkan. Admin menerima notifikasi order baru melalui indikator merah di website dan WhatsApp dengan bunyi; template WhatsApp belum tersedia. | CR-006 superseded untuk ownership transaksi/invoice dan arah pelaporan. OPN-004, OPN-005, OPN-006, OPN-008, OPN-020, OPN-022, dan OPN-023 diperbarui; requirement, model data, dan user flow diselaraskan. | Approved business flow; API/notification contract partial |
| CR-013 | Rabu, 29 Juli 2026 | Review dokumentasi internal | Pertanyaan yang telah selesai ditandai strikethrough tanpa menghapus ID dan bukti keputusan. Diagram proses bisnis memakai notasi flowchart yang konsisten; diagram konteks tetap diperlakukan sebagai context diagram. | Keterbacaan dan audit trail diperbaiki tanpa mengubah scope atau aturan bisnis. | Documentation-only |
| CR-014 | Rabu, 29 Juli 2026 | Review alur fulfillment | Urutan `PROCESSING` → `PACKED` → `SHIPPED` → `COMPLETED` telah disetujui, tetapi pemicu setiap transisi dan cara memastikan barang diterima belum pernah ditetapkan. Melihat nomor resi tidak dapat dianggap sebagai bukti penerimaan. | OPN-020 dibuka kembali sebagai keputusan parsial; Q-025 ditambahkan dan alur client-facing serta user flow diperinci tanpa memilih mekanisme konfirmasi secara sepihak. | Clarification required |
| CR-015 | Rabu, 29 Juli 2026 | Klarifikasi sumber pembatalan | Pembatalan manual oleh admin dan pembatalan kedaluwarsa oleh sistem harus dapat dibedakan tanpa memecah lifecycle menjadi dua status batal. | Status tetap `CANCELLED`; sumber `ADMIN`/`SYSTEM`, pelaku admin jika ada, alasan, dan waktu pembatalan disimpan serta ditampilkan pada rincian order. | Baseline clarification |
| CR-016 | Jumat, 31 Juli 2026 | Konfirmasi resmi klien melalui System Analyst | Production menggunakan shared hosting milik klien dan developer Webekspres akan diberikan akses yang diperlukan untuk setup serta deployment. | OPN-001 diperkuat sebagai keputusan final. Komitmen penyediaan akses pada PRE-006 selesai; kredensial aktual, domain/DNS, akun layanan, kemampuan runtime, cron, log, dan backup tetap diverifikasi saat technical handoff. | Resolved - hosting ownership and access commitment |
| CR-017 | Jumat-Selasa, 7-11 Agustus 2026 | Jawaban tertulis klien melalui WhatsApp | Klien memperjelas ownership nama produk, formula PPh 22, harga partai/grosir, isi dan channel invoice, omzet, kurir toko/Biteship, retensi bukti pembayaran, notifikasi admin, reseller, penggabungan pengiriman, serta penyelesaian order. | Requirement bisnis, fungsi, model data, user flow, dan keputusan terbuka diperbarui. Detail tarif multi-klasifikasi, penomoran akun reseller, nama legal perusahaan, ongkir/invoice order gabungan, provider WhatsApp, dan aturan poin tetap membutuhkan keputusan. | Partially resolved; baseline updated |

### 2.2 Model Delivery Hybrid Agile-Waterfall

Proyek menggunakan model hybrid:

- **Waterfall/stage-gate** untuk penetapan MVP, baseline requirement,
  acceptance criteria, persetujuan UAT, dan go-live.
- **Agile** untuk pemecahan backlog, implementasi iteratif, pengujian
  berkelanjutan, demo increment, dan penyerapan feedback.

Aturan utamanya:

1. Hanya requirement `Baseline` dengan acceptance criteria yang jelas dan tidak
   berstatus `ON_HOLD` yang boleh masuk sprint.
2. `Proposed`, `TBD`, candidate scope, dan `ON_HOLD` tidak menjadi komitmen
   sprint atau MVP sampai disetujui tertulis.
3. Perubahan setelah MVP baseline harus melalui impact analysis terhadap scope,
   jadwal 45 hari kerja, biaya, data, integrasi, test, dan acceptance criteria.
4. Perubahan berdampak sedang atau tinggi harus menukar scope setara, dipindah
   ke fase berikutnya, atau mengubah jadwal/biaya melalui persetujuan tertulis.
5. Setiap increment harus didemokan dan diuji tanpa menunggu UAT akhir.

### 2.3 Change Control

| Tingkat | Contoh | Penanganan |
|---|---|---|
| Rendah | Teks, label, urutan tampilan, atau klarifikasi tanpa perubahan perilaku. | Product Owner dapat memasukkan ke backlog sprint berikutnya. |
| Sedang | Perubahan flow atau field yang memengaruhi satu modul. | Wajib impact analysis dan pertukaran scope/prioritas. |
| Tinggi | Harga, pajak, batas penjualan, stok, POS, status order, invoice, pembayaran, atau hosting. | Wajib persetujuan klien dan Webekspres; sprint terdampak tidak dimulai sebelum keputusan. |

Setiap change request minimal mencatat sumber, tanggal, requirement terdampak,
keputusan, pemilik, target keputusan, dampak, dan status implementasi.

Persetujuan final atas perubahan/ide baru berada pada klien. Perubahan baru
efektif menjadi baseline setelah klien dan Webekspres memberikan persetujuan
tertulis pada hari kerja. Diskusi atau persetujuan di luar hari kerja tidak
mengubah baseline sampai dikonfirmasi kembali pada hari kerja.

## 3. Ringkasan Eksekutif

Proyek membangun website penjualan responsif untuk pelanggan yang telah
diregistrasi dan disetujui admin. Sistem mengelola katalog, tiga jenis harga,
stok, transaksi, invoice, pembayaran transfer manual, pengiriman, dan laporan
omzet berdasarkan wilayah.

Website menggunakan koneksi data dari POS yang disediakan klien untuk
mendapatkan data produk dan stok production. Data contoh hanya digunakan untuk
development, staging, demo, dan UAT ketika koneksi tersebut belum tersedia.
Informasi pemasaran yang tidak tersedia di POS, seperti gambar, video,
deskripsi, dan metadata penayangan, dikelola di website tanpa ditimpa oleh
proses sinkronisasi data production.

## 4. Latar Belakang dan Masalah Bisnis

Proses penjualan membutuhkan kanal digital yang:

- dapat diakses melalui perangkat mobile dan desktop;
- membatasi transaksi hanya untuk pelanggan yang disetujui;
- menjaga katalog dan stok selaras dengan POS;
- mencatat transaksi dan pembayaran secara terstruktur;
- menghitung harga berdasarkan kuantitas pembelian;
- memberikan estimasi ongkir;
- menyediakan invoice dan laporan omzet per wilayah;
- mengurangi pekerjaan manual dan risiko perbedaan data.

## 5. Tujuan Bisnis

| ID | Tujuan |
|---|---|
| BO-001 | Menyediakan kanal penjualan digital yang responsif dan mudah digunakan. |
| BO-002 | Memastikan hanya pelanggan yang telah disetujui admin dapat bertransaksi. |
| BO-003 | Menyatukan katalog website dengan produk dan stok dari POS. |
| BO-004 | Menjaga data pemasaran produk yang dikelola website tetap independen dari POS. |
| BO-005 | Mengotomasi perhitungan harga, ongkir, total transaksi, dan invoice. |
| BO-006 | Mendukung pembayaran transfer bank yang diverifikasi admin. |
| BO-007 | Menyediakan visibilitas transaksi dan omzet berdasarkan periode serta wilayah. |
| BO-008 | Menyediakan fondasi yang dapat ditingkatkan ketika jumlah pengguna atau beban integrasi bertambah. |

## 6. Stakeholder

| Stakeholder | Kepentingan dan Tanggung Jawab |
|---|---|
| Pemilik usaha / klien | Memegang persetujuan final atas requirement, perubahan, UAT, dan go-live. |
| Sylvi - Perwakilan klien / Product Owner | Memprioritaskan backlog, menjawab klarifikasi bisnis, menerima increment sprint, dan menyampaikan persetujuan final klien. |
| Sultan - System Analyst Webekspres | Menjaga konsistensi BRD, FRD, SRS, acceptance criteria, traceability, dan impact analysis. |
| Pak Endang - Project Manager Webekspres | Menjaga cadence, dependency, risk, keputusan, change log, dan eskalasi hambatan tanpa mengambil alih persetujuan final klien. |
| Admin operasional | Memverifikasi pelanggan, mengelola katalog lokal, pembayaran, transaksi, pengiriman, dan laporan. |
| Pelanggan | Melakukan registrasi, melihat katalog, membuat pesanan, membayar, dan memantau transaksi. |
| Kak Rio - PIC POS | Menjadi narahubung koordinasi akses, kontrak teknis, dan validasi integrasi POS. |
| Vendor / pengelola POS | Menyediakan API, kredensial, dokumentasi, dan identifier produk yang stabil. |
| Biteship | Penyedia API pengiriman multi-kurir eksternal; pada baseline hanya menyediakan standardisasi area melalui Maps API serta layanan dan estimasi ongkir melalui Rates API. |
| Webekspres | Menganalisis, mengembangkan, menguji, menerapkan, dan memelihara aplikasi sesuai scope. |
| Pixel Komunika | Penerima hasil akhir pengembangan dan pihak koordinasi proyek. |

## 7. Ruang Lingkup

### 7.1 In Scope

- Website responsif untuk mobile, tablet, dan desktop.
- Registrasi dan autentikasi pelanggan.
- Verifikasi, aktivasi, penolakan, dan penangguhan akun oleh admin.
- Katalog produk, klasifikasi/kategori, merek, SKU, deskripsi, dan media
  ([keputusan resolved: lihat OPN-003](#opn-003)).
- Sinkronisasi produk dan stok dari POS, dengan data seeder sebagai fallback
  sampai API tersedia
  ([keputusan resolved: OPN-003](#opn-003) dan
  [kontrak API masih terbuka: OPN-005](#opn-005)).
- Pembuatan transaksi dan invoice di website, pengiriman laporan penjualan ke
  POS untuk pencatatan transaksi dan pengurangan stok, serta pengiriman laporan
  retur untuk pencatatan retur dan penambahan stok
  ([lihat OPN-005](#opn-005)).
- Enrichment produk lokal berupa nama tampilan, gambar, video, deskripsi
  pemasaran, SEO, dan pengaturan tampil. SKU tetap mengikuti POS, sedangkan
  nama tampilan produk dapat diperbarui mandiri melalui website.
- Tiga jenis harga wajib per produk: eceran, partai, dan grosir. Seluruh harga
  disinkronkan dari POS; harga eceran disiapkan tetapi tidak ditampilkan pada
  storefront fase saat ini ([keputusan resolved: lihat OPN-013](#opn-013)).
- Ambang nilai belanja yang dapat dikonfigurasi per klasifikasi produk serta
  PPh 22 persentase ketika ambang terlampaui
  ([keputusan parsial: lihat OPN-006](#opn-006) dan
  [OPN-018](#opn-018)).
- Keranjang, checkout, transaksi, invoice, dan riwayat transaksi.
- Pembayaran transfer bank dan upload bukti pembayaran dengan retensi lima
  tahun ([lihat OPN-009](#opn-009)).
- Verifikasi pembayaran oleh admin.
- Pemrosesan, pengemasan, pengiriman, penyelesaian pesanan, status
  `TERKENDALA`, konfirmasi penerimaan melalui tautan WhatsApp, dan tampilan
  nomor resi untuk metode pengiriman yang memilikinya.
- Notifikasi order baru kepada admin melalui indikator merah di website dan
  WhatsApp ([keputusan parsial: lihat OPN-023](#opn-023)).
- Pembatalan transaksi oleh admin pada hari kalender yang sama.
- Pengiriman kurir toko ke seluruh kecamatan di Kota Bandung dan Kabupaten
  Bandung dengan target H+1 hari kerja atau H+2 apabila kurir tidak tersedia;
  Minggu dan tanggal merah tidak dihitung ([tarif masih terbuka: OPN-010](#opn-010)).
- Estimasi ongkir melalui Biteship.
- Laporan transaksi dan omzet berdasarkan periode dan wilayah; order mulai
  dihitung sebagai omzet ketika berstatus `SHIPPED`.
- Reseller versi pertama adalah akun yang telah terdaftar dan disetujui admin.
  Reseller aktif dapat melihat harga dan memesan melalui website atau WhatsApp
  ([detail alur WhatsApp: OPN-002](#opn-002)).
- Beberapa order boleh digabungkan dalam satu pengiriman hanya jika alamat
  tujuannya sama ([dampak ongkir/invoice: OPN-014](#opn-014)).
- Monitoring stok, batas minimum stok, dan riwayat perubahan stok.
- Log sinkronisasi dan audit aktivitas kritis.
- Backup, maintenance, SSL, hosting, serta pelatihan sesuai kesepakatan final.

### 7.2 Out of Scope Baseline

- Aplikasi mobile native.
- Pengembangan atau perubahan internal sistem POS milik klien.
- Pembuatan endpoint API di sisi POS.
- Pemesanan, pickup, atau pemanggilan kurir otomatis melalui Biteship.
- Payment gateway.
- Sistem akuntansi lengkap.
- Marketplace multivendor.
- Aplikasi khusus kurir.
- Microservices atau Kubernetes.
- Fitur promo dan voucher yang belum disetujui. Poin konfirmasi penerimaan
  hanya berlaku setelah aturan perolehan, saldo, masa berlaku, dan penggunaan
  disepakati pada [OPN-020](#opn-020).

### 7.3 Candidate Scope

Requirement berikut belum tertulis dalam proposal dan harus disetujui sebelum
menjadi baseline:

| ID | Kandidat | Status |
|---|---|---|
| CND-001 | ~~Segmentasi pelanggan menjadi customer biasa dan reseller.~~ Reseller versi pertama adalah akun terdaftar yang disetujui admin. | Resolved melalui OPN-002 |
| CND-002 | Pemesanan reseller melalui website dan WhatsApp. | In scope; detail pencatatan order WhatsApp mengikuti OPN-002 |
| CND-003 | Grouping beberapa transaksi untuk satu pengiriman dengan alamat tujuan yang sama. | In scope; dampak transaksi mengikuti OPN-014 |

## 8. Konteks Bisnis

Diagram berikut adalah **context diagram**, bukan urutan proses. Panah menunjukkan
arah interaksi atau pertukaran data; panah tidak menyatakan urutan waktu.

```mermaid
flowchart LR
    Customer["Pelanggan"]
    Admin["Admin Operasional"]
    Web(("Website<br/>E-Commerce"))
    POS["Sistem POS Klien"]
    Biteship["Biteship API<br/>Maps dan Rates"]
    Bank["Rekening Bank"]
    Report[("Laporan Transaksi dan Omzet")]

    Customer -->|Registrasi, katalog, checkout,<br/>dan bukti pembayaran| Web
    Admin -->|Approval, verifikasi,<br/>dan operasional| Web
    POS -->|Master produk, harga,<br/>dan stok| Web
    Web -->|Laporan penjualan<br/>dan retur| POS
    Web -->|Pencarian area<br/>dan permintaan tarif| Biteship
    Customer -->|Transfer pembayaran| Bank
    Admin -->|Pencocokan transfer| Bank
    Web -->|Data transaksi| Report
```

## 9. Business Requirements

| ID | Requirement | Status |
|---|---|---|
| BR-001 | Sistem harus menyediakan website penjualan responsif. | Baseline |
| BR-002 | Pelanggan harus registrasi sebelum dapat melakukan transaksi. | Baseline |
| BR-003 | Admin harus menyetujui pelanggan sebelum akses pembelian diberikan. | Baseline |
| BR-004 | Admin harus dapat mengaktifkan dan menonaktifkan akun pelanggan. | Baseline |
| BR-005 | Sistem harus menyinkronkan seluruh field produk yang tersedia di POS. SKU tetap mengikuti POS; website dapat mengubah nama tampilan produk secara mandiri dan melengkapi field presentasi yang tidak disediakan POS ([lihat OPN-003](#opn-003)). | Baseline |
| BR-006 | Setiap produk harus memiliki harga eceran, partai, dan grosir dari POS. Harga eceran disimpan tetapi tidak ditampilkan pada storefront fase saat ini. Transaksi memakai harga partai untuk seluruh item jika sedikitnya satu produk/SKU mencapai minimum global yang dapat diubah admin, dengan nilai awal lima unit; kuantitas antar-SKU tidak dijumlahkan. Jika item juga memenuhi syarat grosir, harga partai tetap dipakai. | Baseline; resolved |
| BR-007 | Admin harus dapat memilih klasifikasi produk dan mengatur ambang nilai belanja untuk masing-masing klasifikasi terpilih melalui website ([lihat OPN-018](#opn-018)). | Baseline |
| BR-008 | Admin harus dapat mengatur tarif PPh 22 melalui website, termasuk `0%`; sistem menambahkan PPh 22 ketika nilai belanja pada klasifikasi terpilih melampaui ambangnya. Dasar pengenaan adalah seluruh subtotal klasifikasi yang terpicu. Subtotal klasifikasi terpicu digabung, dibagi `1,11`, lalu dikalikan persentase PPh 22. Keseragaman tarif multi-klasifikasi dan aturan pembulatan mengikuti [OPN-006](#opn-006). | Baseline; formula resolved, rate/rounding partial |
| BR-009 | Website harus menarik kategori, produk, detail produk, daftar harga, dan seluruh stok dari POS sekali sehari; stok per produk dapat dipanggil berkala untuk rekonsiliasi. Data contoh hanya digunakan sebelum koneksi tersedia ([OPN-005](#opn-005), [OPN-019](#opn-019)). | Baseline; kontrak API partially open |
| BR-010 | Sinkronisasi POS tidak boleh menghapus data pelengkap website ketika field POS tidak tersedia; ketika POS menyediakan field tersebut, nilai POS menjadi sumber utama. | Amendment |
| BR-011 | Sistem harus menampilkan status stok Tersedia, Menipis, atau Habis. | Baseline |
| BR-012 | Admin harus dapat menentukan batas minimum stok. | Baseline |
| BR-013 | Sistem harus menyimpan riwayat perubahan stok efektif website beserta sumbernya: sinkronisasi penuh, pengecekan per produk, penjualan web, retur web, atau koreksi. | Baseline |
| BR-014 | Website harus membuat order dan invoice secara atomik, mengurangi stok efektif website ketika penjualan tercatat, lalu mengirim laporan penjualan ke POS untuk mencatat transaksi dan mengurangi stok POS. Gangguan pelaporan POS tidak boleh menghapus atau menggandakan transaksi website; status pelaporan harus dapat direkonsiliasi ([lihat OPN-005](#opn-005)). | Baseline; API contract partially open |
| BR-015 | Sistem harus menghitung subtotal, ongkir, PPh 22 yang aktif, dan total transaksi. Seluruh subtotal klasifikasi yang melewati ambang digabung, dibagi `1,11`, lalu dikalikan persentase PPh 22 menjadi satu nilai yang ditampilkan terpisah pada cart, checkout, invoice, dan laporan. Detail tarif multi-klasifikasi serta pembulatan mengikuti [OPN-006](#opn-006). | Baseline; formula resolved, rate/rounding partial |
| BR-016 | Website harus menerbitkan invoice untuk setiap transaksi dan menampilkannya di website serta menyediakan unduhan PDF. Bagian atas memuat nama toko, alamat, dan nomor kontak; bagian bawah memuat nama legal perusahaan, NPWP perusahaan, dan nomor akun reseller terdaftar. Invoice juga memuat jumlah, nama, SKU, harga satuan, total harga setiap item, total pembelian keseluruhan, dan nilai PPh 22 jika berlaku. Identitas dikelola admin melalui pengaturan website. Pelanggan dapat memilih penyampaian melalui WhatsApp atau email. Format nomor, nama legal perusahaan, format nomor akun reseller, dan kontrak channel mengikuti [OPN-008](#opn-008) dan [OPN-022](#opn-022). | Baseline; layout/channel resolved, identifiers partial |
| BR-017 | Satu pelanggan dapat memiliki lebih dari satu invoice. | Baseline |
| BR-018 | Pembayaran dilakukan melalui transfer bank dan diverifikasi admin. | Baseline |
| BR-019 | Pelanggan harus dapat mengunggah bukti pembayaran. Bukti pembayaran disimpan selama lima tahun; batas ukuran dan tipe file mengikuti validasi teknis. | Baseline |
| BR-020 | Admin harus dapat menerima atau menolak bukti pembayaran. | Baseline |
| BR-021 | Admin dapat membatalkan transaksi hanya pada tanggal kalender yang sama dengan transaksi; website menyimpan sumber `ADMIN`, admin pelaksana, alasan, dan waktu pembatalan, lalu mencatat retur dan mengembalikan stok efektif secara atomik. | Baseline |
| BR-022 | Setiap retur website harus dilaporkan ke POS untuk mencatat retur dan menambah stok POS. Laporan retur tidak boleh diterapkan di POS sebelum laporan penjualan asal berhasil diterima atau direkonsiliasi, dan retry tidak boleh membuat retur ganda. | Baseline; API contract partially open |
| BR-023 | Sistem harus mendukung kurir toko untuk seluruh kecamatan di Kota Bandung dan Kabupaten Bandung. Tarif dikelola per area. Target pengiriman H+1 hari kerja dan dapat menjadi H+2 jika kurir tidak tersedia; Minggu dan tanggal merah tidak dihitung. | Baseline; tarif area open |
| BR-024 | Biteship digunakan sebagai provider eksternal untuk standardisasi area melalui Maps API dan pilihan layanan/estimasi ongkir melalui Rates API; origin adalah Jl. Sawahkurung IV No. 18B, Bandung; berat berasal dari masing-masing produk; dimensi dipakai untuk produk berkapasitas besar; Grab dan Gojek ditawarkan untuk layanan same-day. Biteship bukan sumber order, stok, invoice, atau pembayaran website ([lihat OPN-021](#opn-021)). | Baseline; akun provider dan kriteria dimensi open |
| BR-025 | Ongkir terpilih harus masuk ke total transaksi dan invoice. | Baseline |
| BR-026 | Sistem harus menyediakan laporan transaksi dan omzet per periode dan wilayah, dengan nilai PPh 22 ditampilkan sebagai komponen terpisah. Order mulai dihitung sebagai omzet ketika berstatus `SHIPPED`. | Baseline |
| BR-027 | Sistem harus mempertahankan jejak audit untuk tindakan administratif kritis. | [Proposed; lihat OPN-015](#opn-015) |
| BR-028 | Kegagalan POS atau Biteship harus dapat ditelusuri dan tidak boleh diam-diam menghasilkan data transaksi salah. | [Proposed; lihat OPN-015](#opn-015) |
| BR-029 | Sistem harus dapat ditingkatkan kapasitasnya tanpa mengubah domain bisnis utama. | [Proposed; lihat OPN-015](#opn-015) |
| BR-030 | Pesanan yang belum dibayar hanya berlaku pada hari pembuatannya dan otomatis dibatalkan pada hari kalender berikutnya dalam zona waktu `Asia/Jakarta`; website menyimpan sumber `SYSTEM`, alasan kedaluwarsa, dan waktu pembatalan, lalu membuat retur website serta menjadwalkan laporan retur POS secara idempotent. | Baseline |
| BR-031 | Ketika order baru berhasil dibuat, sistem harus memberi tahu admin melalui indikator notifikasi merah di website dan pesan WhatsApp ke `081546407702` dengan isi minimum `Cek Order masuk`. Indikator website dianggap dibaca ketika admin membuka daftar pesanan yang akan diproses. Provider, credential, retry, dan fallback mengikuti [OPN-023](#opn-023). | Baseline; provider contract open |
| BR-032 | Reseller versi pertama adalah pelanggan terdaftar yang disetujui admin. Reseller aktif dapat melihat harga dan memesan melalui website atau WhatsApp. | Baseline; alur pencatatan order WhatsApp partial |
| BR-033 | Admin dapat menggabungkan beberapa order menjadi satu pengiriman hanya jika seluruh order memakai alamat tujuan yang sama. Perhitungan ongkir, hubungan invoice, dan sinkronisasi status mengikuti [OPN-014](#opn-014). | Baseline; transaction handling partial |
| BR-034 | Untuk metode yang memiliki resi, resi wajib diisi sebelum status `SHIPPED`; kurir toko boleh tanpa resi. Setelah dikirim, pelanggan menerima tautan konfirmasi melalui WhatsApp. Klik konfirmasi mengubah order menjadi `COMPLETED` dan memberikan poin. Sistem otomatis menyelesaikan order setelah lima hari kerja sejak `SHIPPED` jika tidak ada kendala. Order bermasalah tetap `SHIPPED` dengan penanda `TERKENDALA` sampai admin menyelesaikan kendala. Aturan poin dan mekanisme WhatsApp mengikuti [OPN-020](#opn-020). | Baseline; points/provider details partial |

## 10. Business Rules

| ID | Aturan |
|---|---|
| RULE-001 | Akun yang belum disetujui tidak boleh checkout. |
| RULE-002 | SKU atau external product ID dari POS harus unik dan stabil. |
| RULE-003 | Snapshot stok awal hari berasal dari POS. Website mengurangi stok efektif ketika penjualan web dicatat dan menambahnya kembali ketika retur web dicatat, lalu mencocokkannya melalui sinkronisasi penuh harian atau pengecekan stok per produk. |
| RULE-004 | SKU dan field master produk selain nama tampilan mengikuti POS. Nama tampilan, gambar, deskripsi, dan data presentasi lain dapat dikelola melalui website; sinkronisasi POS tidak boleh menimpa override lokal tersebut. |
| RULE-005 | Setiap produk wajib memiliki tiga jenis harga dari POS: `ECERAN`, `PARTAI`, dan `GROSIR`. Harga `ECERAN` disimpan tetapi tidak ditampilkan pada storefront fase saat ini. |
| RULE-006 | Nilai harga, jenis harga terpilih, nama produk, SKU, ongkir, PPh 22, dan total disimpan sebagai snapshot transaksi. Jika subtotal suatu klasifikasi melewati ambang, seluruh subtotal klasifikasi itu masuk dasar. Dasar semua klasifikasi terpicu digabung; `pph22_amount = (dasar gabungan / 1,11) × tarif PPh 22`, lalu ditampilkan terpisah pada cart, checkout, invoice, dan laporan. Tarif multi-klasifikasi dan pembulatan mengikuti [OPN-006](#opn-006). |
| RULE-007 | Upload bukti pembayaran tidak otomatis membuat transaksi berstatus lunas. |
| RULE-008 | Pembayaran dianggap sah setelah diverifikasi admin. |
| RULE-009 | Pembatalan menggunakan zona waktu `Asia/Jakarta`. |
| RULE-010 | Transaksi mulai dihitung sebagai omzet ketika order berstatus `SHIPPED`; `CANCELLED` tidak dihitung. |
| RULE-011 | Kegagalan Biteship tidak boleh otomatis menghasilkan ongkir Rp0; pelanggan diminta menghubungi admin. |
| RULE-012 | Produk yang hilang dari respons POS tidak langsung dihapus permanen. |
| RULE-013 | Sinkronisasi POS harus idempotent dan tidak membuat duplikasi. |
| RULE-014 | Pemesanan/pickup, label, dan tracking kurir melalui API Biteship dilakukan di luar baseline website. |
| RULE-015 | Perubahan data produk atau identitas toko setelah order tidak mengubah invoice lama; invoice menggunakan snapshot identitas toko, item, harga, total, dan PPh 22 transaksi. |
| RULE-016 | Data contoh harus deterministik, idempotent, menggunakan identifier stabil, tidak menimpa enrichment lokal, dan hanya aktif pada environment non-production. |
| RULE-017 | Data contoh bukan bukti bahwa koneksi POS production telah lulus; go-live mensyaratkan koneksi POS production berhasil diuji. |
| RULE-018 | Minimum harga partai adalah konfigurasi global website dengan nilai awal lima unit. Transaksi memenuhi syarat jika sedikitnya satu baris produk/SKU mencapai minimum; kuantitas SKU berbeda tidak digabung. Setelah syarat terpenuhi, harga partai berlaku untuk seluruh item dalam order. Jika item juga memenuhi minimum grosir, harga partai tetap dipakai. |
| RULE-019 | Ambang PPh 22 berbasis nilai belanja pada klasifikasi terpilih, bukan batas kuantitas maksimum dan bukan alasan untuk menolak checkout. Persentase `0%` menonaktifkan pungutan untuk aturan tersebut. |
| RULE-020 | Pesanan tanpa pembayaran yang masih aktif pada hari pembuatannya otomatis menjadi `CANCELLED` pada hari kalender berikutnya. |
| RULE-021 | Commit transaksi website menjadi event pembuatan invoice, pengurangan stok efektif, dan pembuatan laporan penjualan POS dengan external reference unik. |
| RULE-022 | Pembatalan yang valid membuat retur website dan menambah stok efektif secara atomik; laporan retur POS dikirim idempotent setelah laporan penjualan asal berhasil diterima atau direkonsiliasi. |
| RULE-023 | POS menerima laporan penjualan dan retur dari website; POS bukan penerbit invoice atau source of truth lifecycle order website. Status acknowledgement digunakan untuk rekonsiliasi, bukan untuk membatalkan transaksi lokal yang sah. |
| RULE-024 | Operasi pelaporan POS wajib memiliki external reference/idempotency mechanism yang disepakati sebelum integration acceptance. |
| RULE-025 | Status setelah pembayaran diterima adalah `PROCESSING`, `PACKED`, `SHIPPED`, lalu `COMPLETED`. Nomor resi wajib sebelum `SHIPPED` hanya untuk metode yang memiliki resi. Pelanggan dapat menyelesaikan order melalui tautan konfirmasi WhatsApp; sistem menyelesaikan otomatis setelah lima hari kerja sejak `SHIPPED` jika tidak ada penanda `TERKENDALA`. Melihat resi tidak memicu `COMPLETED`. |
| RULE-026 | Notifikasi WhatsApp menggunakan perilaku bunyi aplikasi/perangkat WhatsApp; website tidak membuat audio notifikasi WhatsApp sendiri. |
| RULE-027 | Pembatalan admin dan pembatalan otomatis memakai satu status `CANCELLED`. Perbedaannya disimpan sebagai `cancellation_source` bernilai `ADMIN` atau `SYSTEM`, dilengkapi alasan, waktu, dan `cancelled_by_user_id` untuk sumber `ADMIN`; rincian order menampilkan keterangan sumber pembatalan. |
| RULE-028 | Penggabungan pengiriman hanya boleh untuk order dengan alamat tujuan yang sama dan tidak menggabungkan nomor order atau invoice. Aturan pembebanan ongkir serta sinkronisasi status tetap mengikuti OPN-014. |
| RULE-029 | Bukti pembayaran disimpan lima tahun sejak diunggah, kecuali kewajiban hukum mengharuskan lebih lama. |

## 11. Proses Bisnis Utama

Diagram pada bagian ini adalah **process flow**: panah menunjukkan urutan
eksekusi. Notasi yang digunakan konsisten dengan
[User Flows](../design/USER_FLOWS.md#11-notasi-flowchart):

| Makna | Bentuk Mermaid |
|---|---|
| Mulai/selesai | Terminator/stadium `(["..."])` |
| Input pengguna atau output sistem | Jajar genjang `[/"..."/]` |
| Proses/aktivitas | Persegi panjang `["..."]` |
| Keputusan/kondisi | Diamond `{"..."}` |
| Data persisten/snapshot | Silinder `[("...")]` |
| Subprocess atau panggilan sistem eksternal | Predefined process `[["..."]]` |

### 11.1 Registrasi dan Persetujuan

```mermaid
flowchart TD
    A(["Mulai: pelanggan membuka registrasi"]) --> B[/"Isi dan kirim data registrasi"/]
    B --> C["Validasi data server-side"]
    C --> D{"Data valid?"}
    D -->|Tidak| E[/"Tampilkan error validasi"/]
    E --> B
    D -->|Ya| F[("Akun PENDING_VERIFICATION tersimpan")]
    F --> G[/"Admin membuka data pendaftar"/]
    G --> H{"Keputusan admin?"}
    H -->|Setujui| I[("Status ACTIVE tersimpan")]
    H -->|Tolak| J[("Status REJECTED tersimpan")]
    I --> K(["Selesai: pelanggan dapat bertransaksi"])
    J --> L(["Selesai: akses transaksi ditolak"])
```

Penangguhan akun aktif merupakan lifecycle pasca-approval, bukan cabang
keputusan registrasi. Alurnya dirinci pada
[UF-03 Review dan Status Pelanggan](../design/USER_FLOWS.md#uf-03-review-dan-status-pelanggan).

### 11.2 Transaksi dan Pembayaran

```mermaid
flowchart TD
    A(["Mulai: pelanggan aktif berbelanja"]) --> B[/"Pilih produk dan kuantitas"/]
    B --> C["Validasi harga, PPh 22, dan stok"]
    C --> D{"Cart valid?"}
    D -->|Tidak| E[/"Tampilkan koreksi yang diperlukan"/]
    E --> B
    D -->|Ya| F[/"Pilih alamat dan layanan pengiriman"/]
    F --> G["Hitung ongkir dan total final"]
    G --> H[["Commit atomik order, invoice,<br/>stok efektif, dan outbox POS"]]
    H --> I[("Order WAITING_PAYMENT,<br/>invoice, dan laporan PENDING tersimpan")]
    I -.->|Asinkron| J[["Antrekan notifikasi order baru admin"]]
    J --> JA[("Status pengiriman notifikasi tersimpan")]
    JA --> JB(["Cabang notifikasi selesai"])
    I -.->|Asinkron| K[["Kirim laporan penjualan ke POS"]]
    K --> L{"POS menerima laporan?"}
    L -->|Ya| M[("Acknowledgement POS tersimpan")]
    L -->|Tidak atau timeout| N[("Retry atau rekonsiliasi diperlukan")]
    M --> KA(["Cabang pelaporan POS selesai"])
    N --> KA
    I --> O[/"Tampilkan instruksi transfer dan invoice"/]
    O --> P{"Bukti diajukan<br/>pada tanggal transaksi?"}
    P -->|Tidak| Q[["Scheduler membatalkan order<br/>pada hari berikutnya"]]
    Q --> R[("Order CANCELLED, sumber SYSTEM,<br/>alasan kedaluwarsa, dan<br/>laporan retur PENDING tersimpan")]
    R --> S(["Selesai: pesanan dibatalkan otomatis"])
    P -->|Ya| T[/"Pelanggan mengunggah bukti pembayaran"/]
    T --> U[/"Admin memeriksa bukti pembayaran"/]
    U --> V{"Pembayaran diterima?"}
    V -->|Tidak| W[("Status pembayaran REJECTED tersimpan")]
    W --> O
    V -->|Ya| X[["Proses fulfillment:<br/>PROCESSING → PACKED → SHIPPED → COMPLETED"]]
    X --> Y(["Selesai melalui konfirmasi WhatsApp<br/>atau auto-complete 5 hari kerja"])
```

Resi wajib hanya untuk metode yang memilikinya. Tautan konfirmasi WhatsApp atau
auto-complete lima hari kerja menyelesaikan order; penanda `TERKENDALA` menahan
scheduler. Detail provider, kalender, dan poin mengikuti [OPN-020](#opn-020).

### 11.3 Sinkronisasi POS

```mermaid
flowchart TD
    A(["Mulai: scheduler atau rekonsiliasi"]) --> B{"Jenis sinkronisasi?"}
    B -->|Penuh harian| C[["Ambil kategori, produk,<br/>harga, dan seluruh stok dari POS"]]
    B -->|Stok per produk| D[["Panggil GetStockByProduct"]]
    C --> E{"Respons valid?"}
    D --> E
    E -->|Tidak| F[("Error sinkronisasi tersimpan;<br/>snapshot terakhir dipertahankan")]
    F --> G(["Selesai: perlu retry atau investigasi"])
    E -->|Ya| H["Upsert field POS dan<br/>pertahankan pelengkap lokal"]
    H --> I["Rekonsiliasi acknowledgement<br/>laporan penjualan dan retur"]
    I --> J[("Snapshot, ledger, dan log sinkronisasi tersimpan")]
    J --> K(["Selesai: data website diperbarui"])
```

## 12. Ukuran Keberhasilan

Nilai target final harus disetujui pada technical kickoff
([open question: lihat OPN-012](#opn-012)).

| ID | Indikator | Target Draft |
|---|---|---|
| KPI-001 | Registrasi yang dapat diproses admin tanpa bantuan teknis | 100% alur normal |
| KPI-002 | Duplikasi produk akibat sinkronisasi | 0 |
| KPI-003 | Enrichment lokal hilang akibat sinkronisasi | 0 |
| KPI-004 | Invoice dengan perhitungan berbeda dari transaksi | 0 |
| KPI-005 | Transaksi tidak sah dari akun belum disetujui | 0 |
| KPI-006 | Aktivitas pembayaran/pembatalan tanpa audit trail | 0 |
| KPI-007 | Ketersediaan aplikasi bulanan | [Open question: OPN-012](#opn-012) |
| KPI-008 | Waktu respons halaman/API persentil ke-95 | Target di SRS |
| KPI-009 | Recovery hasil sinkronisasi gagal | Dapat di-retry tanpa duplikasi |

## 13. Asumsi dan Dependensi

- Klien menyediakan dokumentasi, kredensial, dan environment koneksi POS
  melalui koordinasi dengan Kak Rio setelah alur website berbasis data contoh
  berjalan ([lihat OPN-003](#opn-003) dan [OPN-005](#opn-005)).
- Jika koneksi POS belum tersedia, Webekspres menggunakan data contoh untuk
  development, staging, demo, dan UAT; data contoh tidak digunakan pada
  production.
- POS menyediakan identifier produk yang stabil.
- Koneksi POS dapat diakses dari server production sebelum go-live.
- Kontrak data, rate limit, timeout, dan kebijakan perubahan versi akan
  diberikan sebelum integrasi dimulai.
- Klien menyediakan akun Biteship yang aktif.
- Data rekening bank dan prosedur verifikasi pembayaran diberikan klien.
- Daftar wilayah kurir toko dan tarifnya diberikan klien
  ([lihat OPN-010](#opn-010)).
- Data origin, berat produk, daftar kurir, serta pemetaan area untuk estimasi
  Biteship diberikan klien atau disepakati sebagai aturan operasional; dimensi
  produk diperlukan hanya jika dipakai dalam perhitungan layanan
  ([lihat OPN-021](#opn-021)).
- Lifecycle order, resi kondisional, tautan konfirmasi, auto-complete, dan
  penanda kendala mengikuti [OPN-020](#opn-020); provider, kalender, dan aturan
  poin harus diputuskan sebelum UAT.
  Identifier invoice dan kontrak notifikasi WhatsApp diselesaikan
  sebelum integration acceptance ([lihat OPN-022](#opn-022) dan
  [OPN-023](#opn-023)).
- Infrastruktur production menggunakan shared hosting milik klien. Klien akan
  memberikan akses yang diperlukan kepada developer Webekspres untuk setup dan
  deployment ([lihat OPN-001](#opn-001)).
- Perubahan scope setelah baseline mengikuti change control pada
  [Bagian 2.3](#23-change-control).

## 14. Risiko Bisnis

| ID | Risiko | Dampak | Mitigasi |
|---|---|---|---|
| RSK-001 | Stok penuh hanya disinkronkan sekali sehari sementara transaksi POS lain dapat mengubah stok. | Stok website stale dan terjadi overselling. | Perbarui stok efektif saat transaksi/retur website di-commit, gunakan pengecekan stok per produk saat rekonsiliasi diperlukan, dan pantau selisih ([OPN-005](#opn-005)). |
| RSK-002 | Kontrak API POS berubah. | Sinkronisasi atau pelaporan penjualan/retur gagal. | Versioning, contract test, logging, dan change request ([lihat OPN-005](#opn-005)). |
| RSK-003 | Shared hosting membatasi worker dan resource. | Sinkronisasi terlambat atau berhenti. | Gunakan cron, queue berbasis database/file, monitoring ringan, serta upgrade ke VPS bila batas resource tidak lagi mencukupi ([lihat OPN-001](#opn-001)). |
| RSK-004 | Biteship lambat/tidak tersedia. | Checkout tertunda. | Timeout dan fallback manual yang disetujui ([lihat OPN-016](#opn-016)). |
| RSK-005 | Lonjakan traffic atau bot. | Aplikasi lambat/tidak tersedia. | CDN, cache, rate limit, monitoring, dan scale-up. |
| RSK-006 | Media produk dan bukti pembayaran membesar. | Storage/bandwidth habis. | Object storage, kompresi, dan kebijakan retensi ([lihat OPN-009](#opn-009)). |
| RSK-007 | Tarif multi-klasifikasi dan pembulatan PPh 22 belum final. | Total checkout, invoice, dan laporan dapat berbeda dari kebijakan keuangan. | Finalisasi skenario tarif/pembulatan melalui [OPN-006](#opn-006) sebelum UAT kalkulasi. |
| RSK-008 | Koneksi POS belum tersedia selama development atau menjelang go-live. | Contract mismatch, keterlambatan integrasi, dan stok production tidak aktual. | Gunakan data contoh hanya untuk delivery non-production; setelah alur website berjalan, koordinasikan akses dengan Kak Rio, finalisasi kontrak, dan lakukan contract test sebelum go-live melalui OPN-019. |
| RSK-009 | Timeout/retry laporan penjualan atau retur POS tidak memiliki idempotency/external reference yang jelas. | Penjualan, retur, atau perubahan stok POS terduplikasi. | Wajibkan external reference unik, acknowledgement/lookup, dan kebijakan retry berbasis status sebelum integration acceptance pada OPN-005. |
| RSK-010 | Laporan retur diterima POS sebelum laporan penjualan asal. | Stok POS dapat bertambah tanpa penjualan asal tercatat. | Tahan laporan retur sampai laporan penjualan asal berstatus berhasil atau sudah direkonsiliasi. |
| RSK-011 | Provider, template, penerima, atau fallback WhatsApp belum final. | Admin tidak menerima notifikasi order baru melalui WhatsApp. | Website badge tetap menjadi channel minimum; selesaikan OPN-023 sebelum integration acceptance WhatsApp. |

## 15. Keputusan Terbuka

Setiap penanda *open question*, `TBD`, atau requirement berstatus `Proposed`
harus merujuk ke item pada bagian ini. Jawaban yang telah disepakati kemudian
dipindahkan ke requirement atau aturan bisnis terkait.

Teks `~~dicoret~~` menandakan pertanyaan atau keputusan tersebut telah selesai.
ID, status, pemilik, dan jawabannya tetap dipertahankan sebagai audit trail.
Item `Partially resolved` tidak dicoret karena masih memiliki keputusan terbuka.

### OPN-001

~~Production menggunakan shared hosting milik klien. Klien akan memberikan
akses yang diperlukan kepada developer Webekspres untuk setup dan deployment.
Batasan runtime dan operasional shared hosting menjadi baseline desain.~~

**Keputusan:** Kepemilikan hosting dan komitmen pemberian akses dikonfirmasi
resmi pada 31 Juli 2026. Kredensial aktual, domain/DNS, akun layanan, versi
runtime/database, dukungan cron, akses log, dan mekanisme backup diverifikasi
pada technical handoff; verifikasi tersebut tidak membuka kembali keputusan
jenis hosting.

**Pemilik:** Klien / Webekspres · **Target:** 28 Juli 2026 · **Status:** Resolved

### OPN-002

Reseller termasuk versi pertama. Reseller didefinisikan sebagai akun yang telah
terdaftar dan disetujui admin, dapat melihat harga, serta dapat memesan melalui
website maupun WhatsApp. Yang masih terbuka adalah apakah order WhatsApp hanya
membuka percakapan/template atau harus dicatat admin sebagai order website,
termasuk dampaknya pada stok, invoice, dan laporan POS.

**Pemilik:** Klien / System Analyst · **Target:** Sebelum implementasi channel WhatsApp · **Status:** Partially resolved

### OPN-003

~~Seluruh field produk yang tersedia di POS dikirim melalui API dan menjadi sumber utama website, termasuk SKU, nama dasar, klasifikasi/kategori, merek, harga, dan stok. Website boleh mengubah nama tampilan produk secara mandiri serta melengkapi gambar, video, deskripsi, dan data presentasi. Sinkronisasi mempertahankan override nama tampilan dan pelengkap lokal; detail field tetap menjadi bagian kontrak integrasi pada OPN-005.~~

**Pemilik:** Klien / Vendor POS · **Target:** 28 Juli 2026 · **Status:** Resolved

### OPN-004

~~Website membuat transaksi dan invoice serta memperbarui stok efektif. POS menerima laporan penjualan dari website untuk mencatat transaksi dan mengurangi stok POS, serta laporan retur untuk mencatat retur dan menambah stok POS. Acknowledgement dan sinkronisasi berikutnya digunakan untuk rekonsiliasi.~~

**Pemilik:** Klien / Vendor POS · **Target:** 28 Juli 2026 · **Status:** Resolved

### OPN-005

Working scheme API:

- master data sekali sehari: `GetCategory`, `GetAllProducts`,
  `GetProductDetail`, dan `GetPriceList`;
- inventory sekali sehari: `GetAllStock`; `GetStockByProduct` dapat dipanggil
  berkala ketika diperlukan untuk pencocokan;
- transaksi per kejadian: website mengirim laporan penjualan dan laporan retur
  ke POS; nama operasi final belum ditetapkan;
- acknowledgement laporan penjualan/retur direkonsiliasi bersama proses
  sinkronisasi.

Kak Rio menjadi PIC POS. Klien akan membuka akses setelah alur website berbasis
data contoh sudah berjalan; kondisi ini menjadi trigger akses dan tidak
memerlukan tanggal kalender terpisah sebelum readiness gate tercapai. Yang
masih terbuka adalah nama operasi final; bentuk URL/method/payload/response;
autentikasi; external reference atau idempotency; acknowledgement/lookup; kode
error, timeout, rate limit; detail data laporan penjualan/retur; timestamp/cutoff
snapshot stok dan cara membuktikan penjualan/retur web sudah tercakup pada
snapshot POS; serta acceptance contract test.

**Pemilik:** Kak Rio / Vendor POS · **Target:** Sebelum integration acceptance · **Status:** Partially resolved

### OPN-006

Biaya persentase dikonfirmasi sebagai PPh 22. Admin dapat memilih klasifikasi,
menetapkan ambang nilai belanja, dan menetapkan persentase termasuk `0%` melalui
website. Ketika subtotal suatu klasifikasi melewati ambang, seluruh subtotal
klasifikasi tersebut menjadi dasar. Dasar dari semua klasifikasi terpicu
digabung terlebih dahulu, dibagi `1,11`, lalu dikalikan persentase PPh 22.
Contoh klien: `(Rp3.000.000 / 1,11) × 0,5%`. Hasil ditampilkan sebagai satu
komponen terpisah pada cart, checkout, invoice, dan laporan. Yang masih terbuka
adalah apakah semua klasifikasi wajib memakai tarif yang sama dan aturan
pembulatan nilai rupiah.

**Pemilik:** Klien / System Analyst · **Target:** Sebelum Sprint 2 · **Status:** Formula resolved; rate/rounding partially open

### OPN-007

~~Pesanan yang belum dibayar hanya berlaku pada hari pembuatannya dan otomatis dibatalkan pada hari kalender berikutnya menggunakan zona waktu `Asia/Jakarta`.~~

**Pemilik:** Klien · **Target:** 28 Juli 2026 · **Status:** Resolved

### OPN-008

Website menerbitkan invoice ketika transaksi berhasil dibuat. Ownership
invoice telah diselesaikan; yang masih terbuka adalah format/awalan nomor
invoice website dan aturan penomorannya.

**Pemilik:** Klien / System Analyst · **Target:** Sebelum Sprint 2 · **Status:** Partially resolved

### OPN-009

~~Bukti pembayaran disimpan selama lima tahun. Batas tipe dan ukuran file
ditetapkan sebagai validasi teknis tanpa mengurangi retensi tersebut.~~

**Pemilik:** Klien / Webekspres · **Target:** 7 Agustus 2026 · **Status:** Resolved

### OPN-010

Kurir toko melayani seluruh kecamatan di Kota Bandung dan Kabupaten Bandung.
Target pengiriman H+1 hari kerja, dapat menjadi H+2 jika kurir tidak tersedia;
Minggu dan tanggal merah tidak dihitung. Tarif per area masih dievaluasi.

**Pemilik:** Klien · **Target:** Sebelum Sprint 3 · **Status:** Partially resolved - tariff open

### OPN-011

~~Order mulai dihitung sebagai omzet ketika berstatus `SHIPPED`.~~

**Pemilik:** Klien · **Target:** 7 Agustus 2026 · **Status:** Resolved

### OPN-012

Baseline internal pengguna bersamaan normal maksimal 50 pengguna dan dapat
lebih rendah. Volume produk/transaksi, target availability, serta skenario
lonjakan beban divalidasi Webekspres melalui pengujian proporsional shared
hosting.

**Pemilik:** Webekspres · **Target:** Sebelum performance test · **Status:** Assumption

### OPN-013

Setiap produk wajib memiliki tiga jenis harga dari POS:

1. **Eceran** untuk penjualan langsung ke konsumen; disiapkan dan disinkronkan,
   tetapi tidak ditampilkan pada storefront fase saat ini.
2. **Partai** jika sedikitnya satu produk/SKU dalam satu struk mencapai minimum
   global yang dapat diubah admin, dengan nilai awal lima unit. Kuantitas SKU
   berbeda tidak dijumlahkan. Setelah terpenuhi, harga partai berlaku untuk
   seluruh item dalam order.
3. **Grosir** dengan minimum kuantitas dan harga yang dapat berbeda per produk;
   contoh klien adalah minimum 200 unit dengan harga Rp10.800.

Jika suatu item memenuhi harga partai dan grosir, harga partai dipakai.

**Pemilik:** Klien / System Analyst · **Target:** 7 Agustus 2026 · **Status:** Resolved

### OPN-014

Beberapa order boleh digabungkan menjadi satu pengiriman hanya jika alamat
tujuannya sama. Nomor order dan invoice tetap terpisah. Yang masih terbuka
adalah pembebanan ongkir, tindakan admin untuk membuat grup, serta sinkronisasi
status/resi antar-order dalam satu pengiriman.

**Pemilik:** Klien / System Analyst · **Target:** Sebelum implementasi pengiriman gabungan · **Status:** Partially resolved

### OPN-015

Persetujuan BR-027 sampai BR-029 sebagai baseline, termasuk batas minimum audit trail, ketahanan integrasi, dan kesiapan scaling.

**Pemilik:** Klien / Webekspres · **Target:** Sebelum MVP baseline · **Status:** Open

### OPN-016

~~Jika pengecekan ongkir Biteship tidak tersedia, website tidak menetapkan
ongkir nol dan meminta pelanggan menghubungi admin.~~

**Pemilik:** Klien · **Target:** 7 Agustus 2026 · **Status:** Resolved

### OPN-017

~~Perwakilan klien/Product Owner: Sylvi; System Analyst Webekspres: Sultan; Project Manager Webekspres: Pak Endang. Persetujuan final berada pada klien dan perubahan efektif menjadi baseline setelah disetujui tertulis oleh klien serta Webekspres pada hari kerja.~~

**Pemilik:** Klien / Webekspres · **Target:** 27 Juli 2026 · **Status:** Resolved

### OPN-018

~~Scope PPh 22 tetap berada dalam MVP. Istilah “batas maksimal penjualan” dikoreksi berdasarkan klarifikasi klien menjadi ambang nilai belanja pada klasifikasi terpilih: transaksi tidak ditolak ketika melewati ambang, tetapi PPh 22 diterapkan sesuai konfigurasi.~~

Detail dasar pengenaan tetap mengikuti [OPN-006](#opn-006).

**Pemilik:** Klien · **Target:** 28 Juli 2026 · **Status:** Resolved - scope corrected and retained

### OPN-019

Data contoh hanya digunakan untuk development, staging, demo, dan UAT.
Production wajib menggunakan data dari POS. Klien akan membuka akses setelah
alur website menggunakan data contoh telah berjalan, dengan Kak Rio sebagai PIC
koordinasi. Diagram operasi, cadence, PIC, dan trigger akses menjadi working
scheme, tetapi kontrak final serta penerimaan hasil uji koneksi masih wajib
diselesaikan sebelum go-live.

**Pemilik:** Klien / Kak Rio / Webekspres · **Target:** Sebelum go-live · **Status:** Partially resolved

### OPN-020

Web menjadi pengelola lifecycle pesanan. Setelah pembayaran diverifikasi,
urutan status adalah `PROCESSING`, `PACKED`, `SHIPPED`, lalu `COMPLETED`.
Nomor resi wajib sebelum `SHIPPED` hanya untuk metode yang memang memiliki
resi; kurir toko tidak memerlukan resi. Pelanggan menerima tautan konfirmasi
penerimaan melalui WhatsApp. Klik konfirmasi menyelesaikan order dan memberi
poin. Jika belum ada konfirmasi dan tidak ada kendala, sistem menyelesaikan
order otomatis setelah lima hari kerja sejak `SHIPPED`. Jika barang belum
diterima atau ada masalah, order tetap `SHIPPED` dengan penanda `TERKENDALA`
sampai admin menyelesaikan kendala.
Pembatalan baseline tetap hanya oleh admin pada hari yang sama atau otomatis
untuk pesanan belum dibayar pada hari berikutnya; website mencatat retur dan
mengirim laporan retur ke POS. Pengembalian dana di luar retur stok standar
membutuhkan change request terpisah.

Yang masih terbuka adalah provider/template tautan WhatsApp, kalender hari
kerja yang dipakai scheduler, aturan perolehan dan penggunaan poin, serta
apakah admin juga boleh menyelesaikan order secara manual setelah menerima
bukti konfirmasi lain.

Melihat status atau nomor resi tidak boleh otomatis mengubah order menjadi
`COMPLETED`.

**Pemilik:** Klien / System Analyst · **Target:** Sebelum implementasi fulfillment · **Status:** Partially resolved - lifecycle resolved; provider/points details open

### OPN-021

Origin pengiriman adalah Jl. Sawahkurung IV No. 18B, Bandung. Berat berasal
dari masing-masing produk; dimensi digunakan untuk produk berkapasitas besar.
Grab dan Gojek ditawarkan untuk layanan same-day sehingga origin/destination
memerlukan koordinat yang valid. Yang masih terbuka adalah definisi produk
berkapasitas besar, nilai fallback jika berat/dimensi kosong, kode layanan
provider yang final, serta pihak yang menanggung akun, aktivasi production,
dan biaya API.

**Pemilik:** Klien / Webekspres · **Target:** Sebelum integrasi Biteship · **Status:** Partially resolved

### OPN-022

Website menerbitkan invoice saat transaksi dibuat. Invoice tampil di website,
dapat diunduh sebagai PDF, dan dapat dikirim melalui WhatsApp atau email sesuai
pilihan pelanggan. Identitas berasal dari pengaturan website. Bagian atas
memuat nama toko, alamat, dan nomor kontak; bagian bawah memuat nama legal
perusahaan, NPWP perusahaan, serta nomor akun reseller terdaftar. Nilai awal
yang tersedia: nama toko `Pixel Komunika`, alamat `Jl. Sawahkurung IV No. 18B,
Bandung`, nomor WhatsApp `081546407702`, dan NPWP
`0821.4146.0442.4000`. Yang masih terbuka adalah nama legal perusahaan,
konfirmasi format NPWP, format nomor akun reseller, format nomor invoice, serta
provider/template pengiriman WhatsApp/email.

**Pemilik:** Klien / Webekspres · **Target:** Sebelum Sprint 2 · **Status:** Partially resolved

### OPN-023

Event minimum adalah order baru kepada admin. Channel minimum adalah indikator
merah di website dan pesan WhatsApp ke `081546407702` dengan isi `Cek Order
masuk`. Indikator dianggap dibaca saat admin membuka daftar pesanan yang akan
diproses. Yang masih terbuka adalah provider/credential, approval template,
serta aturan retry/fallback.

**Pemilik:** Klien / Webekspres · **Target:** Sebelum integration acceptance notifikasi · **Status:** Partially resolved

### 15.1 Klarifikasi Klien 28 Juli 2026

Pertanyaan berstatus `Open` harus dijawab dan dicatat tertulis sebelum
requirement terdampak dipindahkan ke sprint. Q-012 dipertahankan sebagai audit
trail karena sudah dijawab pada 27 Juli 2026.

| ID | Pertanyaan | Requirement Terdampak | Status |
|---|---|---|---|
| Q-001 | ~~Apakah PPh 22 dihapus sepenuhnya dari MVP atau hanya cara perhitungannya yang berubah?~~ | BR-008, BR-015, RULE-006 | Resolved - PPh 22 tetap dalam MVP |
| Q-002 | ~~Apakah istilah “biaya tambahan berbentuk persentase/surcharge” pada proposal dan dokumen saat ini merujuk pada PPh 22?~~ | BR-008, OPN-006 | Resolved - dikonfirmasi sebagai PPh 22 |
| Q-003 | Jika PPh 22 tetap digunakan, siapa yang dikenakan, produk/transaksi apa yang terkena, berapa tarifnya, dan apa dasar perhitungannya? | BR-008, FR-PRC-004 | Partially resolved - klasifikasi, ambang, tarif, dasar seluruh subtotal terpicu, pembagi `1,11`, dan agregasi telah ditetapkan; tarif multi-klasifikasi serta pembulatan mengikuti OPN-006 |
| Q-004 | ~~Apakah batas maksimal penjualan dihapus sepenuhnya untuk semua produk atau hanya produk/pelanggan tertentu?~~ | BR-007, FR-PRC-003 | Resolved - bukan batas maksimum; dikoreksi menjadi ambang nilai per klasifikasi |
| Q-005 | ~~Jika batas dihapus, apakah kuantitas pembelian hanya dibatasi oleh stok tersedia dan tingkat harga?~~ | BR-006, BR-007, aturan stok | Resolved - tidak ada hard limit dari aturan ini; ambang memicu PPh 22 |
| Q-006 | ~~Apakah surcharge ketika batas terlampaui ikut dihapus jika batas maksimal penjualan dihapus?~~ | BR-008, FR-PRC-004 | Resolved - komponen tersebut adalah PPh 22 |
| Q-007 | ~~Apakah tiga tingkat harga berdasarkan kuantitas tetap berlaku tanpa perubahan?~~ | BR-006, OPN-013 | Resolved - minimum partai global dapat diubah admin (awal lima); berlaku ke seluruh order; partai menang terhadap grosir |
| Q-008 | ~~Apakah PPh 22 harus tampil sebagai baris terpisah pada cart, checkout, invoice, dan laporan?~~ | BR-015, RULE-006, laporan | Resolved - ditampilkan sebagai komponen terpisah; pada invoice hanya jika transaksi terkena PPh 22 |
| Q-009 | ~~Apakah konfigurasi klasifikasi, ambang, dan tarif PPh 22 berasal dari POS atau dikelola di website?~~ | BR-008, BR-009, OPN-006 | Resolved - dikelola melalui website |
| Q-010 | ~~Apa saja “poin-poin yang berkenaan” yang juga ingin dihapus atau diubah oleh klien?~~ | Seluruh traceability terkait | Resolved - tidak ada penghapusan scope berdasarkan CR-004 |
| Q-011 | ~~Apakah perubahan ini memengaruhi nilai proposal, scope komersial, atau deadline 45 hari kerja?~~ | MVP baseline dan change control | Resolved - mengikuti plan/proposal awal |
| Q-012 | ~~Siapa yang memberikan persetujuan final dan kapan keputusan tersebut efektif menjadi baseline?~~ | OPN-017 | Resolved - klien memberi persetujuan final; efektif setelah persetujuan tertulis kedua pihak pada hari kerja |
| Q-013 | ~~Apakah data contoh boleh digunakan pada production jika koneksi POS belum tersedia saat go-live?~~ | BR-009, OPN-019 | Resolved - tidak; data production wajib berasal dari POS |
| Q-014 | ~~Kapan koneksi data POS ditargetkan tersedia dan siapa PIC vendor yang memvalidasi kontrak data?~~ | OPN-005, OPN-019 | Resolved - PIC POS adalah Kak Rio; akses dibuka setelah alur website berbasis data contoh berjalan |
| Q-015 | ~~Untuk harga partai, apakah harga partai berlaku untuk seluruh item dalam struk atau hanya item yang memenuhi syarat; dan bagaimana prioritasnya jika item juga memenuhi syarat grosir?~~ | BR-006, OPN-013 | Resolved - berlaku untuk seluruh item dan harga partai diprioritaskan |
| Q-016 | PPh 22 dihitung dari seluruh subtotal klasifikasi, hanya nilai di atas ambang, atau dasar lain; apakah multi-klasifikasi digabung sebelum tarif atau dihitung per klasifikasi lalu dijumlahkan? | BR-008, BR-015, OPN-006 | Partially resolved - seluruh subtotal klasifikasi terpicu digabung, dibagi `1,11`, lalu dikali tarif; tarif multi-klasifikasi dan pembulatan open |
| Q-017 | ~~Status/event apa yang dianggap sebagai penjualan untuk mengurangi stok; apakah perlu reservasi sebelumnya; dan bagaimana retur menambah stok?~~ | BR-011 - BR-014, OPN-004 | Resolved - commit penjualan/retur website memperbarui stok efektif; POS menerima laporan penjualan/retur |
| Q-018 | Apakah nama operasi pada diagram sudah final; bagaimana URL/method, autentikasi, payload/response, pagination, rate limit, dan kode error setiap operasi? | OPN-005, SRS 7.1 | Open - vendor POS |
| Q-019 | Apakah POS mendukung external reference/idempotency dan lookup untuk mencegah laporan penjualan atau retur ganda ketika request timeout dan di-retry? | BR-014, BR-021 - BR-022, OPN-005 | Open - vendor POS |
| Q-020 | ~~Apakah invoice POS menjadi invoice resmi tunggal, atau website tetap membuat nomor/dokumen invoice sendiri?~~ | BR-016, OPN-008, OPN-022 | Resolved - transaksi dan invoice dibuat di website; format nomor masih mengikuti OPN-008 |
| Q-021 | Bagaimana kontrak laporan penjualan dan retur dari website ke POS, termasuk acknowledgement, lookup, dan data yang disinkronkan berkala? | BR-009, BR-014, OPN-005 | Partially resolved - arah dan tujuan bisnis resolved; kontrak vendor POS open |
| Q-022 | Apakah nama, alamat, nomor kontak, dan NPWP toko pada invoice berasal dari payload POS atau konfigurasi website, dan siapa yang menyediakan nilai finalnya? | BR-016, OPN-022, SRS 9.1 | Partially resolved - pengaturan website, layout, data toko/NPWP tersedia; nama legal perusahaan, format NPWP/akun reseller, dan nomor invoice open |
| Q-023 | Siapa admin/nomor penerima WhatsApp, provider apa yang digunakan, bagaimana isi/approval template, retry/fallback, serta kapan indikator website dianggap sudah dibaca? | BR-031, OPN-023 | Partially resolved - penerima, isi minimum, dan read behavior selesai; provider/credential/retry open |
| Q-024 | Apakah payload stok POS memiliki timestamp/cutoff dan bagaimana website mengetahui laporan penjualan/retur mana yang sudah tercakup agar delta stok tidak dihitung dua kali? | BR-009, BR-014, OPN-005 | Open - vendor POS |
| Q-025 | Kondisi apa yang memindahkan order dari `PROCESSING` ke `PACKED`, `PACKED` ke `SHIPPED`, dan `SHIPPED` ke `COMPLETED`; apakah nomor resi wajib; siapa yang mengonfirmasi barang diterima; dan bagaimana menangani barang yang belum diterima? | RULE-025, OPN-020, FR-ORD-005 | Partially resolved - resi kondisional, tautan WhatsApp, auto-complete lima hari kerja, dan penanda TERKENDALA selesai; provider, kalender, serta poin open |
| Q-026 | Jika beberapa klasifikasi terpicu dengan tarif PPh 22 berbeda, tarif mana yang dipakai setelah dasarnya digabung; dan bagaimana pembulatan nilai rupiah dilakukan? | BR-008, BR-015, OPN-006 | Open - klien/keuangan/System Analyst |
| Q-027 | Apa nama legal perusahaan, format NPWP final, aturan nomor akun reseller, dan format/awalan nomor invoice? | BR-016, OPN-008, OPN-022 | Open - klien |
| Q-028 | Untuk order gabungan, bagaimana ongkir dibebankan, siapa yang membuat grup, dan apakah satu resi/status otomatis diterapkan ke semua order? | BR-033, OPN-014 | Open - klien/System Analyst |
| Q-029 | Apakah order WhatsApp dicatat menjadi order website; berapa poin konfirmasi penerimaan, kapan kedaluwarsa, dan dapat dipakai untuk apa? | BR-032, BR-034, OPN-002, OPN-020 | Open - klien |
| Q-030 | Apa definisi produk berkapasitas besar, fallback berat/dimensi, kode layanan Grab/Gojek, serta siapa pemilik akun/biaya Biteship? | BR-024, OPN-021 | Open - klien/Webekspres |

### 15.2 MVP Baseline dan Stage Gates

| Gate | Target | Exit Criteria |
|---|---|---|
| G0 - Clarification | Hari kerja 1-5 | Keputusan kritis dijawab, P0/MVP ditetapkan, API utama dapat diuji, dan item `ON_HOLD` dikeluarkan dari sprint. |
| G1 - Sprint Ready | Sebelum setiap sprint | Item memenuhi Definition of Ready, mempunyai acceptance criteria, dependency tersedia, dan tidak memiliki blocker keputusan. |
| G2 - Increment Accepted | Akhir setiap sprint | Implementasi, test, code review, demo, dan acceptance Sylvi sebagai Product Owner/perwakilan klien selesai. |
| G3 - MVP/UAT | Setelah seluruh P0 selesai | Seluruh acceptance criteria MVP lulus di staging; defect kritis/tinggi ditutup atau diterima tertulis. |
| G4 - Go-Live | Maksimal hari kerja ke-45 | UAT sign-off, backup/rollback, deployment checklist, training, dan persetujuan go-live selesai. |

### 15.3 Rencana Delivery 45 Hari Kerja

| Periode | Aktivitas Utama | Output |
|---|---|---|
| Hari 1-5 | Klarifikasi, API spike, prioritas P0/P1/P2, baseline MVP, dan sprint planning. | G0 lulus dan backlog Sprint 1 ready |
| Hari 6-15 | Sprint 1. | Increment 1 diterima |
| Hari 16-25 | Sprint 2. | Increment 2 diterima |
| Hari 26-35 | Sprint 3 dan system integration test. | Feature complete P0 |
| Hari 36-42 | Regression, security/performance test proporsional, UAT, dan perbaikan. | UAT sign-off |
| Hari 43-45 | Release readiness, deployment, smoke test, training, dan go-live. | Production release |

Waktu tidak otomatis bertambah karena change request. Perubahan yang tidak dapat
ditampung kapasitas harus menukar scope setara, dipindahkan ke fase berikutnya,
atau mendapat persetujuan perubahan jadwal/biaya.

### 15.4 Checklist Pra-Pengembangan

Checklist ini dipakai pada technical kickoff dan sprint planning. Item yang
berstatus blocker tidak boleh dianggap selesai hanya karena ada asumsi lisan.

| ID | Bukti yang Dibutuhkan | Owner | Gate | Status Awal |
|---|---|---|---|---|
| PRE-001 | Keputusan scope PPh 22, ambang nilai klasifikasi, dan dampak scope. | Klien | G0 | Parsial - formula dasar gabungan `/1,11 × tarif` selesai; tarif multi-klasifikasi dan pembulatan tetap OPN-006 |
| PRE-002 | Definisi tiga jenis harga, expiry pesanan, dan lifecycle order yang dapat diuji. | Klien / System Analyst | G1 Sprint 2 | Parsial - harga partai, expiry, dan lifecycle selesai; provider/calendar/poin pada OPN-020 tetap open |
| PRE-003 | Kontrak POS atau dataset seeder tervalidasi beserta pemilik data. | Klien / Kak Rio / Webekspres | G1 integrasi | Parsial - operasi, cadence, PIC, dan trigger akses tersedia; payload, auth, idempotency, pembukaan koneksi aktual, dan contract test pada OPN-005/OPN-019 tetap blocker |
| PRE-004 | Data Biteship dan kurir toko: origin, berat, dimensi bila digunakan, area ID/koordinat, daftar kurir, tarif, SLA, serta akun production. | Klien | G1 pengiriman | Parsial - origin, berat, dimensi produk besar, Grab/Gojek, fallback, area, dan SLA tersedia; tarif, fallback data, kode layanan, akun/biaya tetap OPN-010/OPN-021 |
| PRE-005 | Keputusan invoice dan notifikasi, termasuk channel serta template jika dipilih. | Klien | G1 Sprint 2 | Parsial - layout, sumber website, preview/PDF/channel, penerima, isi, dan read behavior selesai; nama legal, format NPWP/akun reseller/nomor invoice, provider/credential/retry tetap OPN-022/OPN-023 |
| PRE-006 | Hosting, domain/DNS, akun layanan, dan akses environment ditetapkan. | Klien / Webekspres | Sebelum staging | Parsial - shared hosting milik klien dan komitmen pemberian akses developer resolved pada OPN-001/CR-016; kredensial aktual, domain/DNS, akun layanan, kemampuan runtime, cron, log, backup, dan jadwal handoff masih perlu disediakan atau diverifikasi |
| PRE-007 | Skenario UAT, data uji, perwakilan uji, dan proses sign-off disepakati. | Klien / Webekspres | Sebelum UAT | Open |

## 16. Persetujuan

Working baseline ditandatangani pada 27 Juli 2026 dengan item `ON_HOLD` dan
keputusan terbuka sebagai pengecualian eksplisit. Final MVP baseline ditetapkan
setelah keputusan kritis diberi jawaban, Webekspres menyatakan siap
melaksanakan baseline, dan klien memberikan persetujuan final. Persetujuan
harus tercatat tertulis pada hari kerja.

| Peran | Nama | Tanggal | Persetujuan |
|---|---|---|---|
| Perwakilan Klien / Product Owner | Sylvi | 27 Juli 2026 | Approved |
| System Analyst Webekspres | Sultan | 27 Juli 2026 | Approved |
| Project Manager Webekspres | Pak Endang | 27 Juli 2026 | Approved |
