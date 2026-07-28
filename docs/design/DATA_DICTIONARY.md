# Data Dictionary

## Pixel Komunika E-Commerce

| Metadata | Nilai                                                                                                                          |
| -------- | ------------------------------------------------------------------------------------------------------------------------------ |
| Versi    | 0.1 - Working Baseline                                                                                                         |
| Tanggal  | Selasa, 28 Juli 2026                                                                                                           |
| Status   | Internal - siap menjadi dasar migration MVP                                                                                    |
| ERD      | [Entity Relationship Diagram](ERD.md)                                                                                          |
| Sumber   | [BRD](../requirements/BRD.md), [FRD](../requirements/FRD.md), [SRS](../requirements/SRS.md), dan [MVP](../requirements/MVP.md) |

## 1. Konvensi

- Seluruh nama tabel dan kolom memakai `snake_case`.
- Primary key memakai `BIGINT UNSIGNED`.
- Nilai uang memakai `DECIMAL(19,2)`; tidak memakai `FLOAT` atau `DOUBLE`.
- Persentase memakai `DECIMAL(8,4)` dan disimpan sebagai nilai persen, misalnya
  `0.5000` berarti `0,5%`.
- Timestamp disimpan dalam UTC dan ditampilkan dalam `Asia/Jakarta`.
  `orders.order_date_local` menyimpan tanggal bisnis untuk aturan pembatalan
  D+1.
- Seluruh tabel operasional memiliki `created_at` dan `updated_at`, kecuali
  tabel append-only yang hanya memerlukan `created_at`.
- `PK` = primary key, `FK` = foreign key, `UK` = unique key, `IDX` = index,
  `NN` = not null.
- Nilai `Provisional` terhubung ke open question dan dapat berubah melalui
  migration setelah keputusan disetujui.

## 2. Identity dan Customer

### 2.1 `roles`

| Kolom      | Tipe            | Null  | Key/Default | Deskripsi                |
| ---------- | --------------- | :---: | ----------- | ------------------------ |
| id         | BIGINT UNSIGNED | Tidak | PK          | Identifier internal.     |
| code       | VARCHAR(32)     | Tidak | UK          | `ADMIN` atau `CUSTOMER`. |
| name       | VARCHAR(100)    | Tidak | —           | Nama tampilan role.      |
| created_at | DATETIME(6)     | Tidak | —           | Waktu pembuatan.         |
| updated_at | DATETIME(6)     | Tidak | —           | Waktu perubahan.         |

Permission granular diterapkan melalui Laravel Policy/Gate. Tabel permission
baru dibuat hanya jika role operasional bertambah.

### 2.2 `users`

| Kolom             | Tipe            | Null  | Key/Default | Deskripsi                              |
| ----------------- | --------------- | :---: | ----------- | -------------------------------------- |
| id                | BIGINT UNSIGNED | Tidak | PK          | Identifier pengguna.                   |
| role_id           | BIGINT UNSIGNED | Tidak | FK, IDX     | Role pengguna.                         |
| name              | VARCHAR(150)    | Tidak | —           | Nama pengguna/perwakilan toko.         |
| email             | VARCHAR(191)    | Tidak | UK          | Email login.                           |
| phone             | VARCHAR(32)     | Tidak | UK          | Nomor kontak unik.                     |
| password          | VARCHAR(255)    | Tidak | —           | Hash password; tidak pernah plaintext. |
| email_verified_at | DATETIME(6)     |   Ya  | —           | Waktu verifikasi email jika digunakan. |
| remember_token    | VARCHAR(100)    |   Ya  | —           | Token native autentikasi Laravel.      |
| last_login_at     | DATETIME(6)     |   Ya  | —           | Login berhasil terakhir.               |
| created_at        | DATETIME(6)     | Tidak | —           | Waktu registrasi.                      |
| updated_at        | DATETIME(6)     | Tidak | —           | Waktu perubahan.                       |

### 2.3 `customer_profiles`

| Kolom               | Tipe            | Null  | Key/Default                 | Deskripsi                                                       |
| ------------------- | --------------- | :---: | --------------------------- | --------------------------------------------------------------- |
| id                  | BIGINT UNSIGNED | Tidak | PK                          | Identifier profil.                                              |
| user_id             | BIGINT UNSIGNED | Tidak | FK, UK                      | Satu profil per pelanggan.                                      |
| business_name       | VARCHAR(191)    |   Ya  | —                           | Nama toko/usaha pelanggan.                                      |
| verification_status | VARCHAR(32)     | Tidak | IDX, `PENDING_VERIFICATION` | `PENDING_VERIFICATION`, `ACTIVE`, `REJECTED`, atau `SUSPENDED`. |
| rejection_reason    | TEXT            |   Ya  | —                           | Alasan penolakan/penangguhan yang dapat ditampilkan.            |
| reviewed_by         | BIGINT UNSIGNED |   Ya  | FK                          | Admin pemberi keputusan terakhir.                               |
| reviewed_at         | DATETIME(6)     |   Ya  | —                           | Waktu keputusan terakhir.                                       |
| created_at          | DATETIME(6)     | Tidak | —                           | Waktu pembuatan.                                                |
| updated_at          | DATETIME(6)     | Tidak | —                           | Waktu perubahan.                                                |

### 2.4 `addresses`

