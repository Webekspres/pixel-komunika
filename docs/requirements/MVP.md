# Minimum Viable Product (MVP) Baseline

## Website E-Commerce Pelanggan Terverifikasi

| Atribut | Nilai |
|---|---|
| Versi | 0.14 - Klarifikasi Gratis Ongkir Kurir Toko 21 Agustus 2026 |
| Tanggal | Jumat, 21 Agustus 2026 |
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
| MVP-003 | Hak akses guest, pending, reseller aktif, dan admin | FR-AUTH-002; FR-CAT-007; FR-PRC-007; FR-ORD-003 | Guest/pending melihat katalog tanpa harga; reseller aktif yang disetujui admin dapat melihat harga, checkout, dan riwayat sendiri. |
| MVP-004 | Katalog dan pelengkap produk | BR-005; FR-CAT-001 - FR-CAT-007 | SKU/master berasal dari POS; admin dapat mengubah nama tampilan serta melengkapi gambar, video, deskripsi, dan presentasi produk tanpa ditimpa sinkronisasi. |
| MVP-005 | Harga eceran, partai, dan grosir | BR-006; FR-PRC-001 - FR-PRC-002, FR-PRC-005 - FR-PRC-008 | Minimum global partai dapat diubah admin (awal lima unit). Jika satu SKU mencapainya, harga partai berlaku untuk seluruh order dan menang terhadap grosir. |
| MVP-006 | Integrasi master data dan inventory POS | BR-009 - BR-013; FR-POS-001 - FR-POS-016 | Master data dan seluruh stok disinkronkan sekali sehari; stok produk tertentu dapat dicocokkan berkala; data contoh digunakan sampai alur website berjalan, lalu akses POS dikoordinasikan dengan Kak Rio. |
| MVP-007 | Stok efektif dan rekonsiliasi | BR-011 - BR-014; FR-POS-007 - FR-POS-012, FR-POS-017 - FR-POS-020 | Stok efektif berkurang ketika penjualan website dicatat, bertambah ketika retur website dicatat, lalu laporan penjualan/retur direkonsiliasi ke POS tanpa perubahan ganda. |
| MVP-008 | Keranjang dan checkout | BR-014 - BR-015; FR-CART-001 - FR-CART-006 | Pelanggan aktif dapat mengelola cart, alamat, pengiriman, dan melihat subtotal, PPh 22, ongkir, serta total yang dihitung server-side; PPh 22 tampil sebagai komponen terpisah. |
| MVP-009 | Order, invoice, riwayat, dan expiry | BR-014, BR-016 - BR-017, BR-030, BR-034; FR-POS-017, FR-ORD-001 - FR-ORD-005, FR-ORD-009 - FR-ORD-013 | Invoice tampil di website, dapat diunduh PDF, dan dikirim melalui WhatsApp/email; identitas toko di atas serta perusahaan/NPWP/akun reseller di bawah. Resi wajib hanya bila tersedia; tautan WhatsApp atau scheduler lima hari kerja menyelesaikan order, sedangkan `TERKENDALA` menahan scheduler. |
| MVP-010 | Pembayaran transfer manual | BR-018 - BR-020; FR-PAY-001 - FR-PAY-007 | Pelanggan mengunggah bukti secara privat; admin menerima/menolak; status dan audit tercatat. |
| MVP-011 | Pembatalan, retur, dan rekonsiliasi stok | BR-021 - BR-022; FR-ORD-006 - FR-ORD-008; FR-POS-012, FR-POS-018 | Pembatalan yang valid memakai satu status `CANCELLED`, menyimpan sumber `ADMIN`/`SYSTEM`, pelaku admin jika ada, alasan, dan waktu; kemudian membuat retur website, mengembalikan stok efektif, serta melaporkan retur ke POS setelah laporan penjualan asal diterima tanpa membuat retur ganda. |
| MVP-012 | Pengiriman kurir toko dan Biteship | BR-023 - BR-025, BR-033; FR-SHP-001 - FR-SHP-008 | Kurir toko melayani seluruh kecamatan Kota/Kabupaten Bandung dengan SLA H+1 hari kerja. Subtotal barang + PPh 22 sedikitnya Rp1.000.000 mendapat ongkir Rp0; nilai di bawahnya memakai tarif area. Biteship memakai origin toko, berat produk, dimensi produk besar, dan Grab/Gojek same-day; kegagalan meminta pelanggan menghubungi admin. Order beralamat sama dapat digrup untuk pengiriman. |
| MVP-013 | Laporan dasar | BR-026; FR-RPT-001 - FR-RPT-003, FR-RPT-005 | Admin melihat transaksi, omzet, dan PPh 22 berdasarkan periode/area; omzet mulai saat `SHIPPED` dan tidak dihitung ganda saat `COMPLETED`. |
| MVP-014 | Audit dan penanganan gangguan | BR-027 - BR-028; FR-POS-020; FR-AUD-001, FR-AUD-004; FRD Bagian 15 | Pelaporan penjualan/retur ke POS dan gangguan sinkronisasi tercatat; percobaan ulang tidak membuat laporan atau perubahan stok ganda. |
| MVP-015 | Security dan authorization | SRS Bagian 12.3 | CSRF, validation, policy, rate limit, secure session, private upload, dan secret management lulus test relevan. |
| MVP-016 | Release readiness | SRS Bagian 15 - 18 | Shared hosting milik klien menjadi target production; akses deployment developer, domain/DNS, runtime/database, cron, log, backup/rollback, staging, CI, smoke test, UAT, training, dan sign-off go-live tersedia serta terverifikasi sebelum rilis. |
| MVP-017 | Ambang klasifikasi dan PPh 22 | BR-007 - BR-008; FR-PRC-003 - FR-PRC-004 | Seluruh subtotal klasifikasi terpicu digabung; `PPh 22 = (dasar / 1,11) × tarif`. Tarif multi-klasifikasi dan pembulatan wajib ditetapkan sebelum UAT. |
| MVP-018 | Notifikasi order baru kepada admin | BR-031; FR-NTF-001 - FR-NTF-002 | Indikator merah dianggap dibaca saat admin membuka daftar proses; WhatsApp dikirim ke `081546407702` dengan isi minimum `Cek Order masuk`. |
| MVP-019 | Reseller dan channel pemesanan | BR-032; FR-RSL-001 - FR-RSL-004 | Akun yang disetujui admin menjadi reseller aktif, memperoleh nomor akun, dapat melihat harga, serta memesan lewat website atau WhatsApp. Pencatatan order WhatsApp harus diputuskan sebelum UAT. |
| MVP-020 | Retensi bukti pembayaran | BR-019; FR-PAY-002, FR-PAY-006 | Bukti pembayaran privat dipertahankan selama lima tahun dan hanya dapat diakses pihak berwenang. |

