# PROGRESS

Catatan progres implementasi Pixel Komunika.

Dokumen ini dipakai untuk mencatat:
- progress per fase / workstream
- pekerjaan yang sedang berjalan
- blocker / dependency eksternal
- keputusan penting yang memengaruhi delivery
- next steps iterasi berikutnya

## Ringkasan status

- Tanggal update terakhir: 2026-08-04
- Fase aktif: transisi dari Fase 2 ke Fase 3
- Status umum: on track
- PIC update: AI agent

## Roadmap ringkas

- `Fase 1` Identity & access
- `Fase 2` Catalog, pricing, stock, dan data contoh POS
- `Fase 3` Cart, checkout, shipping quote, order, invoice
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

- Status: selesai fondasi
- Target hasil:
  - adapter data contoh POS
  - import/upsert produk, harga, stok
  - aturan harga partai/grosir
  - konfigurasi PPh 22
- Catatan:
  - Tabel kategori, brand, produk, enrichment, harga, tax rule, snapshot stok, dan ledger stok sudah dibuat.
  - Jalur import data contoh POS-like bersifat idempotent melalui `catalog:import-sample`.
  - Rule harga partai/grosir dan agregasi PPh 22 dasar sudah memiliki test.

### Fase 3 — Cart, checkout, shipping, order, invoice

- Status: berikutnya
- Target hasil:
  - cart aktif tunggal
  - alamat customer
  - ongkir Biteship Maps/Rates
  - order + invoice + expiry unpaid
- Catatan:
  - TBD

### Fase 4 — Payment, pembatalan/retur, admin workflow

- Status: belum mulai
- Target hasil:
  - upload bukti bayar privat
  - approval/reject pembayaran
  - pembatalan `ADMIN` / `SYSTEM`
  - retur website + update stok efektif
  - indikator order baru admin
- Catatan:
  - TBD

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

### YYYY-MM-DD

- Selesai:
  - Fase 1 auth foundation: role, profile customer, login/register/logout, approval admin, access guard.
  - Fase 2 foundation: schema catalog/pricing/inventory, importer data contoh, kalkulasi harga dan PPh 22 dasar.
- Sedang dikerjakan:
  - Menyiapkan Fase 3 cart, checkout, shipping quote, order, dan invoice.
- Blocker:
  - Detail parameter Biteship production belum final.
- Next:
  - Tambah alamat, cart aktif tunggal, order draft, invoice snapshot, dan shipment quote placeholder/adapter.

## Blocker dan dependency eksternal

- Akses POS production / PIC Kak Rio
- Konfirmasi parameter operasional Biteship
- Provider / template / fallback WhatsApp
- Verifikasi shared hosting: cron, log, backup, worker bounded

## Keputusan penting

- UI storefront memakai Blade + Livewire, bukan SPA.
- Baseline production memakai shared hosting.
- Data contoh dipakai sampai koneksi POS production siap.
- Auth dibangun dengan fitur native Laravel session tanpa package auth tambahan.
- Fondasi data katalog mengikuti jalur import/upsert yang sama dengan adapter POS.

## Cara pakai

Setiap selesai satu iterasi, update minimal:
1. `Ringkasan status`
2. `Progress per fase` yang berubah
3. `Log progres`
4. `Blocker dan dependency eksternal` bila ada perubahan
