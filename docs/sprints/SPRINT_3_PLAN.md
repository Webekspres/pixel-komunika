# Sprint 3 — Plan Pengerjaan Sisa Pekerjaan

> Berdasarkan `docs/sprints/SPRINT_3.md` (revisi 18 Agu 2026). Mayoritas task sprint
> sudah terimplementasi; dokumen ini merinci langkah untuk **sisa pekerjaan** yang
> benar-benar tersisa beserta dependensi dan keputusan yang dibutuhkan.

## 1. Konteks & Tujuan

Sprint 3 hampir selesai. Yang tersisa hanya 3 item:

| ID | Item | Blocker | Status |
|---|---|---|---|
| A | Integrasi Biteship live (Maps & Rates) + fallback | Keputusan klien & API key | ⏳ Menunggu |
| B | Provider WhatsApp produksi | Keputusan klien (provider, nomor, template) | ⏳ Menunggu |
| C | Verifikasi ledger retur + cakupan test | Tidak ada | ✅ **Selesai** |

Urutan yang disarankan: **C → A/B** (C **selesai**; A & B bisa paralel setelah keputusan klien turun).

---

## 2. Item C — Verifikasi Ledger Retur & Cakupan Test ✅ **SELESAI**

### Status audit
Terverifikasi: alur retur memakai `restoreStock(..., 'ORDER_RETURNED', ...)`
di `app/Services/OrderService.php:323`, sehingga ledger retur **sudah tercatat**.

### Implementasi test (selesai)
- `tests/Feature/PaymentAndReturnTest.php`: 2 test baru
  - `restores inventory stock via ORDER_RETURNED ledger when return is approved` — asserts `source = ORDER_RETURNED`, `quantity_delta > 0`, snapshot increases
  - `does not modify stock when return is rejected` — asserts no `ORDER_RETURNED` ledger entry
- `tests/Feature/BackendMvpReadyTest.php`: updated existing return test with same assertions

### Verifikasi
```bash
php artisan test --filter="PaymentAndReturnTest"   # 5 tests, 22 assertions ✅
php artisan test --filter="BackendMvpReadyTest"    # 7 tests, 28 assertions ✅
php artisan test                                   # 111 tests, 490 assertions ✅
```

### DoD ✅
- Assertion `source = ORDER_RETURNED` ada di test.
- Semua test hijau; `SPRINT_3.md` konsisten.

---

## 3. Item A — Integrasi Biteship Live (Maps & Rates) + Fallback (Estimasi: L, ±2–3 hari)

### Blocking / keputusan klien
- Kontrak Biteship masih *partial* (BRD `OPN-016`/`OPN-021`, SRS TD-008). Perlu
  persetujuan klien untuk memakai Maps (`areas`) + Rates (`couriers`) — **tanpa**
  order/label/tracking/webhook (di luar baseline).
- API key Biteship (env: `BITESHIP_API_KEY`), origin area ID, dan daftar kurir yang
  diaktifkan.

### Langkah
1. **Konfigurasi**
   - Buat `config/biteship.php`: `base_url` (`https://api.biteship.com`), `api_key`
     (env), `origin` (alamat toko dari `config('store.address')` + `origin_area_id`),
     `timeout` (mis. 5 detik).
   - Tambahkan env keys ke `.env.example`: `BITESHIP_API_KEY`, `BITESHIP_ORIGIN_AREA_ID`.
2. **Service live**
   - Buat `app/Services/Shipping/BiteshipShippingService.php` mengimplementasikan
     `ShippingCalculatorInterface` (kontrak yang sama dengan mock, dipakai `Checkout`):
     - **Maps areas:** `GET /v1/maps/areas` (pencarian area tujuan, debounced) untuk
       standardisasi kecamatan tujuan.
     - **Rates:** `POST /v1/rates/couriers` dengan origin area + destination area +
       berat; mapping respons ke format rate yang dikonsumsi Checkout
       (`provider`, `code`, `service`, `name`, `cost`, `etd`).
     - Snapshot ongkir terpilih tetap masuk ke `shipments.shipping_amount`
       (tidak berubah).
3. **Fallback error**
   - Jika Biteship timeout/gagal/tidak ada tarif: **jangan set Rp0**.
   - Tampilkan notice di `resources/views/livewire/storefront/checkout.blade.php`:
     "Ongkir tidak dapat dihitung otomatis, silakan hubungi admin (WhatsApp
     `081546407702`)" + tetap tampilkan opsi Kurir Toko.
