# PROGRESS

Catatan progres implementasi Pixel Komunika.

Dokumen ini dipakai untuk mencatat:
- progress per fase / workstream
- pekerjaan yang sedang berjalan
- blocker / dependency eksternal
- keputusan penting yang memengaruhi delivery
- next steps iterasi berikutnya

## Ringkasan status

- Tanggal update terakhir: 2026-09-01
- Fase aktif: Fase 6 (release readiness); blocker utama integrasi eksternal (POS/WA/Biteship live)
- Status umum: alur web end-to-end usable dengan data contoh; ±75% MVP; test suite 146/146 lulus
- Handover lengkap: [`docs/HANDOVER.md`](HANDOVER.md)
- PIC update: Kris Adiwinata (handover)

## Roadmap ringkas

- `Fase 1` Identity & access
- `Fase 2` Catalog, pricing, stock, dan data contoh POS
- `Fase 3` Cart, checkout, shipping quote, order, invoice (Selesai dengan Mock Adapter)
- `Fase 4` Payment, cancellation/return, admin workflow
- `Fase 5` Reporting, audit, sync resilience, security
- `Fase 6` Release readiness, UAT, dan go-live

## Progress per fase

### Fase 1 — Identity & access

- Status: selesai
- Target hasil:
  - registrasi, login, logout
  - approval admin
  - status customer `pending/active/rejected/suspended`
  - guard akses guest/pending/active/admin
- Catatan:
  - Registrasi, login/logout, approval admin, dashboard akun, dan guard `admin` / `active.customer` sudah aktif.
  - Harga pada beranda hanya muncul untuk admin atau customer aktif.

### Fase 2 — Catalog, pricing, stock, dan data contoh POS

- Status: selesai fondasi + admin PPh 22; multi-rate/pembulatan PPh tetap provisional (OPN-006)
- Target hasil:
  - adapter data contoh POS
  - import/upsert produk, harga, stok
  - aturan harga partai/grosir
  - konfigurasi PPh 22
- Catatan:
  - Tabel kategori, brand, produk, enrichment, media, harga, tax rule, snapshot stok, dan ledger stok sudah dibuat.
  - Jalur import data contoh POS-like bersifat idempotent melalui `catalog:import-sample` / `pos:sync-masters`.
  - Partai cart-wide mengalahkan grosir; PPh 22 memakai `(basis / 1,11) × tarif` dengan rounding config.
  - Admin UI `/admin/tax-rules` untuk kelola ambang dan tarif PPh 22 (termasuk 0%).

### Fase 3 — Cart, checkout, shipping, order, invoice

- Status: selesai untuk scope tanpa POS (PDF, gratis ongkir, konfirmasi penerimaan)
- Target hasil:
  - cart aktif tunggal
  - alamat customer
  - ongkir Biteship (mock/live siap) + kurir toko Bandung
  - order + invoice + expiry unpaid + shipment/payment rows
- Catatan:
  - Checkout menulis `order_charge_components`, `shipments`, `payments`, outbox POS sale, notifikasi NEW_ORDER.
  - Scheduler `orders:auto-cancel-unpaid` + `orders:auto-complete-shipped` (skip `TERKENDALA`).
  - Endpoint POS `GET /api/pos/orders/{order_number}` tetap satu-satunya HTTP POS.
  - Detail pesanan pelanggan menampilkan rekening tujuan pembayaran (dari `BankAccount` aktif).
  - Nomor resi tampil di detail order pelanggan/admin; `shipment_group_code` otomatis saat checkout.
  - Invoice HTML + unduh PDF (`barryvdh/laravel-dompdf`, `orders.invoice.download`).
  - Konfirmasi penerimaan: route publik `/konfirmasi-penerimaan/{order}?token=…` + notifikasi WA stub saat `shipped`.
  - Gratis ongkir Kurir Toko bila subtotal + PPh 22 ≥ Rp1.000.000 (`StoreCourierFreeShipping`).
  - Biteship live: `BiteshipShippingService` + config siap; default driver `mock`; butuh `BITESHIP_ORIGIN_AREA_ID`.
  - Kirim invoice via WhatsApp/email channel masih menunggu provider (OPN).

