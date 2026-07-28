# Dokumentasi Proyek

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
   konektor lintas-flow `UF-01` sampai `UF-18`.

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

Pada Selasa, 28 Juli 2026, klien mengarahkan agar pengembangan dilanjutkan
sesuai proposal awal. PPh 22, batas maksimal penjualan, dan surcharge terkait
tetap berada dalam MVP; detail formula masih menunggu klarifikasi pada
[BRD Bagian 15.1](requirements/BRD.md#151-klarifikasi-klien-28-juli-2026).

Production menggunakan shared hosting milik klien. Data production produk dan
stok wajib berasal dari POS; data contoh hanya digunakan untuk development,
staging, demo, dan UAT.

## Source Documents

- [Proposal Klien Sylvi - Update 1](references/proposal-klien-sylvi-update-1.pdf)

Dokumen sumber tidak diedit. Perubahan requirement dicatat pada BRD, FRD, dan
SRS agar perbedaan terhadap proposal tetap dapat ditelusuri.
