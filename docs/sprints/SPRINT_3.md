# Sprint 3: POS Integration, Stock, Payment, Shipping, Reports & System Hardening

> **Catatan revisi (18 Agu 2026):** Dokumen ini diperbarui berdasarkan audit kode dan
> dokumen aktual. Mayoritas task ternyata sudah terimplementasi — dokumen ini kini
> berfungsi sebagai **checklist verifikasi** (task + bukti implementasi) sekaligus
> daftar **sisa pekerjaan** yang benar-benar belum ada.

## 1. Konteks & Metadata Sprint
- **Project:** Website E-Commerce Custom Pixel Komunika
- **Fase:** Sprint 3 (System Integration, Fulfillment, Payment & Hardening)
- **Assignee:** Kris Adiwinata (`ka.webekspres@gmail.com`)
- **Dokumen Referensi:**
  - `docs/requirements/`: `BRD.md`, `FRD.md`, `SRS.md`, `MVP.md`
  - `docs/design/`: `ERD.md`, `DATA_DICTIONARY.md`, `USER_FLOWS.md`
- **Tech Stack:** Laravel 13, Livewire 4, Alpine.js, Tailwind CSS 4, Flux UI, MySQL/MariaDB (InnoDB)

---

## 2. Sprint Goal

Memastikan integrasi stok & pelaporan POS, verifikasi pembayaran manual + retensi bukti
transfer 5 tahun, kalkulasi ongkir (Kurir Toko & Biteship), alur fulfillment hingga
selesai, notifikasi admin, laporan omzet per wilayah, serta hardening audit log dan
error handling — **seluruhnya berjalan sesuai dokumen requirements dan sudah
terverifikasi oleh test.**

---

## 3. Daftar Backlog & Task Breakdown

### A. Modul POS & Manajemen Stok Efektif (UF-14, UF-15, UF-09, UF-13)

- [x] **Task 1: Background Worker Pelaporan Transaksi ke POS (`WEB_SALE_REPORT`)**
  - **Implementasi aktual:** `app/Domains/PosIntegration/PosOutboxService.php` menulis
    operasi `WEB_SALE_REPORT` ke tabel `pos_integration_operations`
    (migration `2026_08_13_000118`). `app/Console/Commands/PosDispatchSaleReports.php`
    mengirim antrean via `app/Domains/PosIntegration/SamplePosSyncService.php`.
  - **Status operasi:** `PENDING`, `SUCCEEDED`, `RECONCILIATION_REQUIRED`, `FAILED`
    (konstanta `app/Models/PosIntegrationOperation.php`).
  - **Idempotency:** `orders.idempotency_key` + `pos_integration_operations.external_reference`
    mencegah pencatatan ganda saat retry.
  - **Scheduler:** `pos:dispatch-sale-reports` tiap 5 menit (`routes/console.php`).

- [x] **Task 2: Background Worker Pelaporan Retur ke POS (`WEB_RETURN_REPORT`)**
  - **Implementasi aktual:** `PosOutboxService` menulis `WEB_RETURN_REPORT`;
    `app/Console/Commands/PosDispatchReturnReports.php` memprosesnya.
  - **Guard retur:** retur hanya dikirim jika `WEB_SALE_REPORT` asal berstatus
    `SUCCEEDED` atau `RECONCILIATION_REQUIRED` (`SamplePosSyncService.php`).
  - **Scheduler:** `pos:dispatch-return-reports` tiap 5 menit.

- [x] **Task 3: Ledger Stok & Sinkronisasi Stok Efektif**
  - **Implementasi aktual:** tabel `inventory_ledger`
    (migration `2026_08_04_170008` + `2026_08_13_000119`).
  - **Catatan terminologi:** kolomnya bernama `source` (bukan `source_type`), nilai yang
    dipakai: `FULL_SYNC` (import katalog), `ORDER_CREATED` (pengurangan stok saat
    checkout), `ORDER_CANCELLED` (restore stok pembatalan) — lihat
    `app/Services/OrderService.php`.
  - **Atomik:** `inventory_snapshots` di-update dalam `DB::transaction` dengan
    `lockForUpdate()` pada `OrderService` (checkout & restore stok).

### B. Modul Pembayaran Manual & Retensi Bukti (UF-10, UF-11)

- [x] **Task 4: Pengajuan Bukti Pembayaran oleh Pelanggan**
  - **Implementasi aktual:** `app/Livewire/Customer/OrderDetail.php` →
    `uploadPaymentProof()` → `app/Services/PaymentService.php`.
  - **Validasi file:** `required|image|mimes:jpeg,png,jpg,webp,pdf|max:5120` (5 MB).
  - **Retensi:** `payment_proofs.retain_until = now + 5 tahun`
    (`config('store.payment_proof_retain_years')`), file di disk `local` (`payment-proofs`).
  - **Status aktual:** order → `payment_pending`; `payments.status` → `SUBMITTED`
    (bukan `PAYMENT_SUBMITTED`).