### Fase 4 — Payment, pembatalan/retur, admin workflow

- Status: selesai (service-level + UI admin)
- Catatan:
  - `payments` + `retain_until` 5 tahun; audit pada verify/reject.
  - Cancel menyimpan metadata `ADMIN`/`SYSTEM` + `sales_returns` + return outbox.
  - `processReturn` mengembalikan stok dan mengantri `WEB_RETURN_REPORT`.
  - Halaman `/admin/payments` direvamp menjadi Livewire: tabel terpadu dengan status tabs, search/filter bank, dan modal review 2 kolom; bukti streaming lewat temporary signed URL (privat).
  - Daftar `/admin/orders` memakai pola detail-first + dropdown action (batal same-day, tandai `TERKENDALA`, transisi status ada di detail order).
  - Transisi status diselaraskan dengan FRD: pembayaran diterima -> order `processing`; ditolak -> order `payment_rejected` (pelanggan dapat unggah ulang).

### Fase 5 — Reporting, audit, sync resilience, security

- Status: fondasi + UI audit search + throttle checkout/upload selesai; CI dependency scan belum
- Target hasil:
  - laporan dasar
  - audit trail
  - retry/idempotency sync POS
  - security checks
- Catatan:
  - `ReportingService` omzet dari status `shipped`/`completed` (satu snapshot, tanpa double-count logika terpisah); UI `/admin/reports` filter periode + kecamatan.
  - `AuditLogger` + `audit_logs`; aksi kritis tercatat; transaksi + `lockForUpdate` pada mutasi stok/order.
  - UI `/admin/audit-logs` (FR-AUD-002): filter actor, action, entity, periode.
  - Policies Order/PaymentProof/CustomerProfile; bukti privat + signed URL.
  - Throttle: login/register/POS API; checkout `placeOrder` (10/menit); upload bukti (5/menit); konfirmasi penerimaan publik (10/menit).
  - Belum: dependency vulnerability scan di CI (NFR-SEC-010).
  - Sample POS sync + ack simulator; WhatsApp stub (`log`/`fake`).
  - Scheduler: `pos:sync-*`, `pos:dispatch-*-reports`, `notifications:dispatch-pending`.

### Fase 6 — Release readiness

- Status: persiapan berjalan (staging + skrip deploy shared hosting); smoke/UAT/contract test belum
- Target hasil:
  - contract test POS/Biteship
  - staging smoke test
  - shared-hosting readiness
  - UAT, training, sign-off
- Catatan:
  - Estimasi keseluruhan ±75% MVP.
  - Gap fungsional tanpa POS sudah ditutup (PDF, gratis ongkir, konfirmasi penerimaan, audit UI, throttle).
  - Blocker eksternal utama: akses POS production (Kak Rio). Lainnya: Biteship origin ID, WhatsApp produksi, format invoice/akun reseller, PPh 22 final, channel kirim invoice.

## Log progres

### 2026-09-01

- Dokumen handover terpadu [`docs/HANDOVER.md`](HANDOVER.md) dibuat:
  matriks MVP P0, fitur belum/ditunda, arsitektur, setup, testing, blocker OPN.
- Verifikasi test suite: **146/146** lulus (706 assertions).
- Pointer handover ditambahkan ke [`docs/README.md`](README.md).

### 2026-08-27 (lanjutan)

- Selesai gap MVP tanpa menunggu POS:
  - Gratis ongkir Kurir Toko ≥ Rp1jt (`StoreCourierFreeShipping` + Checkout/OrderService).
  - Tautan konfirmasi penerimaan publik + notifikasi stub saat shipped.
  - Unduh invoice PDF (`barryvdh/laravel-dompdf`).
  - UI audit log admin `/admin/audit-logs`.
  - Rate limit checkout & upload bukti bayar.
