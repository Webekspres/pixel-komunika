# Minimum Viable Product (MVP) Baseline

## Website E-Commerce Pelanggan Terverifikasi

| Atribut | Nilai |
|---|---|
| Versi | 0.1 - Candidate MVP untuk review |
| Tanggal | Senin, 27 Juli 2026 |
| Target delivery | 45 hari kerja |
| Product Owner / klien | Sylvi |
| System Analyst | Sultan - Webekspres |
| Project Manager | Pak Endang - Webekspres |
| Status | Candidate P0 - menunggu persetujuan final klien |
| Dokumen induk | [BRD](BRD.md), [FRD](FRD.md), [SRS](SRS.md) |

## 1. Tujuan

Dokumen ini menjadi daftar kerja ringkas untuk MVP. BRD, FRD, dan SRS tetap
menjadi sumber detail. Apabila terdapat konflik, hierarki sumber dan change
control pada BRD berlaku.

MVP berarti alur penjualan minimum dapat digunakan end-to-end dengan aman:
pelanggan mendaftar, disetujui, melihat katalog dan harga sesuai hak akses,
membuat order, memilih pengiriman, menerima invoice, mengirim bukti pembayaran,
serta diproses admin.

## 2. Aturan Prioritas

- **P0 / Must Have** wajib selesai dan lulus UAT untuk go-live.
- **P1 / Should Have** dikerjakan setelah P0 aman jika kapasitas tersedia.
- **P2 / Could Have** dipindahkan ke fase berikutnya tanpa menggagalkan MVP.
- **ON_HOLD / Candidate** tidak boleh masuk sprint.
- Item P0 hanya boleh masuk sprint setelah memenuhi Definition of Ready FRD.
- Perubahan P0 setelah baseline mengikuti change control BRD.

## 3. Daftar P0 / Must Have

| ID | Kapabilitas MVP | Requirement Utama | Acceptance Outcome |
|---|---|---|---|
| MVP-001 | Registrasi dan autentikasi | BR-002; FR-AUTH-001 - FR-AUTH-004 | Pengguna dapat registrasi, login, dan logout dengan validasi serta session yang aman. |
| MVP-002 | Approval dan status pelanggan | BR-003 - BR-004; FR-AUTH-006 - FR-AUTH-009 | Admin dapat menyetujui, menolak, menangguhkan, dan mengaktifkan pelanggan dengan status yang benar. |
| MVP-003 | Hak akses guest, pending, aktif, dan admin | FR-AUTH-002; FR-CAT-007; FR-PRC-007; FR-ORD-003 | Guest/pending dapat melihat katalog tanpa harga; hanya pelanggan aktif dapat checkout dan melihat riwayat sendiri. |
| MVP-004 | Katalog dan enrichment produk | BR-005; FR-CAT-001 - FR-CAT-007 | Produk aktif, kategori, merek, SKU, deskripsi, dan media dapat dikelola tanpa kehilangan enrichment lokal. |
| MVP-005 | Tiga tingkat harga | BR-006; FR-PRC-001 - FR-PRC-002, FR-PRC-005 - FR-PRC-007 | Harga yang benar dipilih berdasarkan kuantitas, tervalidasi saat checkout, dan disimpan sebagai snapshot. Detail mengikuti OPN-013. |
| MVP-006 | Sumber produk/stok POS atau seeder | BR-009 - BR-013; FR-POS-001 - FR-POS-003, FR-POS-007 - FR-POS-009, FR-POS-013 - FR-POS-016 | Sistem dapat dikembangkan dan diuji dengan dataset stabil; API digunakan ketika tersedia tanpa mengubah domain transaksi. |
| MVP-007 | Status dan validasi stok | BR-011 - BR-013; FR-POS-007 - FR-POS-009, FR-POS-011 | Status tersedia/menipis/habis benar dan stok divalidasi kembali sebelum order dibuat. |
| MVP-008 | Keranjang dan checkout | BR-014 - BR-015; FR-CART-001 - FR-CART-006 | Pelanggan aktif dapat mengelola cart, alamat, pengiriman, dan melihat total yang dihitung server-side. |
| MVP-009 | Order, invoice, dan riwayat | BR-016 - BR-017; FR-ORD-001 - FR-ORD-005 | Order/invoice unik dibuat dengan snapshot; pelanggan aktif melihat miliknya dan admin mengelola seluruh order. |
| MVP-010 | Pembayaran transfer manual | BR-018 - BR-020; FR-PAY-001 - FR-PAY-007 | Pelanggan mengunggah bukti secara privat; admin menerima/menolak; status dan audit tercatat. |
| MVP-011 | Pembatalan dan rekonsiliasi stok | BR-021 - BR-022; FR-ORD-006 - FR-ORD-008; FR-POS-012 | Admin hanya dapat membatalkan sesuai aturan hari yang sama dan stok direkonsiliasi secara atomik. |
| MVP-012 | Pengiriman kurir toko dan Biteship | BR-023 - BR-025; FR-SHP-001 - FR-SHP-006 | Pelanggan memilih layanan yang tersedia; ongkir masuk invoice; kegagalan tidak menghasilkan ongkir Rp0. |
| MVP-013 | Laporan dasar | BR-026; FR-RPT-001 - FR-RPT-003, FR-RPT-005 | Admin melihat transaksi dan omzet berdasarkan periode serta area; transaksi batal dikecualikan. |
| MVP-014 | Audit dan penanganan error minimum | BR-027 - BR-028; FR-AUD-001, FR-AUD-004; FRD Bagian 15 | Tindakan kritis dan kegagalan integrasi dapat ditelusuri tanpa menyimpan secret mentah. |
| MVP-015 | Security dan authorization | SRS Bagian 12.3 | CSRF, validation, policy, rate limit, secure session, private upload, dan secret management lulus test relevan. |
| MVP-016 | Release readiness | SRS Bagian 15 - 18 | Staging, CI, backup, rollback, logging, smoke test, UAT, training, dan sign-off go-live tersedia. |
| MVP-017 | Batas pembelian dan komponen biaya | BR-007 - BR-008; FR-PRC-003 - FR-PRC-004 | Batas aktif tervalidasi saat checkout; komponen biaya aktif dihitung dan disimpan sebagai snapshot sesuai OPN-006. |