| Kolom           | Tipe            | Null  | Key/Default | Deskripsi                               |
| --------------- | --------------- | :---: | ----------- | --------------------------------------- |
| id              | BIGINT UNSIGNED | Tidak | PK          | Identifier alamat.                      |
| user_id         | BIGINT UNSIGNED | Tidak | FK, IDX     | Pemilik alamat.                         |
| label           | VARCHAR(100)    |   Ya  | —           | Contoh: Toko Utama.                     |
| recipient_name  | VARCHAR(150)    | Tidak | —           | Penerima.                               |
| recipient_phone | VARCHAR(32)     | Tidak | —           | Nomor penerima.                         |
| address_line    | TEXT            | Tidak | —           | Alamat lengkap.                         |
| province_code   | VARCHAR(32)     |   Ya  | IDX         | Kode provinsi dari provider/alur final. |
| province_name   | VARCHAR(100)    | Tidak | —           | Snapshot nama provinsi.                 |
| city_code       | VARCHAR(32)     |   Ya  | IDX         | Kode kota/kabupaten.                    |
| city_name       | VARCHAR(100)    | Tidak | —           | Snapshot nama kota/kabupaten.           |
| district_code   | VARCHAR(32)     |   Ya  | IDX         | Kode kecamatan.                         |
| district_name   | VARCHAR(100)    | Tidak | IDX         | Nama kecamatan untuk laporan.           |
| postal_code     | VARCHAR(16)     |   Ya  | —           | Kode pos.                               |
| is_default      | BOOLEAN         | Tidak | `false`     | Penanda alamat utama.                   |
| created_at      | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                        |
| updated_at      | DATETIME(6)     | Tidak | —           | Waktu perubahan.                        |

## 3. Product, Pricing, dan Inventory

### 3.1 `categories`

| Kolom             | Tipe            | Null  | Key/Default | Deskripsi                          |
| ----------------- | --------------- | :---: | ----------- | ---------------------------------- |
| id                | BIGINT UNSIGNED | Tidak | PK          | Identifier kategori.               |
| pos_category_id   | VARCHAR(191)    | Tidak | UK          | Identifier stabil dari POS/seeder. |
| name              | VARCHAR(191)    | Tidak | IDX         | Nama klasifikasi.                  |
| is_active         | BOOLEAN         | Tidak | `true`      | Status master.                     |
| source_updated_at | DATETIME(6)     |   Ya  | —           | Timestamp perubahan dari POS.      |
| synced_at         | DATETIME(6)     |   Ya  | —           | Sinkronisasi berhasil terakhir.    |
| created_at        | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                   |
| updated_at        | DATETIME(6)     | Tidak | —           | Waktu perubahan.                   |

### 3.2 `brands`

| Kolom             | Tipe            | Null  | Key/Default | Deskripsi                       |
| ----------------- | --------------- | :---: | ----------- | ------------------------------- |
| id                | BIGINT UNSIGNED | Tidak | PK          | Identifier merek.               |
| pos_brand_id      | VARCHAR(191)    |   Ya  | UK          | Identifier POS jika tersedia.   |
| name              | VARCHAR(191)    | Tidak | IDX         | Nama merek.                     |
| is_active         | BOOLEAN         | Tidak | `true`      | Status master.                  |
| source_updated_at | DATETIME(6)     |   Ya  | —           | Timestamp perubahan dari POS.   |
| synced_at         | DATETIME(6)     |   Ya  | —           | Sinkronisasi berhasil terakhir. |
| created_at        | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                |
| updated_at        | DATETIME(6)     | Tidak | —           | Waktu perubahan.                |

### 3.3 `products`

