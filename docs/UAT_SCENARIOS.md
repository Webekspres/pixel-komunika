---
title: Skenario User Acceptance Test (UAT)
subtitle: Website B2B Pixel Komunika — MVP
---

| | |
|---|---|
| **Versi dokumen** | 1.0 — 5 Oktober 2026 |
| **Disusun oleh** | Webekspres Teknologi Indonesia |
| **Pelaksana uji** | Pixel Komunika (klien) dan tim IT Pixel Komunika |
| **Jadwal UAT** | Kamis, 15 Oktober 2026 |
| **Lingkungan uji** | Staging — `https://dev.store.pixelkomunika.com` |

# 1. Tujuan

Dokumen ini berisi daftar langkah uji untuk memastikan website sudah sesuai
kebutuhan bisnis Pixel Komunika sebelum digunakan secara resmi (go-live).

> **Catatan penting.** Dokumen ini adalah **panduan** pengecekan. Kolom
> **Hasil** dan **Catatan** **tidak wajib diisi** oleh klien. Namun dokumen ini
> **wajib dibaca dan dipahami** sebelum UAT, agar seluruh pihak memiliki
> gambaran yang sama tentang apa yang diuji dan hasil yang diharapkan, sehingga
> UAT berjalan lancar.

- Setiap skenario punya **langkah** dan **hasil yang diharapkan**. Penguji cukup
  mengikuti langkahnya. Kolom **Hasil** (Lulus / Gagal) dan **Catatan** boleh
  dipakai bila membantu.
- UAT dinyatakan **diterima** bila seluruh skenario berprioritas **Tinggi**
  lulus dan tidak ada temuan berkategori *Bug kritis* yang terbuka.
- Hasil UAT ditandatangani pada bagian **12. Persetujuan (Sign-off)**.

# 2. Persiapan

**Akun uji.** Kata sandi dikirim terpisah oleh Webekspres Teknologi Indonesia, tidak dicantumkan di
dokumen ini.

| Peran | Email | Kondisi akun |
|---|---|---|
| Admin | `admin@pixelkomunika.test` | Admin toko |
| Reseller aktif | `test@example.com` | Sudah disetujui, bisa belanja |
| Pendaftar baru | `pending@example.com` | Menunggu verifikasi |
| Akun ditolak | `rejected@example.com` | Pendaftaran ditolak |
| Akun ditangguhkan | `suspended@example.com` | Akun dibekukan |

**Data uji.** Produk, harga, dan stok di staging adalah **data contoh** yang
strukturnya sama dengan data POS. Angka harga/stok tidak mencerminkan kondisi
toko sebenarnya.

**Perangkat.** Disarankan menguji di laptop (Chrome/Edge) dan di HP, terutama
untuk alur pelanggan.

**Prioritas.** **Tinggi** = wajib lulus untuk go-live. **Sedang** = penting,
boleh diperbaiki setelah go-live bila ada solusi sementara.

# 3. Keputusan sementara yang mohon dikonfirmasi

Beberapa hal di bawah ini belum diputuskan final oleh Pixel Komunika, sehingga
Webekspres Teknologi Indonesia menetapkan aturan sementara. **Bila tidak ada komentar saat UAT,
aturan ini dianggap disetujui.** Perubahan format nomor sebaiknya diputuskan
sebelum transaksi pertama di production.

| No | Hal | Aturan sementara | Skenario terkait |
|---|---|---|---|
| K-1 | Format nomor pesanan & invoice | `PK-20261015-A7K2Q` dan `INV-20261015-A7K2Q` (tanggal + 5 karakter acak, tidak berurutan) | CHK-08 |
| K-2 | Format nomor akun reseller | `PKR-000123`, terbit otomatis saat pendaftaran disetujui | REG-05 |
| K-3 | PPh 22 bila beberapa klasifikasi terkena sekaligus | Dasar = jumlah subtotal klasifikasi yang terkena; tarif yang dipakai = **tarif tertinggi**; dibulatkan ke rupiah terdekat | CHK-05 |
| K-4 | Admin menyelesaikan pesanan secara manual | Diizinkan untuk pesanan berstatus *Dikirim* (dengan konfirmasi, tercatat di audit log) | FUL-03 |
| K-5 | Hari kerja untuk penyelesaian otomatis | Senin–Jumat, tidak termasuk libur nasional & cuti bersama SKB 3 Menteri | FUL-05 |
| K-6 | Notifikasi WhatsApp order baru | Melalui layanan Fonnte ke nomor admin `081546407702` dengan pesan "Cek Order masuk" | CHK-08 |
| K-7 | Pembatalan pesanan | Hanya oleh **admin** (hari yang sama) atau **sistem** (tidak dibayar). Pelanggan tidak dapat membatalkan sendiri; pelanggan menghubungi admin | CAN-01, CAN-04 |
| K-8 | Pengajuan retur | Pelanggan mengajukan retur lewat tombol **Ajukan Retur via WhatsApp** pada pesanan *Dikirim/Selesai*; retur diproses admin di luar website | FUL-07 |