- Next: smoke/UAT staging; tunggu POS/Biteship origin/WhatsApp produksi.

### 2026-08-27

- Review status vs kode + `docs/sprints/SPRINT_3.md`:
  - Sprint 3 Task 1–6, 8–13 selesai; Task 7 Biteship core ready (menunggu `BITESHIP_ORIGIN_AREA_ID`); WA masih log/fake.
  - Sudah ada (PROGRESS sebelumnya usang): tampilan nomor resi, penggabungan pengiriman (`shipment_group_code`).
  - Belum (saat review pagi): invoice PDF, tautan konfirmasi penerimaan, gratis ongkir Kurir Toko ≥ Rp1jt, UI cari audit log, throttle upload/checkout, CI dependency scan.
- Blocker utama dikonfirmasi: koneksi POS production dari klien; web tetap jalan dengan data contoh.
- Next (tanpa menunggu POS): invoice PDF, tautan konfirmasi, gratis ongkir, UI audit search; lanjut smoke/UAT staging.

### 2026-08-19

- Selesai:
  - Storefront: perbaikan tampilan "Semua Produk" saat kategori kosong, badge enrichment, polish layout katalog & homepage, penyelarasan `DESIGN.md` dengan implementasi.
  - Admin katalog: slug produk read-only dengan auto-fill dari nama tampilan; perbaikan migrasi kompatibel SQLite.
  - Akun/UI: daftar alamat jadi accordion + konfirmasi hapus; input wajib bertanda asterisk; aturan destructive actions di `AGENTS.md`.
  - Pengaturan toko: validasi tab identitas/partai independen; form tambah rekening & tarif kurir dalam modal.
  - Order: rekening tujuan pembayaran tampil di detail pesanan pelanggan.
  - Admin order: daftar detail-first + dropdown action (batal same-day FR-ORD-006, tandai `TERKENDALA`).
  - Admin pembayaran: revamp tabel terpadu + status tabs + search/filter bank + modal review 2 kolom dengan signed URL.
  - Transisi status diselaraskan dengan FRD: `approve -> processing`, `reject -> payment_rejected`.
  - Branch `staging` dibuat dari `dev` dan dipush ke `origin/staging`.
  - Pest 109/109 lulus.
- Next:
  - Gap fungsional: invoice PDF + pilihan channel, tautan konfirmasi WhatsApp, nomor resi di detail order, penggabungan order, UI audit search.
  - Fase 6: deploy & smoke test staging, contract test POS/Biteship, UAT, training, sign-off.
  - Tunggu keputusan eksternal: POS (Kak Rio), Biteship, WhatsApp, format invoice/akun, PPh 22 final.

### 2026-08-13

- Selesai:
  - Migrasi ERD parity (store/bank/media/charges/payments/shipments/POS sync/notifications/audit).
  - Domain services: PPh `/1.11`, fulfillment, reporting, audit, notifications, POS outbox/sample sync, enrichment, customer verification + reseller account.
  - Seed demo lengkap (store, bank, kurir Bandung, customer statuses, sample orders).
  - Pest 52/52 termasuk `BackendMvpReadyTest`.
- Next:
  - Fokus wiring/frontend Livewire terhadap service yang sudah ada.
  - Tunggu OPN eksternal untuk POS HTTP real, WA provider, rounding PPh final.

### 2026-08-12

- Selesai:
  - Sprint 2 catch-up: fix eligibility partai, tampilan harga storefront, admin PPh 22 UI.
  - Invoice snapshot identitas toko + `tax_pph22_snapshot` pada order.
  - Command `orders:auto-cancel-unpaid` + schedule harian.
  - Endpoint POS `GET /api/pos/orders/{order_number}`.
  - Dokumen `docs/SPRINT_2_DEMO.md`.
  - Test suite 42/42 lulus.
  - Nama legal perusahaan ditetapkan `Pixel Komunika` sebagai nilai awal dan
    dapat diubah melalui panel admin.
  - Pertanyaan terbuka dan dokumentasi invoice terkait nama legal diselaraskan.
  - Daftar pertanyaan diringkas agar hanya memuat hal yang belum terjawab;
    keputusan final tetap dicatat pada requirement dan riwayat perubahan.
