# Sprint 2 Demo

Demo Sprint 2 fokus pada harga partai, PPh 22, cart, checkout, order, invoice, dan integrasi POS follow-up.

## Scope demo

- eligibility harga partai (min 5 unit/SKU, tidak digabung antar-SKU)
- admin kelola klasifikasi + ambang nilai belanja PPh 22
- admin kelola tarif PPh 22 (termasuk 0%)
- engine kalkulasi PPh 22 multi-klasifikasi + snapshot transaksi
- cart: validasi harga/stok/PPh22 server-side
- checkout: subtotal, ongkir, PPh22 terpisah, total
- commit atomik order + invoice + kurangi stok efektif
- invoice: identitas toko + nilai PPh 22
- scheduler auto-cancel unpaid D+1
- riwayat transaksi pelanggan
- endpoint POS follow-up/acknowledgement

## Skenario demo

### 1. Eligibility harga partai

1. Login sebagai customer aktif
2. Tambahkan 4 unit SKU-A dan 4 unit SKU-B ke keranjang
3. Verifikasi harga per baris masih tier eceran (belum trigger partai cart-wide)
4. Tambahkan SKU-A menjadi 5 unit
5. Verifikasi seluruh baris eligible memakai harga partai

### 2. Admin konfigurasi PPh 22

1. Login sebagai admin
2. Buka `GET /admin/tax-rules`
3. Edit kategori, ubah ambang dan tarif (uji tarif 0%)
4. Simpan dan verifikasi perubahan tercermin di ringkasan keranjang

### 3. Checkout dan invoice

1. Customer aktif checkout dengan alamat default
2. Pilih opsi pengiriman mock
3. Verifikasi breakdown: subtotal, PPh 22, ongkir, grand total
4. Submit order
5. Verifikasi:
   - order `unpaid` + stok berkurang
   - invoice berisi identitas toko (nama, NPWP)
   - `tax_pph22_snapshot` tersimpan di order

### 4. Riwayat transaksi

1. Buka `GET /akun/pesanan`
2. Buka detail order
3. Verifikasi item, total, PPh 22, dan status

### 5. Auto-cancel D+1

1. Buat order unpaid dengan `created_at` hari sebelumnya (UAT/seed)
2. Jalankan `php artisan orders:auto-cancel-unpaid`
3. Verifikasi status `cancelled` dan stok kembali

### 6. POS follow-up endpoint

1. Buat order unpaid dari checkout
2. Panggil `GET /api/pos/orders/{order_number}` dengan header `Authorization: Bearer {POS_API_TOKEN}`
3. Verifikasi payload order, invoice, items, dan `acknowledged_at`

## Acceptance Sprint 2

Sprint 2 layak dianggap siap review internal bila:

- unit test partai eligibility dan PriceCalculator lulus
- feature test admin tax rules, checkout/order, auto-cancel, dan POS API lulus
- skenario demo 1–6 dapat dijalankan end-to-end di staging/dev

## Test command

```bash
php artisan test --compact
```

## Env tambahan

```env
STORE_NAME="Pixel Komunika"
STORE_ADDRESS="Jl. Contoh No. 1, Bandung"
STORE_PHONE="022-1234567"
STORE_NPWP="00.000.000.0-000.000"
POS_API_TOKEN=your-pos-token
```