4. **Wiring driver**
   - `app/Providers/AppServiceProvider.php`: bind `ShippingCalculatorInterface`
     berdasarkan `config('store.shipping.driver')` — `mock` (default, untuk
     dev/test/UAT) vs `biteship` (staging/production).
5. **Test**
   - `tests/Feature/ShippingBiteshipTest.php` dengan `Http::fake()`:
     - sukses: rates ter-mapping benar (harga, etd, provider);
     - timeout/5xx: fallback notice, bukan Rp0;
     - area tidak ditemukan: fallback.
   - Pastikan `CheckoutAndOrderTest` tetap hijau dengan driver mock.

### DoD
- Tarif live Biteship muncul di checkout saat `driver=biteship`.
- API gagal/timeout → pelanggan melihat notice "hubungi admin", tidak pernah Rp0.
- Test baru hijau + test checkout lama hijau.
- Konfigurasi via env, tanpa hardcode.

---

## 4. Item B — Provider WhatsApp Produksi (Estimasi: M–L, ±1–2 hari + approval)

### Blocking / keputusan klien
- Pilihan provider (misal WhatsApp Business Platform / provider resmi; dokumen klien
  mencatat "template WhatsApp belum tersedia").
- Nomor tujuan produksi (default `081546407702`) dan template pesan `Cek Order masuk`.
- Biaya & persetujuan akses API (Meta/WhatsApp approval bila relevan).

### Langkah
1. **Riset & pilih provider** — gunakan riset layanan (Gravity Index) untuk membandingkan
   provider WhatsApp Business API sesuai kebutuhan (nomor tunggal, template pesan,
   budget). Keputusan tetap di tangan klien.
2. **Implementasi notifier**
   - Buat `app/Domains/Notifications/WhatsAppProviderNotifier.php`
     mengimplementasikan `WhatsAppNotifierInterface` (kontrak `send()` yang sama dengan
     `LogWhatsAppNotifier`/`FakeWhatsAppNotifier`).
   - Kirim pesan `Cek Order masuk` ke `config('store.whatsapp.admin_order_phone')`.
3. **Wiring**
   - `app/Providers/AppServiceProvider.php`: dukung driver `provider` selain
     `log|fake`; env keys di `.env.example` (`WA_API_KEY`, `WA_SENDER_ID`, dll. sesuai
     provider).
   - Gagal kirim → jangan crash alur order; fallback ke log + retry via
     `notifications:dispatch-pending` (antrean sudah ada).
4. **Test**
   - Unit test provider dengan `Http::fake()`; pastikan test yang ada
     (`AdminNotificationTest`) tetap hijau dengan driver `fake`.

### DoD
- Order baru mengirim notifikasi `Cek Order masuk` ke nomor produksi di
  staging/production.
- Kegagalan provider tidak memblokir alur order (fallback log + retry antrean).

---

## 5. Dependensi & Urutan

```text
[C verifikasi ledger retur]  ✅ SELESAI
        │
        ├──> [A Biteship live]  ── butuh: keputusan klien + BITESHIP_API_KEY
        │
        └──> [B WhatsApp prod]  ── butuh: keputusan klien + provider + approval
```

- A dan B **tidak saling bergantung** — bisa dikerjakan paralel setelah keputusan klien.
- Keduanya **diblokir keputusan klien**; selama belum turun, sprint dapat ditutup dengan
  status "menunggu keputusan klien" atau A/B dikerjakan parsial (mis. service Biteship
  dibangun dengan mode mock + siap-switch).

## 6. Keputusan yang Dibutuhkan Klien

1. **Biteship:** setuju memakai Maps & Rates live? Sediakan API key + daftar kurir?
   (PIC POS: Kak Rio.)
2. **WhatsApp:** pilih provider, nomor tujuan produksi, dan template pesan.
3. **Prioritas:** mana yang didahulukan — Biteship atau WhatsApp?

## 7. Definition of Done Sprint (final)

1. Item C: test retur + ledger `ORDER_RETURNED` hijau. ✅ target awal sprint ini.
2. Item A & B: selesai **hanya jika** keputusan klien sudah turun; jika tidak, sprint
   ditutup dengan catatan blocker dan backlog diteruskan ke sprint berikutnya.