| Kolom          | Tipe            | Null  | Key/Default | Deskripsi                                                                         |
| -------------- | --------------- | :---: | ----------- | --------------------------------------------------------------------------------- |
| id             | BIGINT UNSIGNED | Tidak | PK          | Identifier produk.                                                                |
| category_id    | BIGINT UNSIGNED | Tidak | FK, IDX     | Klasifikasi produk.                                                               |
| brand_id       | BIGINT UNSIGNED |   Ya  | FK, IDX     | Merek produk.                                                                     |
| pos_product_id | VARCHAR(191)    | Tidak | UK          | Identifier stabil POS/seeder.                                                     |
| sku            | VARCHAR(191)    | Tidak | UK          | SKU produk.                                                                       |
| name           | VARCHAR(255)    | Tidak | IDX         | Nama produk dari POS.                                                             |
| weight_grams   | INT UNSIGNED    |   Ya  | —           | Berat untuk quote ongkir; sumber masih [OPN-021](../requirements/BRD.md#opn-021). |
| length_cm      | DECIMAL(10,2)   |   Ya  | —           | Panjang kemasan; provisional.                                                     |
| width_cm       | DECIMAL(10,2)   |   Ya  | —           | Lebar kemasan; provisional.                                                       |
| height_cm      | DECIMAL(10,2)   |   Ya  | —           | Tinggi kemasan; provisional.                                                      |
| is_active      | BOOLEAN         | Tidak | `true`, IDX | Aktif pada master.                                                                |
| synced_at      | DATETIME(6)     |   Ya  | —           | Sinkronisasi berhasil terakhir.                                                   |
| created_at     | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                                                                  |
| updated_at     | DATETIME(6)     | Tidak | —           | Waktu perubahan.                                                                  |

### 3.4 `product_enrichments`

| Kolom             | Tipe            | Null  | Key/Default | Deskripsi                   |
| ----------------- | --------------- | :---: | ----------- | --------------------------- |
| id                | BIGINT UNSIGNED | Tidak | PK          | Identifier enrichment.      |
| product_id        | BIGINT UNSIGNED | Tidak | FK, UK      | Satu enrichment per produk. |
| slug              | VARCHAR(191)    | Tidak | UK          | URL produk.                 |
| short_description | VARCHAR(500)    |   Ya  | —           | Ringkasan pemasaran lokal.  |
| description       | TEXT            |   Ya  | —           | Deskripsi panjang lokal.    |
| seo_title         | VARCHAR(191)    |   Ya  | —           | Judul SEO.                  |
| seo_description   | VARCHAR(320)    |   Ya  | —           | Deskripsi SEO.              |
| label             | VARCHAR(100)    |   Ya  | —           | Label presentasi.           |
| display_order     | INT             | Tidak | `0`         | Urutan tampil.              |
| is_visible        | BOOLEAN         | Tidak | `true`, IDX | Status tampil storefront.   |
| created_at        | DATETIME(6)     | Tidak | —           | Waktu pembuatan.            |
| updated_at        | DATETIME(6)     | Tidak | —           | Waktu perubahan.            |

### 3.5 `product_media`

| Kolom      | Tipe            | Null  | Key/Default | Deskripsi                        |
| ---------- | --------------- | :---: | ----------- | -------------------------------- |
| id         | BIGINT UNSIGNED | Tidak | PK          | Identifier media.                |
| product_id | BIGINT UNSIGNED | Tidak | FK, IDX     | Produk pemilik media.            |
| media_type | VARCHAR(16)     | Tidak | —           | `IMAGE` atau `VIDEO`.            |
| object_key | VARCHAR(500)    | Tidak | UK          | Object key/path privat provider. |
| alt_text   | VARCHAR(255)    |   Ya  | —           | Teks alternatif gambar.          |
| mime_type  | VARCHAR(100)    | Tidak | —           | MIME hasil validasi server.      |
| file_size  | BIGINT UNSIGNED | Tidak | —           | Ukuran byte.                     |
| sort_order | INT             | Tidak | `0`         | Urutan tampil.                   |
| is_primary | BOOLEAN         | Tidak | `false`     | Media utama produk.              |
| created_at | DATETIME(6)     | Tidak | —           | Waktu unggah.                    |
| updated_at | DATETIME(6)     | Tidak | —           | Waktu perubahan.                 |

### 3.6 `product_prices`

| Kolom             | Tipe            | Null  | Key/Default     | Deskripsi                                                           |
| ----------------- | --------------- | :---: | --------------- | ------------------------------------------------------------------- |
| id                | BIGINT UNSIGNED | Tidak | PK              | Identifier harga.                                                   |
| product_id        | BIGINT UNSIGNED | Tidak | FK, UK gabungan | Produk.                                                             |
| price_type        | VARCHAR(16)     | Tidak | UK gabungan     | `ECERAN`, `PARTAI`, atau `GROSIR`.                                  |
| amount            | DECIMAL(19,2)   | Tidak | —               | Harga dari POS.                                                     |
| minimum_quantity  | INT UNSIGNED    |   Ya  | —               | Minimum khusus grosir; syarat partai berada pada aturan cart/order. |
| pos_price_id      | VARCHAR(191)    |   Ya  | UK              | Identifier harga POS jika tersedia.                                 |
| source_updated_at | DATETIME(6)     |   Ya  | —               | Timestamp sumber.                                                   |
| synced_at         | DATETIME(6)     | Tidak | —               | Waktu sinkronisasi.                                                 |
| created_at        | DATETIME(6)     | Tidak | —               | Waktu pembuatan.                                                    |
| updated_at        | DATETIME(6)     | Tidak | —               | Waktu perubahan.                                                    |

Constraint: unik `product_id + price_type`; nilai `amount >= 0`;
`minimum_quantity` wajib untuk `GROSIR`. Cakupan penerapan `PARTAI` dan
prioritas terhadap `GROSIR` masih
[OPN-013](../requirements/BRD.md#opn-013).

### 3.7 `category_tax_rules`

| Kolom             | Tipe            | Null  | Key/Default | Deskripsi                                                       |
| ----------------- | --------------- | :---: | ----------- | --------------------------------------------------------------- |
| id                | BIGINT UNSIGNED | Tidak | PK          | Identifier versi aturan.                                        |
| category_id       | BIGINT UNSIGNED | Tidak | FK, IDX     | Klasifikasi terpilih.                                           |
| threshold_amount  | DECIMAL(19,2)   | Tidak | —           | Ambang nilai belanja.                                           |
| rate_percent      | DECIMAL(8,4)    | Tidak | `0`         | Tarif PPh 22; `0` menonaktifkan pungutan.                       |
| calculation_basis | VARCHAR(32)     |   Ya  | Provisional | Nilai final menunggu [OPN-006](../requirements/BRD.md#opn-006). |
| is_active         | BOOLEAN         | Tidak | `true`, IDX | Aturan aktif.                                                   |
| effective_from    | DATETIME(6)     | Tidak | —           | Awal masa berlaku.                                              |
| effective_until   | DATETIME(6)     |   Ya  | —           | Akhir masa berlaku.                                             |
| updated_by        | BIGINT UNSIGNED | Tidak | FK          | Admin pengubah.                                                 |
| created_at        | DATETIME(6)     | Tidak | —           | Waktu pembuatan versi.                                          |
| updated_at        | DATETIME(6)     | Tidak | —           | Waktu perubahan.                                                |

### 3.8 `inventory_snapshots`

| Kolom               | Tipe            | Null  | Key/Default | Deskripsi                             |
| ------------------- | --------------- | :---: | ----------- | ------------------------------------- |
| id                  | BIGINT UNSIGNED | Tidak | PK          | Identifier snapshot aktif.            |
| product_id          | BIGINT UNSIGNED | Tidak | FK, UK      | Satu snapshot per produk.             |
| quantity            | INT             | Tidak | `0`         | Stok efektif terakhir.                |
| low_stock_threshold | INT UNSIGNED    | Tidak | `0`         | Batas status menipis.                 |
| stock_status        | VARCHAR(16)     | Tidak | IDX         | `TERSEDIA`, `MENIPIS`, atau `HABIS`.  |
| source              | VARCHAR(32)     | Tidak | —           | `POS_API` atau `SEEDER`.              |
| source_updated_at   | DATETIME(6)     |   Ya  | —           | Timestamp stok dari POS.              |
| synced_at           | DATETIME(6)     | Tidak | IDX         | Waktu snapshot terakhir.              |
| version             | INT UNSIGNED    | Tidak | `1`         | Optimistic-lock version bila dipakai. |
| created_at          | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                      |
| updated_at          | DATETIME(6)     | Tidak | —           | Waktu perubahan.                      |

### 3.9 `inventory_ledger`

| Kolom              | Tipe            | Null  | Key/Default | Deskripsi                                                                |
| ------------------ | --------------- | :---: | ----------- | ------------------------------------------------------------------------ |
| id                 | BIGINT UNSIGNED | Tidak | PK          | Identifier perubahan stok.                                               |
| product_id         | BIGINT UNSIGNED | Tidak | FK, IDX     | Produk.                                                                  |
| sync_run_id        | BIGINT UNSIGNED |   Ya  | FK, IDX     | Sync pemicu jika ada.                                                    |
| pos_return_id      | BIGINT UNSIGNED |   Ya  | FK, IDX     | Retur pemicu jika ada.                                                   |
| source_type        | VARCHAR(32)     | Tidak | IDX         | `FULL_SYNC`, `PRODUCT_SYNC`, `SALE`, `CANCEL_RETURN`, atau `CORRECTION`. |
| quantity_before    | INT             | Tidak | —           | Nilai sebelum.                                                           |
| quantity_change    | INT             | Tidak | —           | Delta bertanda.                                                          |
| quantity_after     | INT             | Tidak | —           | Nilai sesudah.                                                           |
| external_reference | VARCHAR(191)    |   Ya  | IDX         | Referensi POS/order/sync.                                                |
| occurred_at        | DATETIME(6)     | Tidak | IDX         | Waktu perubahan bisnis.                                                  |
| created_at         | DATETIME(6)     | Tidak | —           | Waktu pencatatan.                                                        |

## 4. Cart dan Checkout

### 4.1 `carts`

| Kolom      | Tipe            | Null  | Key/Default   | Deskripsi                                  |
| ---------- | --------------- | :---: | ------------- | ------------------------------------------ |
| id         | BIGINT UNSIGNED | Tidak | PK            | Identifier cart.                           |
| user_id    | BIGINT UNSIGNED | Tidak | FK, IDX       | Pelanggan aktif.                           |
| status     | VARCHAR(16)     | Tidak | `ACTIVE`, IDX | `ACTIVE`, `CHECKED_OUT`, atau `ABANDONED`. |
| expires_at | DATETIME(6)     |   Ya  | IDX           | Masa berlaku teknis cart.                  |
| created_at | DATETIME(6)     | Tidak | —             | Waktu pembuatan.                           |
| updated_at | DATETIME(6)     | Tidak | —             | Waktu perubahan.                           |

Constraint: maksimal satu cart `ACTIVE` per pengguna.

### 4.2 `cart_items`

| Kolom      | Tipe            | Null  | Key/Default     | Deskripsi               |
| ---------- | --------------- | :---: | --------------- | ----------------------- |
| id         | BIGINT UNSIGNED | Tidak | PK              | Identifier item.        |
| cart_id    | BIGINT UNSIGNED | Tidak | FK, UK gabungan | Cart.                   |
| product_id | BIGINT UNSIGNED | Tidak | FK, UK gabungan | Produk.                 |
| quantity   | INT UNSIGNED    | Tidak | —               | Kuantitas, minimal `1`. |
| created_at | DATETIME(6)     | Tidak | —               | Waktu ditambahkan.      |
| updated_at | DATETIME(6)     | Tidak | —               | Waktu perubahan.        |

Harga dan total cart dihitung ulang server-side dari master aktif. Transaksi
eligible harga partai jika minimal satu SKU memiliki kuantitas `>= 5`;
kuantitas antar-SKU tidak dijumlahkan.

## 5. Order, Invoice, dan Payment

### 5.1 `store_profiles`

| Kolom          | Tipe            | Null  | Key/Default | Deskripsi                  |
| -------------- | --------------- | :---: | ----------- | -------------------------- |
| id             | BIGINT UNSIGNED | Tidak | PK          | Identifier profil toko.    |
| store_name     | VARCHAR(191)    | Tidak | —           | Nama pada invoice.         |
| address        | TEXT            | Tidak | —           | Alamat pada invoice.       |
| contact_number | VARCHAR(32)     | Tidak | —           | Nomor kontak pada invoice. |
| npwp           | VARCHAR(32)     | Tidak | —           | NPWP pada invoice.         |
| is_active      | BOOLEAN         | Tidak | `true`, IDX | Profil aktif.              |
| created_at     | DATETIME(6)     | Tidak | —           | Waktu pembuatan.           |
| updated_at     | DATETIME(6)     | Tidak | —           | Waktu perubahan.           |

Sumber/mapping final profil toko masih
[OPN-022](../requirements/BRD.md#opn-022).

### 5.2 `bank_accounts`

| Kolom          | Tipe            | Null  | Key/Default | Deskripsi                    |
| -------------- | --------------- | :---: | ----------- | ---------------------------- |
| id             | BIGINT UNSIGNED | Tidak | PK          | Identifier rekening tujuan.  |
| bank_name      | VARCHAR(100)    | Tidak | —           | Nama bank.                   |
| account_number | VARCHAR(64)     | Tidak | UK          | Nomor rekening.              |
| account_holder | VARCHAR(191)    | Tidak | —           | Nama pemilik rekening.       |
| instructions   | TEXT            |   Ya  | —           | Instruksi transfer.          |
| is_active      | BOOLEAN         | Tidak | `true`, IDX | Rekening yang dapat dipilih. |
| created_at     | DATETIME(6)     | Tidak | —           | Waktu pembuatan.             |
| updated_at     | DATETIME(6)     | Tidak | —           | Waktu perubahan.             |

### 5.3 `orders`

| Kolom               | Tipe            | Null  | Key/Default | Deskripsi                                          |
| ------------------- | --------------- | :---: | ----------- | -------------------------------------------------- |
| id                  | BIGINT UNSIGNED | Tidak | PK          | Identifier order.                                  |
| user_id             | BIGINT UNSIGNED | Tidak | FK, IDX     | Pelanggan pemilik order.                           |
| source_address_id   | BIGINT UNSIGNED |   Ya  | FK          | Alamat sumber sebelum snapshot shipment.           |
| order_number        | VARCHAR(64)     | Tidak | UK          | Nomor order web.                                   |
| idempotency_key     | VARCHAR(191)    | Tidak | UK          | Pencegah checkout ganda.                           |
| pos_sales_order_id  | VARCHAR(191)    |   Ya  | UK          | Referensi `CreateSalesOrder`.                      |
| status              | VARCHAR(32)     | Tidak | IDX         | Status lifecycle pada FRD 9.1.                     |
| order_date_local    | DATE            | Tidak | IDX         | Tanggal bisnis Asia/Jakarta untuk auto-cancel D+1. |
| subtotal_amount     | DECIMAL(19,2)   | Tidak | —           | Total seluruh `order_items.line_total`.            |
| pph22_amount        | DECIMAL(19,2)   | Tidak | `0`         | Total PPh 22 snapshot.                             |
| shipping_amount     | DECIMAL(19,2)   | Tidak | `0`         | Ongkir snapshot.                                   |
| grand_total         | DECIMAL(19,2)   | Tidak | —           | Total final server-side.                           |
| expires_at          | DATETIME(6)     | Tidak | IDX         | Batas aktif order belum dibayar.                   |
| cancelled_at        | DATETIME(6)     |   Ya  | —           | Waktu pembatalan.                                  |
| cancellation_reason | TEXT            |   Ya  | —           | Alasan pembatalan.                                 |
| created_at          | DATETIME(6)     | Tidak | IDX         | Waktu order dibuat.                                |
| updated_at          | DATETIME(6)     | Tidak | —           | Waktu perubahan.                                   |

Status internal sebelum konfirmasi POS: `DRAFT` dan
`RECONCILIATION_REQUIRED`. Status customer-facing:
`WAITING_PAYMENT`, `PAYMENT_SUBMITTED`, `PAYMENT_REJECTED`,
`PAYMENT_VERIFIED`, `PROCESSING`, `READY_FOR_DELIVERY`, `SHIPPED`,
`COMPLETED`, dan `CANCELLED`. Bagian fulfillment masih
[OPN-020](../requirements/BRD.md#opn-020).

### 5.4 `order_items`

| Kolom                  | Tipe            | Null  | Key/Default | Deskripsi                                         |
| ---------------------- | --------------- | :---: | ----------- | ------------------------------------------------- |
| id                     | BIGINT UNSIGNED | Tidak | PK          | Identifier baris.                                 |
| order_id               | BIGINT UNSIGNED | Tidak | FK, IDX     | Order.                                            |
| product_id             | BIGINT UNSIGNED |   Ya  | FK, IDX     | Produk sumber; boleh null jika master diarsipkan. |
| category_id_snapshot   | BIGINT UNSIGNED |   Ya  | IDX         | ID klasifikasi saat checkout.                     |
| category_name_snapshot | VARCHAR(191)    | Tidak | —           | Nama klasifikasi historis.                        |
| sku_snapshot           | VARCHAR(191)    | Tidak | IDX         | SKU historis.                                     |
| product_name_snapshot  | VARCHAR(255)    | Tidak | —           | Nama produk historis.                             |
| price_type             | VARCHAR(16)     | Tidak | —           | `PARTAI` atau `GROSIR` pada kanal fase ini.       |
| quantity               | INT UNSIGNED    | Tidak | —           | Jumlah barang.                                    |
| unit_price             | DECIMAL(19,2)   | Tidak | —           | Harga satuan snapshot.                            |
| line_total             | DECIMAL(19,2)   | Tidak | —           | `quantity * unit_price`.                          |
| created_at             | DATETIME(6)     | Tidak | —           | Waktu snapshot.                                   |

Constraint: satu SKU muncul maksimal sekali dalam satu order.

### 5.5 `order_charge_components`

| Kolom                | Tipe            | Null  | Key/Default | Deskripsi                                                    |
| -------------------- | --------------- | :---: | ----------- | ------------------------------------------------------------ |
| id                   | BIGINT UNSIGNED | Tidak | PK          | Identifier komponen.                                         |
| order_id             | BIGINT UNSIGNED | Tidak | FK, IDX     | Order.                                                       |
| category_tax_rule_id | BIGINT UNSIGNED |   Ya  | FK, IDX     | Aturan sumber jika masih tersedia.                           |
| component_code       | VARCHAR(32)     | Tidak | IDX         | Minimal `PPH22`; komponen lain hanya melalui change control. |
| label_snapshot       | VARCHAR(100)    | Tidak | —           | Label invoice/laporan.                                       |
| basis_amount         | DECIMAL(19,2)   | Tidak | —           | Dasar perhitungan snapshot; provisional OPN-006.             |
| rate_percent         | DECIMAL(8,4)    | Tidak | —           | Tarif snapshot.                                              |
| amount               | DECIMAL(19,2)   | Tidak | —           | Nilai rupiah komponen.                                       |
| config_snapshot      | JSON            | Tidak | —           | Klasifikasi, ambang, formula, dan metadata aturan teredaksi. |
| created_at           | DATETIME(6)     | Tidak | —           | Waktu snapshot.                                              |

### 5.6 `invoices`

| Kolom                  | Tipe            | Null  | Key/Default | Deskripsi                                       |
| ---------------------- | --------------- | :---: | ----------- | ----------------------------------------------- |
| id                     | BIGINT UNSIGNED | Tidak | PK          | Identifier invoice.                             |
| order_id               | BIGINT UNSIGNED | Tidak | FK, UK      | Satu invoice per order baseline.                |
| store_profile_id       | BIGINT UNSIGNED |   Ya  | FK          | Profil sumber untuk traceability.               |
| pos_invoice_id         | VARCHAR(191)    |   Ya  | UK          | Identifier invoice POS.                         |
| invoice_number         | VARCHAR(100)    | Tidak | UK          | Nomor invoice POS/lokal sesuai keputusan final. |
| issued_at              | DATETIME(6)     | Tidak | IDX         | Waktu terbit.                                   |
| store_name_snapshot    | VARCHAR(191)    | Tidak | —           | Nama toko wajib.                                |
| store_address_snapshot | TEXT            | Tidak | —           | Alamat toko wajib.                              |
| store_contact_snapshot | VARCHAR(32)     | Tidak | —           | Kontak toko wajib.                              |
| store_npwp_snapshot    | VARCHAR(32)     | Tidak | —           | NPWP toko wajib.                                |
| subtotal_amount        | DECIMAL(19,2)   | Tidak | —           | Total harga item.                               |
| shipping_amount        | DECIMAL(19,2)   | Tidak | `0`         | Ongkir.                                         |
| pph22_amount           | DECIMAL(19,2)   | Tidak | `0`         | Nilai rupiah PPh 22 jika ada.                   |
| grand_total            | DECIMAL(19,2)   | Tidak | —           | Total pembelian keseluruhan.                    |
| created_at             | DATETIME(6)     | Tidak | —           | Waktu penyimpanan.                              |
| updated_at             | DATETIME(6)     | Tidak | —           | Waktu perubahan metadata.                       |

Item invoice dibaca dari `order_items`. Format PDF, channel, dan ownership
invoice POS/lokal masih [OPN-022](../requirements/BRD.md#opn-022).

### 5.7 `payments`

| Kolom            | Tipe            | Null  | Key/Default          | Deskripsi                                                  |
| ---------------- | --------------- | :---: | -------------------- | ---------------------------------------------------------- |
| id               | BIGINT UNSIGNED | Tidak | PK                   | Identifier pembayaran.                                     |
| order_id         | BIGINT UNSIGNED | Tidak | FK, UK               | Satu payment lifecycle per order baseline.                 |
| bank_account_id  | BIGINT UNSIGNED | Tidak | FK                   | Rekening tujuan.                                           |
| status           | VARCHAR(24)     | Tidak | `NOT_SUBMITTED`, IDX | `NOT_SUBMITTED`, `SUBMITTED`, `VERIFIED`, atau `REJECTED`. |
| amount           | DECIMAL(19,2)   |   Ya  | —                    | Nominal yang diajukan pelanggan.                           |
| submitted_at     | DATETIME(6)     |   Ya  | —                    | Pengajuan terakhir.                                        |
| verified_by      | BIGINT UNSIGNED |   Ya  | FK                   | Admin pemeriksa terakhir.                                  |
| verified_at      | DATETIME(6)     |   Ya  | —                    | Waktu verifikasi.                                          |
| rejection_reason | TEXT            |   Ya  | —                    | Alasan penolakan.                                          |
| admin_note       | TEXT            |   Ya  | —                    | Catatan internal.                                          |
| created_at       | DATETIME(6)     | Tidak | —                    | Waktu pembuatan.                                           |
| updated_at       | DATETIME(6)     | Tidak | —                    | Waktu perubahan.                                           |

### 5.8 `payment_proofs`

| Kolom         | Tipe            | Null  | Key/Default | Deskripsi                     |
| ------------- | --------------- | :---: | ----------- | ----------------------------- |
| id            | BIGINT UNSIGNED | Tidak | PK          | Identifier bukti.             |
| payment_id    | BIGINT UNSIGNED | Tidak | FK, IDX     | Payment.                      |
| object_key    | VARCHAR(500)    | Tidak | UK          | Object key privat.            |
| original_name | VARCHAR(255)    | Tidak | —           | Nama file teredaksi.          |
| mime_type     | VARCHAR(100)    | Tidak | —           | MIME hasil validasi server.   |
| file_size     | BIGINT UNSIGNED | Tidak | —           | Ukuran byte.                  |
| checksum      | VARCHAR(128)    |   Ya  | IDX         | Deteksi duplikasi/integritas. |
| is_active     | BOOLEAN         | Tidak | `true`      | Bukti yang sedang dinilai.    |
| created_at    | DATETIME(6)     | Tidak | —           | Waktu unggah.                 |

## 6. Shipping

### 6.1 `store_courier_rates`

| Kolom       | Tipe            | Null  | Key/Default | Deskripsi            |
| ----------- | --------------- | :---: | ----------- | -------------------- |
| id          | BIGINT UNSIGNED | Tidak | PK          | Identifier tarif.    |
| area_code   | VARCHAR(32)     |   Ya  | IDX         | Kode wilayah.        |
| area_name   | VARCHAR(191)    | Tidak | IDX         | Nama area layanan.   |
| rate_amount | DECIMAL(19,2)   | Tidak | —           | Tarif kurir toko.    |
| eta_text    | VARCHAR(100)    |   Ya  | —           | Estimasi pengiriman. |
| is_active   | BOOLEAN         | Tidak | `true`, IDX | Status tarif.        |
| created_at  | DATETIME(6)     | Tidak | —           | Waktu pembuatan.     |
| updated_at  | DATETIME(6)     | Tidak | —           | Waktu perubahan.     |

Data operasional masih [OPN-010](../requirements/BRD.md#opn-010).

### 6.2 `shipments`

| Kolom                    | Tipe            | Null  | Key/Default      | Deskripsi                               |
| ------------------------ | --------------- | :---: | ---------------- | --------------------------------------- |
| id                       | BIGINT UNSIGNED | Tidak | PK               | Identifier shipment.                    |
| order_id                 | BIGINT UNSIGNED | Tidak | FK, UK           | Satu shipment per order baseline.       |
| store_courier_rate_id    | BIGINT UNSIGNED |   Ya  | FK               | Tarif sumber bila kurir toko.           |
| method                   | VARCHAR(32)     | Tidak | IDX              | `STORE_COURIER` atau `BITESHIP`.        |
| service_code             | VARCHAR(100)    |   Ya  | —                | Kode layanan provider.                  |
| service_name_snapshot    | VARCHAR(191)    | Tidak | —                | Nama layanan saat checkout.             |
| eta_snapshot             | VARCHAR(100)    |   Ya  | —                | Estimasi saat checkout.                 |
| recipient_name_snapshot  | VARCHAR(150)    | Tidak | —                | Penerima historis.                      |
| recipient_phone_snapshot | VARCHAR(32)     | Tidak | —                | Kontak historis.                        |
| address_snapshot         | TEXT            | Tidak | —                | Alamat lengkap historis.                |
| province_snapshot        | VARCHAR(100)    | Tidak | —                | Provinsi historis.                      |
| city_snapshot            | VARCHAR(100)    | Tidak | —                | Kota/kabupaten historis.                |
| district_snapshot        | VARCHAR(100)    | Tidak | IDX              | Kecamatan untuk laporan.                |
| postal_code_snapshot     | VARCHAR(16)     |   Ya  | —                | Kode pos historis.                      |
| shipping_amount          | DECIMAL(19,2)   | Tidak | —                | Ongkir snapshot.                        |
| quote_reference          | VARCHAR(191)    |   Ya  | IDX              | Referensi quote Biteship jika tersedia. |
| tracking_number          | VARCHAR(191)    |   Ya  | IDX, Provisional | Nomor resi menunggu OPN-020.            |
| status                   | VARCHAR(32)     | Tidak | IDX              | Lifecycle fulfillment provisional.      |
| shipped_at               | DATETIME(6)     |   Ya  | —                | Waktu dikirim.                          |
| delivered_at             | DATETIME(6)     |   Ya  | —                | Waktu selesai.                          |
| created_at               | DATETIME(6)     | Tidak | —                | Waktu pembuatan.                        |
| updated_at               | DATETIME(6)     | Tidak | —                | Waktu perubahan.                        |

## 7. POS Integration dan Synchronization

### 7.1 `pos_integration_operations`

| Kolom                     | Tipe            | Null  | Key/Default | Deskripsi                                                                      |
| ------------------------- | --------------- | :---: | ----------- | ------------------------------------------------------------------------------ |
| id                        | BIGINT UNSIGNED | Tidak | PK          | Identifier operasi.                                                            |
| order_id                  | BIGINT UNSIGNED |   Ya  | FK, IDX     | Order terkait.                                                                 |
| sync_run_id               | BIGINT UNSIGNED |   Ya  | FK, IDX     | Batch terkait.                                                                 |
| operation                 | VARCHAR(64)     | Tidak | IDX         | Working name operasi POS.                                                      |
| external_reference        | VARCHAR(191)    | Tidak | UK          | Idempotency/reconciliation reference.                                          |
| request_hash              | VARCHAR(128)    | Tidak | IDX         | Hash payload canonical.                                                        |
| status                    | VARCHAR(32)     | Tidak | IDX         | `PENDING`, `SUCCEEDED`, `FAILED`, `AMBIGUOUS`, atau `RECONCILIATION_REQUIRED`. |
| attempt_count             | INT UNSIGNED    | Tidak | `0`         | Jumlah percobaan.                                                              |
| correlation_id            | VARCHAR(191)    |   Ya  | IDX         | Correlation/request ID.                                                        |
| response_reference        | VARCHAR(191)    |   Ya  | IDX         | Referensi hasil POS.                                                           |
| request_payload_redacted  | JSON            |   Ya  | —           | Payload tanpa credential/rahasia.                                              |
| response_payload_redacted | JSON            |   Ya  | —           | Respons aman untuk rekonsiliasi.                                               |
| error_code                | VARCHAR(100)    |   Ya  | IDX         | Kode error terpetakan.                                                         |
| error_message             | TEXT            |   Ya  | —           | Pesan aman/teredaksi.                                                          |
| last_attempt_at           | DATETIME(6)     |   Ya  | —           | Percobaan terakhir.                                                            |
| reconciled_at             | DATETIME(6)     |   Ya  | —           | Rekonsiliasi selesai.                                                          |
| created_at                | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                                                               |
| updated_at                | DATETIME(6)     | Tidak | —           | Waktu perubahan.                                                               |

Nama operasi, payload, auth, error, dan kontrak idempotency masih
[OPN-005](../requirements/BRD.md#opn-005).

### 7.2 `pos_returns`

| Kolom              | Tipe            | Null  | Key/Default | Deskripsi                                                         |
| ------------------ | --------------- | :---: | ----------- | ----------------------------------------------------------------- |
| id                 | BIGINT UNSIGNED | Tidak | PK          | Identifier retur.                                                 |
| order_id           | BIGINT UNSIGNED | Tidak | FK, UK      | Order yang dibatalkan.                                            |
| pos_return_id      | VARCHAR(191)    | Tidak | UK          | Referensi retur POS.                                              |
| pos_sales_order_id | VARCHAR(191)    | Tidak | IDX         | Sales order asal.                                                 |
| reason             | TEXT            | Tidak | —           | Alasan pembatalan.                                                |
| status             | VARCHAR(24)     | Tidak | IDX         | `PENDING`, `SUCCEEDED`, `FAILED`, atau `RECONCILIATION_REQUIRED`. |
| returned_at        | DATETIME(6)     |   Ya  | —           | Waktu retur berhasil.                                             |
| created_at         | DATETIME(6)     | Tidak | —           | Waktu pembuatan.                                                  |
| updated_at         | DATETIME(6)     | Tidak | —           | Waktu perubahan.                                                  |

### 7.3 `sync_runs`

| Kolom          | Tipe            | Null  | Key/Default | Deskripsi                                                                      |
| -------------- | --------------- | :---: | ----------- | ------------------------------------------------------------------------------ |
| id             | BIGINT UNSIGNED | Tidak | PK          | Identifier batch.                                                              |
| sync_type      | VARCHAR(32)     | Tidak | IDX         | `MASTER_FULL`, `INVENTORY_FULL`, `STOCK_PRODUCT`, atau `SALES_RECONCILIATION`. |
| source         | VARCHAR(16)     | Tidak | —           | `POS_API` atau `SEEDER`.                                                       |
| status         | VARCHAR(24)     | Tidak | IDX         | `RUNNING`, `SUCCEEDED`, `PARTIAL`, atau `FAILED`.                              |
| started_at     | DATETIME(6)     | Tidak | IDX         | Awal eksekusi.                                                                 |
| finished_at    | DATETIME(6)     |   Ya  | —           | Akhir eksekusi.                                                                |
| success_count  | INT UNSIGNED    | Tidak | `0`         | Jumlah berhasil.                                                               |
| failed_count   | INT UNSIGNED    | Tidak | `0`         | Jumlah gagal.                                                                  |
| correlation_id | VARCHAR(191)    |   Ya  | UK          | Korelasi log.                                                                  |
| summary        | JSON            |   Ya  | —           | Ringkasan aman.                                                                |
| created_at     | DATETIME(6)     | Tidak | —           | Waktu pencatatan.                                                              |

### 7.4 `sync_errors`

| Kolom                    | Tipe            | Null  | Key/Default | Deskripsi                      |
| ------------------------ | --------------- | :---: | ----------- | ------------------------------ |
| id                       | BIGINT UNSIGNED | Tidak | PK          | Identifier error.              |
| sync_run_id              | BIGINT UNSIGNED | Tidak | FK, IDX     | Batch pemilik error.           |
| entity_type              | VARCHAR(64)     | Tidak | IDX         | Tipe record gagal.             |
| external_id              | VARCHAR(191)    |   Ya  | IDX         | Identifier sumber.             |
| error_code               | VARCHAR(100)    | Tidak | IDX         | Kode error internal/provider.  |
| error_message            | TEXT            | Tidak | —           | Pesan aman/teredaksi.          |
| payload_excerpt_redacted | JSON            |   Ya  | —           | Konteks minimum tanpa rahasia. |
| retryable                | BOOLEAN         | Tidak | `false`     | Dapat di-retry atau tidak.     |
| created_at               | DATETIME(6)     | Tidak | —           | Waktu error.                   |

## 8. Audit

### 8.1 `audit_logs`

| Kolom          | Tipe            | Null  | Key/Default  | Deskripsi                                       |
| -------------- | --------------- | :---: | ------------ | ----------------------------------------------- |
| id             | BIGINT UNSIGNED | Tidak | PK           | Identifier log.                                 |
| actor_user_id  | BIGINT UNSIGNED |   Ya  | FK, IDX      | Null untuk proses sistem.                       |
| action         | VARCHAR(100)    | Tidak | IDX          | Contoh `CUSTOMER_APPROVED`, `PAYMENT_VERIFIED`. |
| auditable_type | VARCHAR(191)    | Tidak | IDX gabungan | Tipe entity logis.                              |
| auditable_id   | BIGINT UNSIGNED | Tidak | IDX gabungan | ID entity logis.                                |
| old_values     | JSON            |   Ya  | —            | Nilai lama teredaksi.                           |
| new_values     | JSON            |   Ya  | —            | Nilai baru teredaksi.                           |
| request_id     | VARCHAR(191)    |   Ya  | IDX          | Request/correlation ID.                         |
| ip_address     | VARCHAR(45)     |   Ya  | —            | IPv4/IPv6 jika relevan.                         |
| user_agent     | VARCHAR(500)    |   Ya  | —            | User agent terpotong.                           |
| created_at     | DATETIME(6)     | Tidak | IDX          | Waktu kejadian.                                 |

Password, token, credential POS/Biteship, nomor rekening mentah dalam log, dan
isi file bukti pembayaran tidak boleh masuk `old_values` atau `new_values`.

## 9. Constraint dan Index Minimum

| Area       | Constraint/Index                                                                                                                  |
| ---------- | --------------------------------------------------------------------------------------------------------------------------------- |
| Identity   | UK `users.email`, UK `users.phone`, UK `customer_profiles.user_id`.                                                               |
| Product    | UK `products.pos_product_id`, UK `products.sku`, UK gabungan `product_prices(product_id, price_type)`.                            |
| Cart       | UK parsial/logis satu `carts` aktif per user; UK `cart_items(cart_id, product_id)`.                                               |
| Order      | UK `orders.order_number`, UK `orders.idempotency_key`, UK nullable `orders.pos_sales_order_id`.                                   |
| Snapshot   | Index `order_items.sku_snapshot`, `shipments.district_snapshot`, dan tanggal/status order untuk laporan.                          |
| Invoice    | UK `invoices.order_id`, UK `invoices.invoice_number`, UK nullable `invoices.pos_invoice_id`.                                      |
| POS        | UK `pos_integration_operations.external_reference`, UK `pos_returns.order_id`, UK `pos_returns.pos_return_id`.                    |
| Inventory  | UK `inventory_snapshots.product_id`; index `inventory_ledger(product_id, occurred_at)`.                                           |
| Sync/Audit | Index `sync_runs(sync_type, started_at)`, `sync_errors(sync_run_id)`, dan `audit_logs(auditable_type, auditable_id, created_at)`. |

## 10. Delete dan Retention

- Master POS tidak dihapus otomatis ketika hilang dari payload; tandai nonaktif
  dan rekonsiliasi.
- Order, item, invoice, payment, shipment, inventory ledger, integration
  operation, retur, dan audit log tidak menggunakan cascade delete.
- Penghapusan user tidak boleh menghilangkan transaksi historis; kebijakan
  anonimisasi/retention final mengikuti kebijakan privacy dan operasional.
- Media produk dan bukti pembayaran dihapus melalui service agar record dan
  object storage tetap konsisten.
- Retention log dan bukti pembayaran harus ditetapkan sebelum production
  ([OPN-009](../requirements/BRD.md#opn-009)).
