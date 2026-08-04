# Sprint 1 Demo

Demo Sprint 1 fokus pada fondasi auth, approval customer, visibilitas harga, dan data contoh katalog.

## Scope demo

- registrasi pelanggan baru
- login/logout
- status `PENDING_VERIFICATION`, `ACTIVE`, `REJECTED`, `SUSPENDED`
- dashboard akun pelanggan
- address book pelanggan
- customer management admin: list, search, filter, detail, approve/reject/suspend/reactivate
- gating akses harga antara guest/pending/active/admin
- seed/import data contoh katalog, harga, dan stok

## Skenario demo

### 1. Guest registrasi

1. Buka `GET /daftar`
2. Isi nama, nama usaha, phone, email, password
3. Submit
4. Verifikasi hasil:
   - akun berhasil dibuat
   - user otomatis login
   - status customer = `PENDING_VERIFICATION`
   - user masuk ke `GET /akun`

### 2. Pending customer dibatasi

1. Login sebagai customer pending
2. Buka `GET /`
3. Verifikasi hasil:
   - katalog publik tetap terlihat
   - nilai harga tidak terlihat
   - route terlindungi seperti `GET /checkout` dan `GET /orders` tertolak

### 3. Admin review customer

1. Login sebagai admin
2. Buka `GET /admin/customers`
3. Cari customer berdasarkan nama/email/phone/usaha
4. Buka detail pendaftar
5. Jalankan aksi berikut:
   - approve
   - reject dengan alasan
   - suspend dengan alasan
   - reactivate
6. Verifikasi `reviewed_by`, `reviewed_at`, status, dan alasan berubah sesuai aksi

### 4. Active customer mendapat akses

1. Login sebagai customer active
2. Buka `GET /`
3. Verifikasi hasil:
   - harga contoh terlihat
   - `GET /checkout` dan `GET /orders` bisa dibuka
   - `GET /akun` menampilkan status active

### 5. Address book customer

1. Login sebagai customer active
2. Buka `GET /akun`
3. Update profil
4. Tambah alamat baru
5. Tandai satu alamat sebagai default
6. Update alamat
7. Hapus alamat non-default
8. Verifikasi:
   - hanya alamat milik sendiri yang bisa diubah
   - default address berpindah dengan benar

### 6. Import sample catalog

1. Jalankan `php artisan catalog:import-sample`
2. Verifikasi:
   - kategori, brand, produk, harga, dan snapshot stok terisi
   - import bisa dijalankan ulang tanpa duplikasi master

## Acceptance Sprint 1

Sprint 1 layak dianggap siap review internal bila:

- registrasi, login, logout lulus test
- approval admin dan perubahan status lulus test
- gating akses harga / checkout / order history lulus test
- profile dan address book customer lulus test
- admin search/filter/detail pendaftar lulus test
- import data contoh dan pricing/PPh 22 dasar lulus test

## Test command

```bash
php artisan test --compact
```