# 4. Registrasi, Persetujuan, dan Hak Akses

| ID | Prioritas | Skenario | Langkah | Hasil yang diharapkan | Hasil | Catatan |
|---|---|---|---|---|---|---|
| REG-01 | Tinggi | Pendaftaran pelanggan baru | Buka **Daftar**, isi data usaha, email, dan nomor HP baru, kirim. | Akun terbentuk dengan status **Menunggu Verifikasi**. Harga produk belum terlihat. | | |
| REG-02 | Tinggi | Email / nomor HP sudah terdaftar | Daftar memakai email atau nomor HP yang sudah dipakai akun lain. | Pendaftaran ditolak dengan pesan yang jelas. | | |
| REG-03 | Tinggi | Pengunjung tanpa login | Tanpa login, buka katalog dan detail produk. | Produk tampil **tanpa harga**. Tidak bisa checkout. | | |
| REG-04 | Tinggi | Akun menunggu verifikasi | Login sebagai `pending@example.com`, buka katalog. | Harga tetap tersembunyi; kartu produk menampilkan keterangan verifikasi akun. | | |
| REG-05 | Tinggi | Admin menyetujui pendaftar | Admin: **Pelanggan** → buka pendaftar REG-01 → **Setujui**. Login ulang sebagai pelanggan tersebut. | Status **Aktif**, nomor akun reseller terbit (format K-2). Pelanggan bisa melihat harga dan belanja. | | |
| REG-06 | Tinggi | Admin menolak pendaftar | Admin menolak pendaftar lain dengan mengisi alasan. | Status **Ditolak**, alasan tersimpan. Pelanggan tidak bisa melihat harga. | | |
| REG-07 | Sedang | Tangguhkan & aktifkan kembali | Admin menangguhkan akun aktif, lalu mengaktifkannya kembali. | Saat ditangguhkan pelanggan tidak bisa belanja; setelah diaktifkan bisa belanja lagi. | | |
| REG-08 | Tinggi | Login & logout | Login dengan akun aktif, lalu logout. Coba login dengan kata sandi salah. | Login/logout berhasil. Kata sandi salah ditolak. | | |
| REG-09 | Sedang | Profil & alamat | Pelanggan: **Akun** → ubah profil; tambah, ubah, dan hapus alamat. | Perubahan tersimpan. Hapus alamat meminta konfirmasi terlebih dahulu. | | |
| REG-10 | Tinggi | Batas akses | Login sebagai pelanggan lalu buka alamat `/admin`. Buka juga pesanan milik pelanggan lain dengan mengganti nomor di alamat browser. | Akses ditolak pada kedua percobaan. | | |

# 5. Katalog dan Harga

