# PROGRESS

Catatan progres implementasi Pixel Komunika.

Dokumen ini dipakai untuk mencatat:
- progress per fase / workstream
- pekerjaan yang sedang berjalan
- blocker / dependency eksternal
- keputusan penting yang memengaruhi delivery
- next steps iterasi berikutnya

## Ringkasan status

- Tanggal update terakhir: 2026-08-12
- Fase aktif: Sprint 2 catch-up selesai; rekonsiliasi keputusan klien 7-11 Agustus berlanjut
- Status umum: fondasi Sprint 2 inti selesai dan teruji; beberapa keputusan operasional/PPh 22 masih menunggu klien
- PIC update: AI agent

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

- Status: selesai fondasi + admin PPh 22; formula PPh 22 final menunggu keputusan klien
- Target hasil:
  - adapter data contoh POS
  - import/upsert produk, harga, stok
  - aturan harga partai/grosir
  - konfigurasi PPh 22
- Catatan:
  - Tabel kategori, brand, produk, enrichment, harga, tax rule, snapshot stok, dan ledger stok sudah dibuat.
  - Jalur import data contoh POS-like bersifat idempotent melalui `catalog:import-sample`.
  - Rule harga partai/grosir diperbaiki (eligibility partai min 5/SKU, tidak agregasi antar-SKU; partai cart-wide saat satu SKU memenuhi syarat).
  - Admin UI `/admin/tax-rules` untuk kelola ambang dan tarif PPh 22 (termasuk 0%).
  - Klarifikasi klien 7-11 Agustus: partai mengalahkan grosir; formula PPh 22 `(subtotal kategori terpicu / 1,11) x tarif` belum diimplementasikan — menunggu keputusan final multi-kategori dan pembulatan.

### Fase 3 — Cart, checkout, shipping, order, invoice

- Status: fondasi tersedia; beberapa requirement operasional terbaru belum tercakup penuh
- Target hasil:
  - cart aktif tunggal
  - alamat customer
  - ongkir Biteship (dikembangkan menggunakan adapter `MockBiteshipShippingService`)
  - order + invoice + expiry unpaid
- Catatan:
  - Skema tabel `carts`, `cart_items`, `orders`, `order_items`, `invoices` sudah terpasang.
  - `CartService` mengelola manipulasi item & kalkulasi PPh 22/partai price.
  - `OrderService` membuat order, snapshot invoice (termasuk identitas toko), `tax_pph22_snapshot`, serta mengurangi stok pada `inventory_snapshot` dan `inventory_ledger`.
  - Scheduler `orders:auto-cancel-unpaid` aktif (daily 00:05).
  - Endpoint POS `GET /api/pos/orders/{order_number}` untuk acknowledgement.
  - Fitur UI Livewire `CartIndex`, `Checkout`, `CustomerOrders`, `OrderDetail`, dan `AdminOrders` sudah lengkap & teruji.
  - Identitas invoice, preview PDF, kanal WhatsApp/email, penggabungan pesanan,
    resi kondisional, kurir toko Bandung, Biteship production, konfirmasi terima,
    status `TERKENDALA`, dan auto-complete 5 hari kerja belum tercakup penuh pada
    fondasi yang ada.

### Fase 4 — Payment, pembatalan/retur, admin workflow

- Status: selesai
- Target hasil:
  - upload bukti bayar privat (`PaymentProof`)
  - approval/reject pembayaran oleh admin (`PaymentService`)
  - pembatalan pesanan + pengembalian stok otomatis (`InventoryLedger` & `InventorySnapshot`)
  - pengajuan & pengolahan retur pesanan (`OrderReturn`)
- Catatan:
  - Skema tabel `payment_proofs` & `order_returns` terpasang.
  - `PaymentService` menangani upload bukti transfer & approval/rejection oleh admin.
  - `OrderService::cancelOrder` & `processReturn` mengembalikan stok produk yang dibatalkan/diretur secara otomatis ke database.
  - Automated test suite lulus 42/42.

### Fase 5 — Reporting, audit, sync resilience, security

- Status: belum mulai
- Target hasil:
  - laporan dasar
  - audit trail
  - retry/idempotency sync POS
  - security checks
- Catatan:
  - TBD

### Fase 6 — Release readiness

- Status: belum mulai
- Target hasil:
  - contract test POS/Biteship
  - staging smoke test
  - shared-hosting readiness
  - UAT, training, sign-off
- Catatan:
  - TBD

## Log progres

### 2026-08-12

- Selesai:
  - Sprint 2 catch-up: fix eligibility partai, tampilan harga storefront, admin PPh 22 UI.
  - Invoice snapshot identitas toko + `tax_pph22_snapshot` pada order.
  - Command `orders:auto-cancel-unpaid` + schedule harian.
  - Endpoint POS `GET /api/pos/orders/{order_number}`.
  - Dokumen `docs/SPRINT_2_DEMO.md`.
  - Test suite 42/42 lulus.
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

- Akses POS production / PIC Kak Rio
- Konfirmasi parameter operasional Biteship
- Provider / template / fallback WhatsApp
- Verifikasi shared hosting: cron, log, backup, worker bounded
- Tarif kurir toko per kecamatan dan kalender hari kerja/libur
- Nama badan usaha legal, aturan nomor invoice, serta nomor akun reseller
- Aturan PPh 22 multi-kategori dan pembulatan
- Aturan ongkir/status untuk penggabungan pesanan serta skema poin loyalitas

## Keputusan penting

- UI storefront memakai Blade + Livewire, bukan SPA.
- Baseline production memakai shared hosting.
- Data contoh dipakai sampai koneksi POS production siap.
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