- [x] **Task 5: Verifikasi Pembayaran Admin (CMS)**
  - **Implementasi aktual:** `app/Livewire/Admin/AdminOrders.php` —
    `approvePayment()` dan `rejectPayment()` (alasan wajib, validasi `min:5`, via modal).
  - **Status aktual:** order `paid` / `unpaid`; `payments` `VERIFIED` / `REJECTED`;
    bukti `approved` / `rejected`; tercatat `verified_by`, `verified_at`, `admin_note`.
  - **Audit:** `PAYMENT_VERIFIED`, `PAYMENT_REJECTED`.

### C. Modul Pengiriman & Integrasi Biteship (UF-07, UF-12)

- [x] **Task 6: Manajemen Tarif Kurir Toko (Admin Settings)**
  - **Implementasi aktual:** `app/Models/StoreCourierRate.php` + CRUD di
    `app/Http/Controllers/Admin/SettingsController.php`; seed wilayah Bandung
    (Coblong, Cicendo, Lembang) dengan `eta_text` H+1.
  - **SLA hari kerja:** `app/Domains/BackgroundJobs/WorkdayCalculator.php`.

- [~] **Task 7: Integrasi Biteship Maps & Rates API (Backend Only)**
  - **Implementasi aktual (mock):** `app/Services/Shipping/ShippingCalculatorInterface.php`
    + `MockBiteshipShippingService.php` (Kurir Toko untuk Bandung, Grab/Gojek Same-day,
    JNE Reguler). Origin: `config('store.address')` = *Jl. Sawahkurung IV No. 18B, Bandung*.
    Ongkir terpilih di-snapshot ke `shipments.shipping_amount`.
  - **Sisa pekerjaan:** koneksi API Biteship **live** (Maps `areas` + Rates `couriers`)
    belum ada; kontrak Biteship masih *partial* di `BRD.md` (OPN-016/OPN-021) dan
    `SRS.md`. Fallback "notifikasi pelanggan hubungi admin" saat Biteship tidak merespons
    juga belum ada (mock selalu mengembalikan tarif). **Butuh keputusan klien.**

- [x] **Task 8: Penggabungan Pengiriman (Order Grouping)**
  - **Implementasi aktual:** otomatis saat order dibuat —
    `OrderService::shipmentGroupCode()` (user + alamat tujuan identik) →
    `shipments.shipment_group_code`. Nomor order & invoice tetap terpisah (sesuai dokumen
    klien).

### D. Modul Lifecycle Fulfillment & Penyelesaian Pesanan (UF-12)

- [x] **Task 9: Transisi Status Fulfillment**
  - **Implementasi aktual:** `app/Domains/Order/FulfillmentService.php` —
    `PROCESSING` → `PACKED` → `SHIPPED` → `COMPLETED`.
  - **Resi:** `shipments.tracking_number` wajib untuk ekspedisi ber-resi; Kurir Toko
    opsional. Audit `ORDER_STATUS_CHANGED`.

- [x] **Task 10: Mekanisme Penyelesaian Pesanan & Penanda Kendala**
  - **Konfirmasi penerimaan:** token sekali pakai (`orders.receipt_token_hash`,
    `receipt_token_expires_at` = +14 hari) diverifikasi `hash_equals` di
    `FulfillmentService::confirmReceipt()` → `receipt_confirmed_at`; audit
    `ORDER_RECEIPT_CONFIRMED`.
  - **Auto-complete:** `app/Console/Commands/AutoCompleteShippedOrders.php` (scheduler
    `orders:auto-complete-shipped` harian 00:15) — selesaikan setelah 5 hari kerja
    (`config('store.fulfillment.auto_complete_workdays')`) hanya untuk
    `shipments.issue_status = NONE`.
  - **Penanda kendala:** `shipments.issue_status = TERKENDALA` + `isHeld()`; audit
    `SHIPMENT_TERKENDALA` / `SHIPMENT_ISSUE_RESOLVED` (tahan auto-complete).

### E. Modul Notifikasi & Laporan (UF-18, UF-19)

- [x] **Task 11: Notifikasi Order Baru Admin**
  - **Implementasi aktual:** `app/Domains/Notifications/NotificationService.php` +
    model `app_notifications`; badge merah `<livewire:admin.notifications>`; auto-mark
    read saat admin membuka daftar pesanan (`AdminOrders::mount`).
  - **WhatsApp:** `config('store.whatsapp')` — pesan `Cek Order masuk` ke
    `081546407702`, driver saat ini `log|fake` (`LogWhatsAppNotifier` /
    `FakeWhatsAppNotifier`); `notifications:dispatch-pending` tiap 5 menit.
  - **Sisa pekerjaan:** provider WhatsApp produksi (masih keputusan klien — lihat dokumen
    klien: "template WhatsApp belum tersedia").