| ID | Prioritas | Skenario | Langkah | Hasil yang diharapkan | Hasil | Catatan |
|---|---|---|---|---|---|---|
| KAT-01 | Tinggi | Jelajah katalog | Buka katalog, pilih kategori, cari produk, buka detail produk. | Produk, foto, kategori, dan merek tampil sesuai. Kategori kosong tidak membuat halaman error. | | |
| KAT-02 | Tinggi | Harga eceran tidak tampil | Sebagai reseller aktif, perhatikan harga di katalog dan detail produk. | Harga yang tampil adalah harga reseller (grosir/partai), **bukan harga eceran**. | | |
| KAT-03 | Tinggi | Harga grosir | Masukkan produk ke keranjang dengan jumlah ≥ minimum grosir produk tersebut. | Harga satuan berubah menjadi harga grosir. | | |
| KAT-04 | Tinggi | Harga partai — satu produk ≥ 5 unit | Masukkan produk A sebanyak 5 unit dan produk B sebanyak 1 unit. | Harga **partai** berlaku untuk **seluruh** produk di keranjang yang memiliki harga partai. | | |
| KAT-05 | Tinggi | Harga partai — tidak digabung antar produk | Kosongkan keranjang. Masukkan produk A 3 unit dan produk B 3 unit. | Jumlah antar produk tidak dijumlahkan: tombol checkout diganti pesan "Tambahkan minimal 5 unit untuk salah satu produk agar bisa checkout." Harga eceran tidak tampil. Tambah produk A menjadi 5 unit: tombol **Lanjut Checkout** muncul. | | |
| KAT-06 | Sedang | Kelengkapan produk oleh admin | Admin: **Produk** → ubah nama tampilan, foto/video, berat, dan dimensi suatu produk. | Perubahan tampil di website dan tidak hilang setelah sinkronisasi data produk. | | |
| KAT-07 | Sedang | Minimum partai | Admin: **Pengaturan** → ubah minimum partai (misal 6), ulangi KAT-04 dengan 5 unit. | Checkout belum bisa dilakukan sampai salah satu produk mencapai minimum baru. Kembalikan ke 5 setelah uji. | | |

# 6. Keranjang dan Checkout

| ID | Prioritas | Skenario | Langkah | Hasil yang diharapkan | Hasil | Catatan |
|---|---|---|---|---|---|---|
| CHK-01 | Tinggi | Kelola keranjang | Tambah produk, ubah jumlah, hapus produk dari keranjang. | Subtotal selalu sesuai jumlah dan harga terbaru. | | |
| CHK-02 | Tinggi | Stok tidak cukup | Isi jumlah melebihi stok produk. | Sistem menolak dan memberi tahu stok yang tersedia. | | |
| CHK-03 | Tinggi | Ringkasan biaya | Lanjut ke **Checkout**, pilih alamat dan pengiriman. | Ringkasan menampilkan **subtotal, PPh 22, ongkir, dan total** secara terpisah. | | |
| CHK-04 | Tinggi | PPh 22 tidak dikenakan | Belanja produk dari kategori yang subtotalnya **di bawah atau sama dengan** ambang PPh 22 (atau tarifnya 0%). | Baris PPh 22 bernilai Rp0 / tidak muncul. | | |
| CHK-05 | Tinggi | PPh 22 dikenakan | Belanja produk dari kategori ber-PPh 22 sampai subtotal kategori **melebihi** ambang. | PPh 22 = (subtotal kategori terkena ÷ 1,11) × tarif. Contoh: subtotal Rp11.100.000, tarif 1,5% → PPh 22 Rp150.000. Lihat juga K-3. | | |
| CHK-06 | Tinggi | Kurir toko — tarif area | Pilih alamat di Bandung dengan total belanja (subtotal + PPh 22) < Rp1.000.000, pilih **Kurir Toko**. | Ongkir sesuai tarif kecamatan; estimasi H+1 hari kerja. | | |
| CHK-07 | Tinggi | Kurir toko — gratis ongkir | Ulangi CHK-06 dengan total belanja (subtotal + PPh 22) ≥ Rp1.000.000. | Ongkir Kurir Toko **Rp0 (gratis)**. | | |
| CHK-08 | Tinggi | Membuat pesanan | Selesaikan checkout. | Nomor pesanan & invoice terbit (format K-1), status **Menunggu Pembayaran**, stok produk berkurang. Admin melihat **tanda merah** pesanan baru; WhatsApp terkirim ke nomor admin (K-6, bila nomor Fonnte sudah aktif). | | |
| CHK-09 | Tinggi | Invoice | Buka detail pesanan → **Invoice**, lalu **Unduh PDF**. | Invoice memuat identitas & NPWP toko, nomor akun reseller, daftar barang + SKU, harga, PPh 22, ongkir, dan total. PDF dapat diunduh. | | |
| CHK-10 | Sedang | Pengiriman ekspedisi | Pilih alamat di luar Bandung. | Pilihan kurir ekspedisi beserta ongkirnya tampil. *Di staging tarif masih simulasi; tarif asli aktif setelah akun Biteship klien terdaftar.* | | |

# 7. Pembayaran

