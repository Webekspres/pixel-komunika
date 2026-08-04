# POS Follow-up

Daftar follow-up integrasi POS setelah flow website berbasis data contoh stabil.

## Tujuan

Menyiapkan kontrak endpoint dan data POS untuk fase setelah Sprint 1 tanpa memblokir auth dan katalog dasar.

## Prioritas follow-up

### 1. Master data read endpoints

Konfirmasi endpoint dan payload untuk:

- kategori
- semua produk
- detail produk
- price list
- seluruh stok
- stok per produk

Catatan requirement: lihat `FR-POS-001` s.d. `FR-POS-004` di `docs/requirements/FRD.md`.

### 2. Identifier dan aturan upsert

Perlu dikunci:

- external ID kategori
- external ID produk
- external ID harga
- SKU stabil
- field wajib vs opsional
- perilaku record yang hilang dari POS

Catatan requirement: `FR-POS-002`, `FR-POS-006`, `FR-POS-014`, `FR-POS-015`.

### 3. Sync behavior

Perlu dikonfirmasi:

- jadwal full sync harian
- endpoint stok per produk untuk pencocokan terarah
- cut-off snapshot stok
- retry policy
- timeout / ambiguous response handling

Catatan requirement: `FR-POS-003`, `FR-POS-004`, `FR-POS-010`, `FR-POS-011`.

### 4. Sales/return reporting contract

Perlu dikonfirmasi:

- nama operasi report sale
- nama operasi report return
- payload detail item
- acknowledgement sukses / gagal
- external reference unik
- aturan idempotency
- urutan sale report vs return report

Catatan requirement: `FR-POS-012`, `FR-POS-017` s.d. `FR-POS-020`, serta open items `OPN-005` dan `OPN-019`.

## Pertanyaan yang perlu dibawa ke PIC POS

1. Endpoint final apa saja yang tersedia untuk read master, read inventory, report sale, dan report return?
2. Field mana yang dijamin stabil untuk key upsert: product ID, SKU, price ID, category ID?
3. Apakah price list selalu mengirim tiga jenis harga lengkap: `ECERAN`, `PARTAI`, `GROSIR`?
4. Apakah stok read bersifat current snapshot atau ada timestamp source yang bisa dipakai?
5. Bagaimana format acknowledgement dan external reference untuk operasi report sale/return?
6. Jika request timeout, bagaimana cara lookup status operasi agar tidak membuat duplikasi?
7. Apakah ada sandbox, IP whitelist, auth token, atau batas rate yang perlu disiapkan?

## Deliverable follow-up

- dokumen kontrak endpoint final
- contoh payload sukses/gagal
- aturan auth dan environment access
- daftar field mapping POS -> website
- test case contract integration
