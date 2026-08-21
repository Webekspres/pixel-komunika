# Sprint 3 — Plan Pengerjaan Sisa Pekerjaan

> Berdasarkan `docs/sprints/SPRINT_3.md` (revisi 18 Agu 2026). Mayoritas task sprint
> sudah terimplementasi; dokumen ini merinci langkah untuk **sisa pekerjaan** yang
> benar-benar tersisa beserta dependensi dan keputusan yang dibutuhkan.

## 1. Konteks & Tujuan

Sprint 3 hampir selesai. Yang tersisa hanya 3 item:

| ID | Item | Blocker | Status |
|---|---|---|---|
| A | Integrasi Biteship live (Maps & Rates) + fallback | `BITESHIP_ORIGIN_AREA_ID` (client provides) | 🔄 **Core implemented** |
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
- **Sudah tersedia:** `BITESHIP_API_KEY`, `BITESHIP_BASE_URL` di `.env`
- **Dibutuhkan dari klien:** `BITESHIP_ORIGIN_AREA_ID` (area ID asal toko di Biteship) + daftar kurir yang diaktifkan.

### Progress implementasi ✅
1. **Konfigurasi** — `config/biteship.php` dibuat, env keys ditambah ke `.env.example`
2. **Service live** — `app/Services/Shipping/BiteshipShippingService.php` implementasi `ShippingCalculatorInterface`:
   - Maps areas: `GET /v1/maps/areas` dengan cache 24 jam
   - Rates: `POST /v1/rates/couriers` dengan origin + destination area + berat
   - Mapping respons ke format Checkout (`provider`, `code`, `service`, `name`, `cost`, `etd`)
3. **Fallback error** — Checkout view menampilkan notice "hubungi admin" saat Biteship gagal/tidak ada tarif, tetap tampilkan Kurir Toko
4. **Wiring driver** — `AppServiceProvider.php` bind berdasarkan `config('store.shipping.driver')`: `mock` (default) vs `biteship`
5. **Test** — `tests/Feature/ShippingBiteshipTest.php` (6 test, 26 assertions):
   - Sukses mapping rates
   - Timeout/5xx → empty array (graceful)
   - Area tidak ditemukan → empty array
   - API key/origin tidak konfigurasi → empty array
   - Filter zero-cost rates

### Verifikasi
```bash
php artisan test --filter="ShippingBiteshipTest"       # 6 tests, 26 assertions ✅
php artisan test --filter="Checkout"                    # 6 tests, 28 assertions ✅
SHIPPING_DRIVER=biteship php artisan test --filter="Checkout"  # 6 tests ✅
php artisan test                                        # 117 tests, 516 assertions ✅
```

### DoD
- [x] Tarif live Biteship muncul di checkout saat `driver=biteship` (service ready)
- [x] API gagal/timeout → pelanggan melihat notice "hubungi admin", tidak pernah Rp0
- [x] Test baru hijau + test checkout lama hijau (both drivers)
- [x] Konfigurasi via env, tanpa hardcode
- [ ] **Waiting:** `BITESHIP_ORIGIN_AREA_ID` dari klien untuk aktivasi penuh di staging/production

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
        ├──> [A Biteship live]  ✅ Core implemented — butuh: BITESHIP_ORIGIN_AREA_ID dari klien
        │
        └──> [B WhatsApp prod]  ── butuh: keputusan klien + provider + approval
```

- A dan B **tidak saling bergantung** — bisa dikerjakan paralel setelah keputusan klien.
- A sudah siap pakai (service + test + fallback), tinggal menunggu `BITESHIP_ORIGIN_AREA_ID` dari klien.
- B **diblokir keputusan klien** penuh.

## 6. Keputusan yang Dibutuhkan Klien

1. **Biteship:** `BITESHIP_ORIGIN_AREA_ID` (area ID asal toko di Biteship) — service sudah ready.
2. **WhatsApp:** pilih provider, nomor tujuan produksi, dan template pesan.
3. **Prioritas:** mana yang didahulukan — Biteship atau WhatsApp?

## 7. Definition of Done Sprint (final)

1. Item C: test retur + ledger `ORDER_RETURNED` hijau. ✅
2. Item A: Core service + test + fallback ✅ — tinggal set `BITESHIP_ORIGIN_AREA_ID` di env staging/production.
3. Item B: selesai **hanya jika** keputusan klien sudah turun; jika tidak, sprint ditutup dengan catatan blocker dan backlog diteruskan ke sprint berikutnya.