| ID | Prioritas | Skenario | Langkah | Hasil yang diharapkan | Hasil | Catatan |
|---|---|---|---|---|---|---|
| PAY-01 | Tinggi | Rekening tujuan | Buka detail pesanan yang menunggu pembayaran. | Rekening bank tujuan transfer yang aktif tampil. | | |
| PAY-02 | Tinggi | Unggah bukti bayar | Unggah bukti transfer (JPG/PNG/WEBP/PDF, maks. 5 MB). | Status berubah menjadi **Pembayaran Diajukan**. | | |
| PAY-03 | Sedang | Format bukti tidak valid | Unggah file selain gambar/PDF atau lebih dari 5 MB. | Unggahan ditolak dengan pesan yang jelas. | | |
| PAY-04 | Tinggi | Admin menyetujui pembayaran | Admin: **Pembayaran** → buka bukti → **Setujui**. | Pesanan menjadi **Diproses**, invoice **Lunas**. Bukti hanya bisa dilihat admin dan pemilik pesanan. | | |
| PAY-05 | Tinggi | Admin menolak pembayaran | Pada pesanan lain, admin **Tolak** bukti dengan alasan. Pelanggan mengunggah ulang. | Status **Pembayaran Ditolak**; pelanggan dapat mengunggah bukti baru dan status kembali **Pembayaran Diajukan**. | | |

# 8. Pembatalan dan Pemrosesan Pesanan

| ID | Prioritas | Skenario | Langkah | Hasil yang diharapkan | Hasil | Catatan |
|---|---|---|---|---|---|---|
| CAN-01 | Tinggi | Admin membatalkan (hari yang sama) | Buat pesanan baru. Admin: **Pesanan** → **Batalkan** dengan alasan. | Status **Dibatalkan**, alasan tercatat, stok produk kembali. | | |
| CAN-02 | Tinggi | Batas pembatalan admin | Lihat pesanan belum dibayar yang dibuat **kemarin** atau pesanan yang sudah **Diproses**. | Tombol batal tidak tersedia. | | |
| CAN-03 | Tinggi | Batal otomatis tidak dibayar | Biarkan pesanan **Menunggu Pembayaran** sampai lewat tengah malam (cek keesokan harinya). | Pesanan otomatis **Dibatalkan** oleh sistem, stok kembali. *Dapat diperagakan Webekspres Teknologi Indonesia bila tidak sempat menunggu.* | | |
| CAN-04 | Tinggi | Pelanggan tidak dapat membatalkan | Login sebagai pelanggan, buka pesanan **Menunggu Pembayaran**. | Tidak ada tombol batal; pembatalan hanya melalui admin (K-7). | | |
| FUL-01 | Tinggi | Alur pemrosesan | Admin membuka pesanan **Diproses** → **Dikemas** → **Dikirim** (isi nomor resi). | Status berubah berurutan; pelanggan melihat status dan nomor resi; pelanggan menerima tautan konfirmasi penerimaan. | | |
| FUL-02 | Tinggi | Konfirmasi penerimaan pelanggan | Buka tautan konfirmasi penerimaan untuk pesanan **Dikirim** → konfirmasi. | Pesanan menjadi **Selesai**. Membuka tautan yang sama lagi hanya menampilkan info bahwa pesanan sudah dikonfirmasi. | | |
| FUL-03 | Sedang | Admin menyelesaikan manual | Pada pesanan **Dikirim** lain, admin klik **Selesai** dan konfirmasi. | Pesanan **Selesai** dan tercatat di audit log (K-4). | | |
| FUL-04 | Sedang | Pengiriman terkendala | Admin menandai pesanan **Dikirim** sebagai **TERKENDALA**. | Tombol Selesai tidak muncul, dan pesanan tidak diselesaikan otomatis. | | |
| FUL-05 | Sedang | Selesai otomatis | Pesanan **Dikirim** tanpa konfirmasi pelanggan selama 5 hari kerja. | Pesanan otomatis **Selesai**; libur nasional tidak dihitung (K-5). *Diperagakan Webekspres Teknologi Indonesia.* | | |
| FUL-07 | Sedang | Pengajuan retur | Pelanggan membuka pesanan **Dikirim** atau **Selesai** → **Ajukan Retur via WhatsApp**. | WhatsApp terbuka ke nomor admin dengan pesan berisi nomor pesanan (K-8). Tombol tidak muncul pada pesanan yang belum dikirim. | | |
| FUL-06 | Sedang | Riwayat pesanan | Pelanggan: **Pesanan** → gunakan tab status. | Semua pesanan pelanggan tampil sesuai status masing-masing. | | |

