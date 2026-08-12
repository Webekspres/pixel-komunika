# PROGRESS

Catatan progres implementasi Pixel Komunika.

Dokumen ini dipakai untuk mencatat:
- progress per fase / workstream
- pekerjaan yang sedang berjalan
- blocker / dependency eksternal
- keputusan penting yang memengaruhi delivery
- next steps iterasi berikutnya

## Ringkasan status

- Tanggal update terakhir: 2026-08-11
- Fase aktif: rekonsiliasi fondasi Fase 2-4 terhadap klarifikasi klien terbaru
- Status umum: fondasi tersedia, tetapi belum siap dinyatakan sesuai requirement terbaru
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

- Status: fondasi tersedia; aturan harga dan pajak perlu diselaraskan
- Target hasil:
  - adapter data contoh POS
  - import/upsert produk, harga, stok
  - aturan harga partai/grosir
  - konfigurasi PPh 22
- Catatan:
  - Tabel kategori, brand, produk, enrichment, harga, tax rule, snapshot stok, dan ledger stok sudah dibuat.
  - Jalur import data contoh POS-like bersifat idempotent melalui `catalog:import-sample`.
  - Rule harga partai/grosir dan agregasi PPh 22 dasar sudah memiliki test untuk
    baseline lama.
  - Klarifikasi terbaru menetapkan minimal partai default 5 per SKU, partai
    berlaku untuk seluruh order, dan partai mengalahkan grosir.
  - `PriceCalculator::resolvePrice()` saat ini masih memeriksa grosir lebih dulu;
    implementasi dan test perlu diperbaiki sebelum rule dinyatakan selesai.
  - `PriceCalculator::calculatePph22()` saat ini menghitung
    `subtotal kategori x tarif`. Requirement terbaru memakai dasar
    `(subtotal kategori terpicu / 1,11) x tarif`; aturan multi-kategori dan
    pembulatan masih menunggu keputusan klien.

### Fase 3 — Cart, checkout, shipping, order, invoice

- Status: fondasi tersedia; rekonsiliasi requirement terbaru belum selesai
- Target hasil:
  - cart aktif tunggal
  - alamat customer
  - ongkir Biteship (dikembangkan menggunakan adapter `MockBiteshipShippingService`)
  - order + invoice + expiry unpaid
- Catatan:
  - Skema tabel `carts`, `cart_items`, `orders`, `order_items`, `invoices` sudah terpasang.
  - `CartService` mengelola manipulasi item & kalkulasi PPh 22/partai price.
  - `OrderService` membuat order, snapshot invoice, serta mengurangi stok pada `inventory_snapshot` dan `inventory_ledger`.
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
  - Automated test suite lulus 28/28.

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

### 2026-08-11

- Selesai:
  - Klarifikasi klien 7-11 Agustus dipetakan ke BRD, FRD, SRS, MVP, data
    dictionary, ERD, user flows, dan dokumen persetujuan klien.
  - Batas ownership POS dan website, reseller V1, invoice, pengiriman,
    notifikasi order, omzet saat `SHIPPED`, retensi bukti bayar 5 tahun, serta
    konfirmasi penerimaan sudah masuk baseline dokumentasi.
- Ditemukan:
  - Prioritas harga pada implementasi saat ini bertentangan dengan keputusan
    partai mengalahkan grosir.
  - Formula PPh 22 pada implementasi belum memakai pembagi `1,11`.
  - Beberapa keputusan operasional masih terbuka dan tidak boleh diasumsikan.
- Next:
  - Dapatkan jawaban klien atas pertanyaan terbuka.
  - Buat iterasi kode terpisah untuk menyelaraskan pricing, PPh 22, invoice,
    shipping, reseller, notifikasi, dan fulfillment setelah keputusan lengkap.

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
