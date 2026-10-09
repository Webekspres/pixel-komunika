# POS Follow-up

Daftar follow-up integrasi POS setelah flow website berbasis data contoh stabil.

## Status Fase 1 (Sandbox Master Data Sync)

- Empat endpoint master data sudah tersedia dan terhubung via adapter sandbox (`PosApiClient` & `SandboxPosMasterSyncService`):
  1. `GET /master/category`
  2. `GET /master/product`
  3. `GET /master/pricelist`
  4. `POST /master/product_detail` (targeted on-demand lookup).
- Adapter sandbox telah menerapkan validasi pra-tulis ketat (fail-fast) agar snapshot katalog terlindungi dari anomali data sandbox.
- **Integrasi POS production BELUM selesai.** Status per 9 Okt 2026 (POS API Documentation v1 revisi 9 Okt + jawaban Kak Rio, CR-024):
  1. ~~Endpoint stok~~: tersedia `GET /inventory/stock` dan `POST /inventory/stock_by_id` (`item_id`, `item_name`, `onhand`). Masih kosong karena stok web belum dialokasikan; nantinya hanya berisi item teralokasi untuk web. Adapter stok belum dibuat (masih sample). Respons kosong tidak boleh mengosongkan katalog.
  2. ~~Kontrak laporan penjualan~~: `POST /order/create_order` dengan `sales_id` = nomor invoice web, `customer` `Retail`, `pembayaran` `Transfer`, `biaya_lain` = ongkir; baris `dpp = line_amount × qty / 1,11`, `ppn = dpp × 11%`. Error bisnis datang dengan HTTP 200 (`Order already exist` = sudah tercatat). Lookup saat timeout lewat `view_orders_detail`. Adapter belum dibuat (masih simulator). **Terbuka:** nilai `pph` per baris dan konfirmasi akhir rumus DPP.
  3. ~~Retur~~: tidak dipakai website. Retur diproses admin di POS (`cancel_order`); stok retur kembali lewat sinkronisasi stok.
  4. ~~Kualitas identifier POS~~: produk tidak valid dilewati dan dicatat tanpa menggagalkan sync (32bda03).
  5. ~~Pemetaan tier harga `Grosir 2`~~: selesai 8 Okt (CR-022), dipetakan ke `GROSIR_2`. Tipe lain di luar dokumentasi (mis. `tes`) tetap ditolak.
  6. ~~Perlakuan pajak `pph`~~: `pph = 1` disimpan sebagai `products.pph22_applicable`; hanya produk ini yang masuk dasar PPh 22 (CR-024). Penanda `ppn` belum dipakai (website tidak menghitung PPN).

## Tujuan

Menyiapkan kontrak endpoint dan data POS untuk fase setelah Sprint 1 tanpa memblokir auth dan katalog dasar.

## Keputusan yang sudah diketahui

- POS menjadi sumber SKU, data dasar produk, harga dasar, dan stok.
- Website boleh menyimpan nama tampilan produk yang berbeda dari nama dasar POS.
- Website mengelola media produk berupa gambar dan video.
- Berat wajib tersedia per produk untuk kalkulasi pengiriman. Dimensi panjang,
  lebar, dan tinggi diperlukan untuk produk berkapasitas besar.
- Sinkronisasi POS tidak boleh menimpa nama tampilan, media, berat, atau dimensi
  yang menjadi enrichment milik website.

## Prioritas follow-up

### 1. Master data read endpoints

Konfirmasi endpoint dan payload untuk:

- kategori
- semua produk
- detail produk
- price list
- seluruh stok
- stok per produk

Kontrak juga harus membedakan field milik POS dengan field enrichment milik
website agar proses upsert tidak menghapus perubahan admin website.

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
4. Field produk apa saja yang authoritative dari POS, dan apakah POS menyediakan berat atau dimensi?
5. Apakah stok read bersifat current snapshot atau ada timestamp source yang bisa dipakai?
6. Bagaimana format acknowledgement dan external reference untuk operasi report sale/return?
7. Jika request timeout, bagaimana cara lookup status operasi agar tidak membuat duplikasi?
8. Apakah ada sandbox, IP whitelist, auth token, atau batas rate yang perlu disiapkan?

## Deliverable follow-up

- dokumen kontrak endpoint final
- contoh payload sukses/gagal
- aturan auth dan environment access
- daftar field mapping dan ownership POS -> website, termasuk field enrichment
  yang tidak boleh ditimpa saat sinkronisasi
- test case contract integration