- [x] **Task 12: Laporan Transaksi, Omzet, dan PPh 22**
  - **Implementasi aktual:** halaman laporan admin (`AdminReportTest`) +
    `app/Services/Admin/AdminDashboardService.php`; filter rentang tanggal (Asia/Jakarta)
    & wilayah (kecamatan).
  - **Aturan omzet:** pesanan minimal `SHIPPED`; `COMPLETED` tidak dihitung ganda;
    `CANCELLED` tidak dihitung; agregasi PPh 22 ditampilkan terpisah.

### F. System Hardening & Audit Trail

- [x] **Task 13: Audit Trail & Error Handling**
  - **Implementasi aktual:** `app/Domains/Audit/AuditLogger.php` → tabel `audit_logs`.
    Aksi yang sudah tercatat: `CUSTOMER_*`, `PAYMENT_VERIFIED`, `PAYMENT_REJECTED`,
    `ORDER_CANCELLED`, `ORDER_STATUS_CHANGED`, `ORDER_RECEIPT_CONFIRMED`,
    `ORDER_RETURN_APPROVED/REJECTED`, `SHIPMENT_TERKENDALA/RESOLVED`.
  - **Atomik & locking:** `DB::transaction` + `lockForUpdate()` pada mutasi stok dan
    pembuatan order/invoice (`OrderService`, `PaymentService`).

---

## 4. Ringkasan Status Sprint

| Task | Status | Bukti utama | Sisa pekerjaan |
|---|---|---|---|
| 1. WEB_SALE_REPORT | ✅ Selesai | `PosOutboxService`, `PosDispatchSaleReports` | — |
| 2. WEB_RETURN_REPORT | ✅ Selesai | `PosOutboxService`, `PosDispatchReturnReports`, guard sale ack | — |
| 3. Ledger stok | ✅ Selesai | `inventory_ledger`, `lockForUpdate` | — (retur: `ORDER_RETURNED`, sudah diverifikasi) |
| 4. Upload bukti bayar | ✅ Selesai | `OrderDetail::uploadPaymentProof` | — |
| 5. Verifikasi pembayaran | ✅ Selesai | `AdminOrders`, `PaymentService` | — |
| 6. Tarif kurir toko | ✅ Selesai | `StoreCourierRate` + settings | — |
| 7. Biteship live | ⏳ Sebagian | `MockBiteshipShippingService` | API live + fallback; butuh keputusan klien |
| 8. Penggabungan pengiriman | ✅ Selesai | `OrderService::shipmentGroupCode` | — |
| 9. Fulfillment + resi | ✅ Selesai | `FulfillmentService` | — |
| 10. Konfirmasi & TERKENDALA | ✅ Selesai | `confirmReceipt`, `AutoCompleteShippedOrders` | — |
| 11. Notifikasi + WhatsApp | ✅ Selesai (log/fake) | `NotificationService`, dispatch command | Provider WhatsApp produksi |
| 12. Laporan omzet/PPh 22 | ✅ Selesai | Reports admin + `AdminReportTest` | — |
| 13. Audit & hardening | ✅ Selesai | `AuditLogger`, transaksi + locking | — |

---

## 5. Definition of Done (DoD) Sprint 3

1. Setiap task memiliki unit/feature test terkait alur stok, pembayaran, ongkir, dan
   fulfillment — **terpenuhi** untuk seluruh task yang sudah ada
   (`CatalogImportTest`, `AdminPaymentReviewTest`, `AdminOrdersTransitionTest`,
   `AdminReportTest`, `AdminNotificationTest`, `PosOrderApiTest`,
   `PaymentAndReturnTest`, `AutoCancelUnpaidOrdersTest`). Biteship live menunggu
   kontrak.
2. Tidak ada mutasi stok tanpa tercatat di `inventory_ledger` — **terpenuhi**
   (`FULL_SYNC`, `ORDER_CREATED`, `ORDER_CANCELLED`).
3. Operasi pelaporan POS punya mekanisme rekonsiliasi dan tidak menduplikasi data saat
   timeout/retry — **terpenuhi** (`external_reference` + status
   `RECONCILIATION_REQUIRED`).
4. Transaksi fulfillment end-to-end (`PROCESSING` → `COMPLETED`) dapat disimulasikan
   dan lulus UAT di staging — **terpenuhi** (demo order di seeder sampai `SHIPPED`,
   flow konfirmasi token & auto-complete siap diuji).

**Sisa pekerjaan sprint yang perlu direncanakan:** (1) Biteship live + fallback,
(2) provider WhatsApp produksi — keduanya menunggu keputusan klien. Verifikasi
ledger retur sudah ditutup: retur memakai `source = ORDER_RETURNED`
(`OrderService::restoreStock`). Rincian langkah: `SPRINT_3_PLAN.md`.