# 9. Admin: Laporan, Audit, dan Pengaturan

| ID | Prioritas | Skenario | Langkah | Hasil yang diharapkan | Hasil | Catatan |
|---|---|---|---|---|---|---|
| ADM-01 | Tinggi | Laporan omzet | Admin: **Laporan** → pilih periode dan kecamatan. | Omzet hanya dari pesanan **Dikirim/Selesai**; PPh 22 ditampilkan terpisah; angka sesuai pesanan uji. | | |
| ADM-02 | Tinggi | Audit log | Admin: **Audit Log** → filter berdasarkan pelaku, aksi, dan periode. | Aksi penting pada skenario sebelumnya (persetujuan akun, verifikasi pembayaran, pembatalan, perubahan status) tercatat. | | |
| ADM-03 | Tinggi | Aturan PPh 22 | Admin: **Aturan PPh 22** → ubah ambang dan tarif (termasuk 0%) suatu kategori, lalu checkout ulang. | Perhitungan PPh 22 di checkout mengikuti aturan baru. Kembalikan nilai awal setelah uji. | | |
| ADM-04 | Tinggi | Pengaturan toko | Admin: **Pengaturan** → periksa/ubah identitas toko, NPWP, rekening bank, dan tarif kurir per kecamatan. | Perubahan tersimpan dan tampil di invoice / checkout berikutnya. | | |
| ADM-05 | Sedang | Dashboard admin | Buka **Dashboard** admin. | Ringkasan pesanan, pembayaran menunggu, dan pendaftar baru tampil sesuai data. | | |

# 10. Di luar cakupan UAT ini

Hal berikut **belum** diuji pada 15 Oktober karena masih menunggu pihak
eksternal. Masing-masing akan diverifikasi terpisah sebelum/sesudah go-live.

| Hal | Status | Keterangan |
|---|---|---|
| Sinkronisasi stok & harga langsung dari POS | Menunggu vendor POS | UAT memakai data contoh |
| Pelaporan penjualan & retur ke POS | Menunggu vendor POS | Diuji pada contract test POS |
| Tarif & pemesanan kurir Biteship asli | Menunggu akun Biteship klien | Staging memakai tarif simulasi |
| WhatsApp order baru | Menunggu pendaftaran nomor di Fonnte | Tanda merah admin sudah berfungsi |
| Kirim invoice via WhatsApp/email, poin loyalitas, ongkir pesanan gabungan, lupa kata sandi | Fase berikutnya | Bukan bagian MVP |

# 11. Pencatatan Temuan

Setiap temuan dicatat dengan merujuk ID skenario dan dikelompokkan sebagai:

| Kategori | Arti | Tindak lanjut |
|---|---|---|
| **Bug kritis** | Alur utama tidak bisa dijalankan / hasil salah (misal total atau pajak keliru) | Diperbaiki sebelum go-live |
| **Bug minor** | Ada solusi sementara, tidak menghambat transaksi | Dijadwalkan setelah go-live |
| **Perubahan** | Sistem berjalan sesuai dokumen, tetapi klien menginginkan perilaku lain | Dibahas dan disepakati terpisah |
| **Di luar scope** | Kebutuhan baru di luar MVP | Masuk fase berikutnya |

| No | ID Skenario | Temuan | Kategori | Pelapor |
|---|---|---|---|---|
| 1 | | | | |
| 2 | | | | |
| 3 | | | | |
| 4 | | | | |
| 5 | | | | |

# 12. Persetujuan (Sign-off)

Dengan menandatangani bagian ini, Pixel Komunika menyatakan hasil UAT sebagai
berikut:

☐ **Diterima** — website siap go-live.\
☐ **Diterima dengan catatan** — siap go-live setelah temuan kritis nomor ............ diperbaiki.\
☐ **Belum diterima** — perlu UAT ulang.

| Peran | Nama | Tanda tangan | Tanggal |
|---|---|---|---|
| Perwakilan Pixel Komunika | | | |
| Perwakilan Tim IT Pixel Komunika | | | |
| Webekspres Teknologi Indonesia | | | |