## 4. Fallback API POS

Jika API POS belum tersedia:

1. Sprint menggunakan seeder deterministik melalui jalur import/upsert yang sama
   dengan adapter POS.
2. Dataset minimal mencakup produk aktif/nonaktif, tiga tingkat harga, serta stok
   tersedia/menipis/habis.
3. SKU dan external ID stabil; menjalankan seeder berulang kali tidak membuat
   duplikasi atau menimpa enrichment lokal.
4. Seeder dapat digunakan untuk local, staging, demo, dan UAT.
5. Seeder tidak otomatis diizinkan sebagai sumber stok production.
6. Go-live tanpa API POS memerlukan persetujuan tertulis melalui
   [OPN-019](BRD.md#opn-019).
7. Ketika API tersedia, contract test wajib lulus sebelum sumber data diganti.

## 5. Tidak Termasuk P0 Saat Ini

| Kategori | Item | Status |
|---|---|---|
| Candidate | Segmentasi/reseller dan pemesanan WhatsApp | Menunggu CND-001/CND-002 |
| Candidate | Penggabungan beberapa transaksi menjadi satu pengiriman | Menunggu CND-003 |
| Candidate | Write-back order atau reservasi stok ke POS | Menunggu CND-004/OPN-005 |
| P1 | Reset password | Dikerjakan jika kapasitas tersedia |
| P1 | Pencarian/filter katalog lanjutan | Dikerjakan jika kapasitas tersedia |
| P1 | Sinkronisasi manual dan monitoring lanjutan | Dikerjakan setelah alur sinkronisasi minimum aman |
| P1 | Export laporan CSV/XLSX | Format masih TBD |
| P2 | Optimasi/scale-out lanjutan di luar baseline traffic | Fase berikutnya berdasarkan metrics |

## 6. MVP Acceptance Gate

MVP dapat dinyatakan selesai apabila:

- seluruh item P0 disetujui sebagai baseline dan memenuhi Definition of Done;
- seluruh acceptance criteria P0 memiliki test case dan lulus di staging;
- alur end-to-end registrasi sampai verifikasi pembayaran lulus UAT;
- strategi sumber data POS/seeder untuk production diputuskan tertulis;
- tidak ada defect kritis atau tinggi yang belum diterima sebagai risiko;
- backup, rollback, monitoring, security check, training, dan smoke test siap;
- Sylvi memberikan UAT dan go-live sign-off pada hari kerja;
- Sultan dan Pak Endang mengonfirmasi technical/release readiness.

## 7. Persetujuan MVP

| Peran | Nama | Tanggal | Status |
|---|---|---|---|
| Product Owner / Perwakilan Klien | Sylvi | TBD | Pending review MVP list |
| System Analyst Webekspres | Sultan | TBD | Pending review MVP list |
| Project Manager Webekspres | Pak Endang | TBD | Pending review MVP list |