## 4. Data Contoh Saat Koneksi POS Belum Tersedia

Jika koneksi POS belum tersedia:

1. Sprint menggunakan data contoh deterministik melalui jalur import/upsert yang sama
   dengan adapter POS.
2. Dataset minimal mencakup produk aktif/nonaktif; harga eceran, partai, dan
   grosir; minimum grosir; aturan ambang klasifikasi; serta stok
   tersedia/menipis/habis.
3. SKU dan external ID stabil; menjalankan data contoh berulang kali tidak membuat
   duplikasi atau menimpa enrichment lokal.
4. Data contoh dapat digunakan untuk local, staging, demo, dan UAT.
5. Data contoh tidak diizinkan sebagai sumber produk atau stok production.
6. Setelah alur website berbasis data contoh berjalan, Webekspres
   mengoordinasikan pembukaan akses POS dengan Kak Rio sebagai PIC.
7. Go-live mensyaratkan koneksi POS tersedia dan berhasil diuji melalui
   [OPN-019](BRD.md#opn-019).
8. Ketika koneksi POS tersedia, contract test wajib lulus sebelum go-live.
9. Adapter data contoh mensimulasikan acknowledgement berhasil, gagal, timeout
   ambigu, serta external reference laporan penjualan dan retur POS.

## 5. Tidak Termasuk P0 Saat Ini

| Kategori | Item | Status |
|---|---|---|
| P1 | Reset password | Dikerjakan jika kapasitas tersedia |
| P1 | Pencarian/filter katalog lanjutan | Dikerjakan jika kapasitas tersedia |
| P1 | Sinkronisasi manual dan monitoring lanjutan | Dikerjakan setelah alur sinkronisasi minimum aman |
| P1 | Export laporan CSV/XLSX | Format masih TBD |
| P2 | Optimasi/scale-out lanjutan di luar baseline traffic | Fase berikutnya berdasarkan metrics |

## 6. MVP Acceptance Gate

MVP dapat dinyatakan selesai apabila:

- seluruh item P0 disetujui sebagai baseline dan memenuhi Definition of Done;
- seluruh acceptance criteria P0 memiliki test case dan lulus di staging;
- alur end-to-end registrasi sampai order selesai dan nomor resi tampil lulus
  UAT;
- resi kondisional, tautan konfirmasi WhatsApp, auto-complete lima hari kerja,
  penanda `TERKENDALA`, dan pencegahan poin ganda lulus UAT; provider, kalender,
  dan nilai/masa berlaku/penggunaan poin telah diputuskan melalui OPN-020;
- pembatalan admin dan sistem menghasilkan status `CANCELLED` yang sama, tetapi
  keterangan sumber, alasan, waktu, dan pelaku admin jika ada tersimpan serta
  tampil dengan benar pada detail order;
- invoice menampilkan layout identitas, nomor akun reseller, preview website,
  PDF, dan pilihan channel serta mempertahankan snapshot historisnya;
- koneksi POS production yang dikoordinasikan dengan Kak Rio tersedia dan
  contract test operasi master data, inventory, laporan penjualan/retur,
  acknowledgement/idempotency, ordering laporan, serta rekonsiliasi lulus;
- indikator order baru website dan read behavior lulus UAT; notifikasi WhatsApp
  lulus setelah provider, template, credential, dan fallback disetujui;
- alur reseller, pemesanan WhatsApp, dan pengiriman gabungan lulus setelah
  pencatatan transaksi, ongkir, resi, invoice, dan status diputuskan;
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
