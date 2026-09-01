# Dokumentasi Proyek

## Handover

- [Dokumen Handover Proyek](HANDOVER.md) — snapshot status implementasi,
  matriks MVP, arsitektur, panduan operasional, blocker go-live, dan checklist
  serah terima (Bahasa Indonesia; per 1 September 2026).
- [PDF Handover](HANDOVER.pdf) — versi cetak/share untuk klien (diagram Mermaid
  sudah di-render sebagai gambar).

## Client Deliverable

- [Ringkasan Ruang Lingkup dan Persetujuan Klien](client/DOKUMEN_REVIEW_DAN_PERSETUJUAN_KLIEN.md)
  - dokumen client-facing yang diringkas dari baseline internal untuk review,
    klarifikasi, dan persetujuan klien;
  - konversi ke PDF harus menggunakan renderer yang mendukung Mermaid atau
    melakukan render diagram terlebih dahulu.

## Requirement Documents

Dokumen direview berurutan dari kebutuhan bisnis ke spesifikasi teknis:

1. [BRD](requirements/BRD.md) - tujuan bisnis, scope, business rules, risiko,
   dan keputusan terbuka.
2. [FRD](requirements/FRD.md) - fungsi per modul, aktor, acceptance criteria,
   dan traceability.
3. [SRS](requirements/SRS.md) - arsitektur, stack, integrasi, data, keamanan,
   performa, deployment, dan operasi.
4. [MVP Baseline](requirements/MVP.md) - daftar P0/Must Have, fallback POS,
   non-MVP, dan acceptance gate 45 hari.

BRD, FRD, dan SRS berstatus **Approved Working Baseline** sejak 27 Juli 2026.
Item `TBD`, `ON_HOLD`, dan keputusan terbuka tetap menjadi pengecualian
eksplisit. Dokumen MVP masih berstatus candidate sampai daftar P0 disetujui
klien.

## Design Documents

1. [ERD](design/ERD.md) - diagram relasi data working baseline dalam Mermaid.
2. [Data Dictionary](design/DATA_DICTIONARY.md) - definisi field, tipe data,
   constraint, index, snapshot, dan penanda provisional.
3. [User Flows](design/USER_FLOWS.md) - flow MVP granular dalam Mermaid dengan
   konektor lintas-flow `UF-01` sampai `UF-19`.
4. [Sitemap](design/SITEMAP.md) - peta halaman, status route, penempatan
   kapabilitas MVP, dan kontrol penambahan halaman.
5. [Frontend UI Architecture](design/FRONTEND_UI_ARCHITECTURE.md) - arsitektur
   layer UI, adopsi Flux, strategi komponen, folder structure, design system,
   animasi, performa, dan aturan development frontend.

## Delivery Method

Proyek menggunakan hybrid Agile-Waterfall:

- Agile untuk backlog, sprint, continuous testing, demo, feedback, dan
  penerimaan increment.
- Waterfall/stage-gate untuk MVP baseline, acceptance criteria, UAT sign-off,
  release readiness, dan go-live.

Governance utama berada pada:

- [BRD Bagian 2.2-2.3](requirements/BRD.md#22-model-delivery-hybrid-agile-waterfall)
  untuk delivery model dan change control;
- [FRD Bagian 1.1-1.4](requirements/FRD.md#11-tata-kelola-backlog-agile)
  untuk backlog, Definition of Ready, Definition of Done, dan sprint;
- [SRS Bagian 2.1-2.2](requirements/SRS.md#21-engineering-governance-hybrid)
  untuk engineering guardrail dan change notice.
- [MVP Baseline](requirements/MVP.md) untuk backlog P0 dan acceptance outcome.

## Current Change Notice

Jawaban tertulis klien sampai 21 Agustus 2026 menetapkan formula PPh 22,
penerapan harga partai, invoice website/PDF/WhatsApp/email, omzet saat dikirim,
wilayah/SLA kurir toko, fallback Biteship, retensi bukti pembayaran, notifikasi
admin, reseller versi pertama, pengiriman gabungan untuk alamat sama, serta
konfirmasi penerimaan dan auto-complete lima hari kerja. Rincian dan sisa
keputusan berada pada [BRD Bagian 15](requirements/BRD.md#15-keputusan-terbuka).
Khusus kurir toko, subtotal barang ditambah PPh 22 sedikitnya Rp1.000.000
mendapat gratis ongkir; nilai di bawahnya memakai tarif area dan dikirim H+1
hari kerja.

Production menggunakan shared hosting milik klien. Klien akan memberikan akses
yang diperlukan kepada developer Webekspres untuk setup dan deployment;
kredensial aktual serta kemampuan teknis hosting diverifikasi saat technical
handoff. Data production produk dan stok wajib berasal dari POS; data contoh
hanya digunakan untuk development, staging, demo, dan UAT.

## Source Documents

- [Proposal Klien Sylvi - Update 1](references/proposal-klien-sylvi-update-1.pdf)

Dokumen sumber tidak diedit. Perubahan requirement dicatat pada BRD, FRD, dan
SRS agar perbedaan terhadap proposal tetap dapat ditelusuri.