- Next:
  - Demo acceptance Sprint 2 dengan klien/internal.
  - Reconcile status task di ClickUp Sprint 2.
  - Selaraskan formula PPh 22 dengan keputusan klien setelah jawaban final.

### 2026-08-11

- Selesai:
  - Klarifikasi klien 7-11 Agustus dipetakan ke BRD, FRD, SRS, MVP, data
    dictionary, ERD, user flows, dan dokumen persetujuan klien.
  - Batas ownership POS dan website, reseller V1, invoice, pengiriman,
    notifikasi order, omzet saat `SHIPPED`, retensi bukti bayar 5 tahun, serta
    konfirmasi penerimaan sudah masuk baseline dokumentasi.
- Ditemukan:
  - Formula PPh 22 pada implementasi belum memakai pembagi `1,11`.
  - Beberapa keputusan operasional masih terbuka dan tidak boleh diasumsikan.
- Next:
  - Dapatkan jawaban klien atas pertanyaan terbuka.
  - Iterasi kode untuk shipping, reseller, notifikasi, dan fulfillment setelah keputusan lengkap.

### 2026-08-07

- Selesai:
  - Redesign UI storefront & internal app shell dengan sidebar layout.
  - Fase 3 core (Cart, Address, Checkout, Order, Invoice, Mock Shipping).
  - Fase 4 core (Payment upload, Admin payment verification, Order cancellation & return workflow with automatic inventory stock restoration).
  - Full automated test suite passing (28/28 tests, 121 assertions).
- Sedang dikerjakan:
  - Persiapan Fase 5 (Laporan penjualan, Audit trail, Sync resilience).
- Blocker:
  - Parameter API Biteship production & kredensial POS production (di-bypass menggunakan mock adapter selama pengembangan).
- Next:
  - Implementasi modul Reporting, Audit Trail, dan Security checks (Fase 5).

## Blocker dan dependency eksternal

- **Utama:** akses POS production / PIC Kak Rio (master, stok live, sale/return report, contract test go-live)
- `BITESHIP_ORIGIN_AREA_ID` + parameter operasional Biteship untuk driver live
- Provider / template / fallback WhatsApp produksi
- Verifikasi shared hosting: cron, log, backup, worker bounded
- Nilai tarif kurir toko per kecamatan untuk transaksi di bawah ambang gratis ongkir
- Aturan nomor invoice serta nomor akun reseller
- Aturan PPh 22 multi-kategori dan pembulatan
- Skema poin loyalitas (OPN-020); pencatatan order channel WhatsApp

## Keputusan penting

- UI storefront memakai Blade + Livewire, bukan SPA.
- Baseline production memakai shared hosting.
- Data contoh dipakai sampai koneksi POS production siap.
- Kurir toko gratis untuk subtotal barang + PPh 22 sedikitnya Rp1.000.000;
  transaksi di bawah ambang memakai tarif area dan dikirim H+1 hari kerja.
- Auth dibangun dengan fitur native Laravel session tanpa package auth tambahan.
- Fondasi data katalog mengikuti jalur import/upsert yang sama dengan adapter POS.
- POS menjadi sumber SKU dan data dasar produk; nama tampilan, media, berat, dan
  dimensi dapat dikelola sebagai enrichment website tanpa ditimpa sinkronisasi.

## Cara pakai

Setiap selesai satu iterasi, update minimal:
1. `Ringkasan status`
2. `Progress per fase` yang berubah
3. `Log progres`
4. `Blocker dan dependency eksternal` bila ada perubahan
