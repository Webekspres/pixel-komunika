# User Flows

## Pixel Komunika E-Commerce

| Metadata | Nilai |
|---|---|
| Versi | 0.2 - Biteship Maps/Rates Flow |
| Tanggal | Selasa, 28 Juli 2026 |
| Status | Internal - granular MVP flow |
| Sumber | [BRD](../requirements/BRD.md), [FRD](../requirements/FRD.md), [SRS](../requirements/SRS.md), dan [MVP](../requirements/MVP.md) |
| Model data | [ERD](ERD.md) dan [Data Dictionary](DATA_DICTIONARY.md) |

## 1. Konvensi

- Setiap diagram menangani satu tujuan operasional dan memiliki ID stabil.
- Node berbentuk `→ UF-xx` adalah konektor ke flow lain, bukan proses baru.
- Konektor dapat diklik pada renderer Mermaid yang mengizinkan link lokal;
  tautan Markdown tetap disediakan setelah diagram sebagai fallback.
- Label `Provisional` menunjukkan cabang yang bergantung pada open question.
- Validasi dan kalkulasi kritis selalu dilakukan server-side.
- Flow notifikasi, reseller, refund, booking kurir Biteship, split shipment, dan
  penggabungan order tidak dimasukkan karena belum menjadi baseline MVP.

## 2. Indeks Flow

| ID | Flow | Aktor Utama | Output |
|---|---|---|---|
| [UF-01](#uf-01-akses-katalog-publik) | Akses katalog publik | Guest, pending, aktif | Katalog sesuai hak akses |
| [UF-02](#uf-02-registrasi-pelanggan) | Registrasi pelanggan | Guest | Akun pending |
| [UF-03](#uf-03-review-dan-status-pelanggan) | Review dan status pelanggan | Admin | Akun aktif/rejected/suspended |
| [UF-04](#uf-04-login-dan-routing-akses) | Login dan routing akses | Pengguna | Sesi sesuai role/status |
| [UF-05](#uf-05-detail-produk-dan-cart) | Detail produk dan cart | Pelanggan aktif | Cart terisi |
| [UF-06](#uf-06-rekalkulasi-harga-dan-pph-22) | Rekalkulasi harga dan PPh 22 | Sistem | Total cart server-side |
| [UF-07](#uf-07-alamat-dan-pengiriman) | Alamat dan pengiriman | Pelanggan aktif, Biteship Maps/Rates | Snapshot ongkir terpilih |
| [UF-08](#uf-08-validasi-checkout) | Validasi checkout | Pelanggan aktif, sistem | Draft order idempotent |
| [UF-09](#uf-09-sales-order-pos-dan-invoice) | Sales order POS dan invoice | Sistem, POS | Order aktif dan invoice |
| [UF-10](#uf-10-pengajuan-pembayaran) | Pengajuan pembayaran | Pelanggan aktif | Bukti pembayaran submitted |
| [UF-11](#uf-11-verifikasi-pembayaran) | Verifikasi pembayaran | Admin | Pembayaran verified/rejected |
| [UF-12](#uf-12-fulfillment-dan-penyelesaian) | Fulfillment dan penyelesaian | Admin | Order completed |
| [UF-13](#uf-13-pembatalan-dan-retur-pos) | Pembatalan dan retur POS | Admin, scheduler, POS | Order cancelled dan stok kembali |
| [UF-14](#uf-14-sinkronisasi-master-dan-harga-pos) | Sinkronisasi master dan harga | Worker, POS/seeder | Master produk terkini |
| [UF-15](#uf-15-sinkronisasi-dan-stok-efektif) | Sinkronisasi dan stok efektif | Worker, POS | Snapshot dan ledger stok |
| [UF-16](#uf-16-enrichment-dan-visibilitas-produk) | Enrichment dan visibilitas | Admin | Presentasi produk terkini |
| [UF-17](#uf-17-konfigurasi-operasional) | Konfigurasi operasional | Admin | Aturan bisnis aktif |
| [UF-18](#uf-18-laporan-dan-audit) | Laporan dan audit | Admin | Laporan snapshot dan audit trail |

## 3. Customer-facing Flows

### UF-01 Akses Katalog Publik

```mermaid
flowchart TD
    A([Mulai]) --> B["Pengunjung membuka katalog"]
    B --> C{"Status sesi?"}
    C -->|Guest| D["Tampilkan produk aktif tanpa harga"]
    C -->|Pending, rejected, suspended| D
    C -->|Pelanggan aktif| E["Tampilkan produk aktif dengan harga yang berlaku"]
    C -->|Admin| F["Tampilkan katalog dan data harga hasil sinkronisasi"]
    D --> G{"Aksi?"}
    G -->|Lihat produk| C_UF05(["→ UF-05"])
    G -->|Daftar| C_UF02(["→ UF-02"])
    G -->|Login| C_UF04(["→ UF-04"])
    E --> C_UF05
    F --> C_UF16(["→ UF-16"])

    click C_UF02 "#uf-02-registrasi-pelanggan"
    click C_UF04 "#uf-04-login-dan-routing-akses"
    click C_UF05 "#uf-05-detail-produk-dan-cart"
    click C_UF16 "#uf-16-enrichment-dan-visibilitas-produk"
```

Lanjutan: [UF-02 Registrasi](#uf-02-registrasi-pelanggan),
[UF-04 Login](#uf-04-login-dan-routing-akses),
[UF-05 Detail Produk](#uf-05-detail-produk-dan-cart), atau
[UF-16 Enrichment](#uf-16-enrichment-dan-visibilitas-produk).

### UF-02 Registrasi Pelanggan

```mermaid
flowchart TD
    A([Guest memilih daftar]) --> B["Isi identitas dan data usaha wajib"]
    B --> C["Kirim formulir"]
    C --> D{"Validasi server lulus?"}
    D -->|Tidak| E["Tampilkan error per field tanpa menyimpan akun"]
    E --> B
    D -->|Ya| F{"Email dan nomor telepon unik?"}
    F -->|Tidak| G["Tolak dengan pesan aman"]
    G --> B
    F -->|Ya| H["Buat user dan customer profile"]
    H --> I["Set PENDING_VERIFICATION"]
    I --> J["Catat audit registrasi"]
    J --> K["Tampilkan informasi menunggu persetujuan"]
    K --> C_UF01(["→ UF-01"])
    I --> C_UF03(["→ UF-03"])

    click C_UF01 "#uf-01-akses-katalog-publik"
    click C_UF03 "#uf-03-review-dan-status-pelanggan"
```

Lanjutan: pelanggan kembali ke
[UF-01 Katalog Publik](#uf-01-akses-katalog-publik); admin memproses melalui
[UF-03 Review Pelanggan](#uf-03-review-dan-status-pelanggan).

### UF-03 Review dan Status Pelanggan

```mermaid
flowchart TD
    A([Admin membuka customer management]) --> B["Cari atau filter pelanggan"]
    B --> C["Buka detail dan data registrasi"]
    C --> D{"Aksi admin?"}
    D -->|Setujui pending| E["Set status ACTIVE"]
    D -->|Tolak pending| F["Wajib isi alasan"]
    F --> G["Set status REJECTED"]
    D -->|Tangguhkan active| H["Wajib isi alasan"]
    H --> I["Set status SUSPENDED"]
    D -->|Aktifkan kembali| E
    E --> J["Simpan reviewer dan waktu"]
    G --> J
    I --> J
    J --> K["Catat old/new value pada audit log"]
    K --> C_UF04(["→ UF-04"])

    click C_UF04 "#uf-04-login-dan-routing-akses"
```

Lanjutan: status baru diterapkan pada
[UF-04 Login dan Routing](#uf-04-login-dan-routing-akses).

### UF-04 Login dan Routing Akses

```mermaid
flowchart TD
    A([Pengguna membuka login]) --> B["Masukkan kredensial"]
    B --> C{"Kredensial valid?"}
    C -->|Tidak| D["Tampilkan pesan generik"]
    D --> B
    C -->|Ya| E["Buat sesi dan catat login"]
    E --> F{"Role dan status?"}
    F -->|Admin| C_UF03(["→ UF-03"])
    F -->|Customer ACTIVE| C_UF05(["→ UF-05"])
    F -->|Pending, rejected, suspended| G["Tolak akses harga, checkout, dan riwayat"]
    G --> C_UF01(["→ UF-01"])

    click C_UF01 "#uf-01-akses-katalog-publik"
    click C_UF03 "#uf-03-review-dan-status-pelanggan"
    click C_UF05 "#uf-05-detail-produk-dan-cart"
```

Lanjutan: [UF-01 Katalog Publik](#uf-01-akses-katalog-publik),
[UF-03 Customer Management](#uf-03-review-dan-status-pelanggan), atau
[UF-05 Produk dan Cart](#uf-05-detail-produk-dan-cart).

### UF-05 Detail Produk dan Cart

```mermaid
flowchart TD
    A([Buka detail produk]) --> B{"Produk aktif dan visible?"}
    B -->|Tidak| C["Tampilkan produk tidak tersedia"]
    C --> C_UF01(["→ UF-01"])
    B -->|Ya| D{"Pelanggan ACTIVE?"}
    D -->|Tidak| E["Tampilkan detail tanpa harga dan tombol cart"]
    E --> C_UF01
    D -->|Ya| F["Tampilkan harga berlaku dan status stok"]
    F --> G["Pelanggan menentukan kuantitas"]
    G --> H{"Kuantitas valid terhadap stok efektif?"}
    H -->|Tidak| I["Tampilkan stok tidak mencukupi"]
    I --> G
    H -->|Ya| J["Tambah atau update satu baris SKU di cart"]
    J --> C_UF06(["→ UF-06"])

    click C_UF01 "#uf-01-akses-katalog-publik"
    click C_UF06 "#uf-06-rekalkulasi-harga-dan-pph-22"
```

Lanjutan: [UF-06 Rekalkulasi Cart](#uf-06-rekalkulasi-harga-dan-pph-22).

### UF-06 Rekalkulasi Harga dan PPh 22

```mermaid
flowchart TD
    A([Cart berubah]) --> B["Muat ulang produk, harga, dan stok aktif"]
    B --> C{"Semua item masih valid?"}
    C -->|Tidak| D["Tandai item bermasalah dan minta koreksi"]
    D --> C_UF05(["→ UF-05"])
    C -->|Ya| E{"Ada satu SKU dengan quantity minimal 5?"}
    E -->|Ya| F["Cart eligible harga PARTAI"]
    E -->|Tidak| G["Cart tidak eligible harga PARTAI"]
    F --> H["Evaluasi minimum GROSIR per produk"]
    G --> H
    H --> I["Pilih harga memakai rule baseline<br/>Provisional OPN-013"]
    I --> J["Hitung subtotal server-side"]
    J --> K["Kelompokkan nilai per klasifikasi PPh 22"]
    K --> L{"Ambang klasifikasi terlampaui?"}
    L -->|Tidak| M["PPh 22 = 0"]
    L -->|Ya| N["Hitung PPh 22 dengan basis provisional OPN-006"]
    M --> O["Tampilkan subtotal, PPh 22 terpisah, dan total"]
    N --> O
    O --> P{"Aksi pelanggan?"}
    P -->|Ubah cart| C_UF05
    P -->|Lanjut| C_UF07(["→ UF-07"])

    click C_UF05 "#uf-05-detail-produk-dan-cart"
    click C_UF07 "#uf-07-alamat-dan-pengiriman"
```

Kuantitas SKU berbeda tidak pernah dijumlahkan untuk memenuhi syarat lima unit.
Cakupan item yang memperoleh harga partai dan prioritas terhadap grosir tetap
[OPN-013](../requirements/BRD.md#opn-013). Dasar PPh 22 tetap
[OPN-006](../requirements/BRD.md#opn-006).

Lanjutan: [UF-07 Alamat dan Pengiriman](#uf-07-alamat-dan-pengiriman).

### UF-07 Alamat dan Pengiriman

```mermaid
flowchart TD
    A([Mulai pengiriman]) --> B["Pilih atau kelola alamat"]
    B --> B1["Cari area setelah pelanggan selesai mengetik"]
    B1 --> B2["Backend memanggil Biteship Maps"]
    B2 --> C{"Alamat lengkap dan area ID dikenali?"}
    C -->|Tidak| D["Minta pelanggan memilih atau memperbaiki area"]
    D --> B
    C -->|Ya| E{"Metode pengiriman?"}
    E -->|Kurir toko| F{"Tarif aktif tersedia untuk area?"}
    F -->|Tidak| G["Blokir metode kurir toko"]
    G --> E
    F -->|Ya| H["Pilih tarif dan ETA kurir toko"]
    E -->|Biteship| I["Validasi area origin/destination, daftar kurir,<br/>nama, nilai, kuantitas, dan berat setiap item"]
    I --> J{"Data quote lengkap?"}
    J -->|Tidak| K["Blokir quote dan catat data yang kurang"]
    K --> E
    J -->|Ya| L["Backend memanggil Biteship Rates;<br/>kirim dimensi bila tersedia"]
    L --> M{"Respons dan rate valid?"}
    M -->|Ya| N["Pilih kurir/layanan, durasi,<br/>mata uang, dan harga final"]
    M -->|Tidak| O{"Fallback sudah disetujui?"}
    O -->|Tidak| P["Hentikan checkout; ongkir tidak boleh Rp0"]
    P --> E
    O -->|Ya| H
    H --> Q["Simpan pilihan sementara"]
    N --> R["Simpan snapshot provider, kurir, layanan,<br/>area ID, price, hash request, dan waktu quote"]
    R --> Q
    Q --> C_UF08(["→ UF-08"])

    click C_UF08 "#uf-08-validasi-checkout"
```

Data operasional dan fallback mengikuti
[OPN-010](../requirements/BRD.md#opn-010),
[OPN-016](../requirements/BRD.md#opn-016), dan
[OPN-021](../requirements/BRD.md#opn-021).
Biteship di flow ini adalah external Maps/Rates provider; flow tidak membuat
order, pickup, label, atau tracking di Biteship.

Lanjutan: [UF-08 Validasi Checkout](#uf-08-validasi-checkout).

### UF-08 Validasi Checkout

```mermaid
flowchart TD
    A([Pelanggan konfirmasi checkout]) --> B["Kirim request dengan idempotency key"]
    B --> C{"Idempotency key sudah diproses?"}
    C -->|Ya| D["Kembalikan order yang sama"]
    D --> C_UF09(["→ UF-09"])
    C -->|Tidak| E{"Akun masih ACTIVE?"}
    E -->|Tidak| C_UF04(["→ UF-04"])
    E -->|Ya| F["Validasi ulang cart, produk, harga, dan stok"]
    F --> G{"Valid?"}
    G -->|Tidak| C_UF06(["→ UF-06"])
    G -->|Ya| H["Validasi alamat dan pilihan pengiriman"]
    H --> I{"Valid?"}
    I -->|Tidak| C_UF07(["→ UF-07"])
    I -->|Ya| J["Database transaction:<br/>buat DRAFT, item, charge, shipment snapshot"]
    J --> K["Buat external reference POS unik"]
    K --> C_UF09

    click C_UF04 "#uf-04-login-dan-routing-akses"
    click C_UF06 "#uf-06-rekalkulasi-harga-dan-pph-22"
    click C_UF07 "#uf-07-alamat-dan-pengiriman"
    click C_UF09 "#uf-09-sales-order-pos-dan-invoice"
```

Lanjutan: [UF-09 Sales Order POS](#uf-09-sales-order-pos-dan-invoice).

### UF-09 Sales Order POS dan Invoice

```mermaid
flowchart TD
    A([Draft order dan external reference tersedia]) --> B["Kirim CreateSalesOrder ke POS"]
    B --> C{"Hasil?"}
    C -->|Sukses| D["Simpan POS sales order dan invoice reference"]
    D --> E["Kurangi stok efektif dan tulis inventory ledger"]
    E --> F["Buat invoice dari snapshot toko, item, ongkir, dan PPh 22"]
    F --> G["Set order WAITING_PAYMENT dan expires_at D+1"]
    G --> C_UF10(["→ UF-10"])
    C -->|Timeout atau ambigu| H["Set operation RECONCILIATION_REQUIRED"]
    H --> I["Lookup dengan external reference"]
    I --> J{"Transaksi ditemukan?"}
    J -->|Ya| D
    J -->|Tidak| K["Retry terbatas atau review operasional<br/>tanpa membuat external reference baru"]
    K --> I
    C -->|Gagal definitif| L["Set operation FAILED; order tetap internal DRAFT"]
    L --> M["Tampilkan checkout belum berhasil"]
    M --> C_UF08(["→ UF-08"])

    click C_UF08 "#uf-08-validasi-checkout"
    click C_UF10 "#uf-10-pengajuan-pembayaran"
```

Kontrak operasi, payload, autentikasi, error, dan idempotency final mengikuti
[OPN-005](../requirements/BRD.md#opn-005). Invoice hanya aktif setelah respons
sukses atau hasil rekonsiliasi membuktikan transaksi POS sudah terbentuk.

Lanjutan: [UF-10 Pengajuan Pembayaran](#uf-10-pengajuan-pembayaran).

### UF-10 Pengajuan Pembayaran

```mermaid
flowchart TD
    A([Pelanggan membuka order]) --> B{"Status order?"}
    B -->|WAITING_PAYMENT| C["Tampilkan rekening dan instruksi transfer"]
    B -->|PAYMENT_REJECTED| C
    B -->|Status lain| D["Tolak upload bukti"]
    C --> E{"Order masih aktif pada tanggal bisnis?"}
    E -->|Tidak| C_UF13(["→ UF-13"])
    E -->|Ya| F["Pilih dan unggah bukti pembayaran"]
    F --> G{"Tipe, ukuran, dan ownership valid?"}
    G -->|Tidak| H["Tolak file tanpa mengaktifkannya"]
    H --> F
    G -->|Ya| I["Simpan object privat dan metadata bukti"]
    I --> J["Set payment SUBMITTED dan order PAYMENT_SUBMITTED"]
    J --> K["Catat audit event"]
    K --> C_UF11(["→ UF-11"])

    click C_UF11 "#uf-11-verifikasi-pembayaran"
    click C_UF13 "#uf-13-pembatalan-dan-retur-pos"
```

Lanjutan: [UF-11 Verifikasi Pembayaran](#uf-11-verifikasi-pembayaran) atau
[UF-13 Pembatalan](#uf-13-pembatalan-dan-retur-pos).

### UF-11 Verifikasi Pembayaran

```mermaid
flowchart TD
    A([Admin membuka pembayaran submitted]) --> B["Buka bukti melalui akses terotorisasi"]
    B --> C{"Keputusan admin?"}
    C -->|Tolak| D["Wajib isi alasan penolakan"]
    D --> E["Set payment REJECTED dan order PAYMENT_REJECTED"]
    E --> F["Simpan actor, waktu, dan audit"]
    F --> C_UF10(["→ UF-10"])
    C -->|Terima| G["Set payment VERIFIED"]
    G --> H["Simpan verifier, waktu, dan audit"]
    H --> I["Set order PAYMENT_VERIFIED lalu PROCESSING"]
    I --> C_UF12(["→ UF-12"])

    click C_UF10 "#uf-10-pengajuan-pembayaran"
    click C_UF12 "#uf-12-fulfillment-dan-penyelesaian"
```

Lanjutan: pelanggan mengunggah ulang melalui
[UF-10](#uf-10-pengajuan-pembayaran), atau order diteruskan ke
[UF-12 Fulfillment](#uf-12-fulfillment-dan-penyelesaian).

### UF-12 Fulfillment dan Penyelesaian

```mermaid
flowchart TD
    A([Order PROCESSING]) --> B["Admin menyiapkan pesanan"]
    B --> C["Set READY_FOR_DELIVERY"]
    C --> D{"Transisi valid?"}
    D -->|Tidak| E["Tolak perubahan dan catat audit"]
    E --> B
    D -->|Ya| F["Serahkan ke metode pengiriman terpilih"]
    F --> G["Isi tracking bila diwajibkan<br/>Provisional OPN-020"]
    G --> H["Set SHIPPED dan shipped_at"]
    H --> I["Konfirmasi penyelesaian"]
    I --> J["Set COMPLETED dan delivered_at"]
    J --> C_UF18(["→ UF-18"])

    click C_UF18 "#uf-18-laporan-dan-audit"
```

Lifecycle fulfillment dan nomor resi tetap
[OPN-020](../requirements/BRD.md#opn-020). Booking/pickup Biteship tidak
dilakukan website.

Lanjutan: [UF-18 Laporan](#uf-18-laporan-dan-audit).

### UF-13 Pembatalan dan Retur POS

```mermaid
flowchart TD
    A([Trigger pembatalan]) --> B{"Trigger?"}
    B -->|Admin| C{"Tanggal Asia/Jakarta masih sama?"}
    C -->|Tidak| D["Tolak pembatalan manual"]
    C -->|Ya| E{"Status WAITING_PAYMENT atau PAYMENT_SUBMITTED?"}
    E -->|Tidak| D
    E -->|Ya| F["Mulai proses cancel idempotent"]
    B -->|Scheduler D+1| G{"Status WAITING_PAYMENT dan order_date lebih lama?"}
    G -->|Tidak| H([Tidak ada tindakan])
    G -->|Ya| F
    F --> I{"POS sales order sudah ada?"}
    I -->|Tidak| J["Batalkan draft lokal"]
    I -->|Ya| K["Kirim CancelSalesOrder"]
    K --> L{"Hasil?"}
    L -->|Sukses| M["Simpan POS return reference"]
    M --> N["Tambah stok efektif dan tulis ledger CANCEL_RETURN"]
    N --> O["Set order CANCELLED dan catat audit"]
    J --> O
    L -->|Timeout atau ambigu| P["Set RECONCILIATION_REQUIRED"]
    P --> Q["Lookup retur/status sebelum retry"]
    Q --> R{"Retur ditemukan?"}
    R -->|Ya| M
    R -->|Tidak| Q
    L -->|Gagal definitif| S["Catat gagal; jangan set CANCELLED atau tambah stok"]
    O --> C_UF18(["→ UF-18"])

    click C_UF18 "#uf-18-laporan-dan-audit"
```

Auto-cancel baseline hanya menargetkan `WAITING_PAYMENT` dari hari kalender
sebelumnya. Refund setelah pembayaran terverifikasi berada di luar flow ini.

Lanjutan: [UF-18 Laporan](#uf-18-laporan-dan-audit).

## 4. Back-office dan Integration Flows

### UF-14 Sinkronisasi Master dan Harga POS

```mermaid
flowchart TD
    A([Scheduler sekali sehari atau trigger admin]) --> B{"Environment dan koneksi?"}
    B -->|Non-production, API belum tersedia| C["Muat seeder deterministik"]
    B -->|Production, API tidak tersedia| D["Gagal readiness; production tidak memakai seeder"]
    B -->|API tersedia| E["Panggil category, product, detail, dan price list"]
    C --> F["Validasi kontrak import internal"]
    E --> F
    F --> G{"Record valid?"}
    G -->|Tidak| H["Catat sync error; pertahankan data valid lama"]
    G -->|Ya| I["Upsert berdasarkan external ID dan SKU stabil"]
    I --> J["Gunakan field POS jika tersedia; pertahankan enrichment lokal"]
    J --> K{"Tiga jenis harga valid?"}
    K -->|Tidak| H
    K -->|Ya| L["Aktifkan master yang siap dijual"]
    L --> M["Produk yang hilang ditandai untuk rekonsiliasi, bukan dihapus"]
    H --> N["Set sync PARTIAL atau FAILED"]
    M --> O["Set sync SUCCEEDED atau PARTIAL"]
    O --> C_UF15(["→ UF-15"])

    click C_UF15 "#uf-15-sinkronisasi-dan-stok-efektif"
```

Akses POS dibuka setelah alur website berbasis data contoh berjalan; Kak Rio
adalah PIC. Go-live tetap memerlukan contract test POS.

Lanjutan: [UF-15 Inventory](#uf-15-sinkronisasi-dan-stok-efektif).

### UF-15 Sinkronisasi dan Stok Efektif

```mermaid
flowchart TD
    A([Trigger perubahan stok]) --> B{"Sumber?"}
    B -->|Full sync harian| C["Panggil seluruh inventory POS"]
    B -->|Pencocokan terarah| D["Panggil stock by product"]
    B -->|CreateSalesOrder sukses| E["Gunakan perubahan SALE dari UF-09"]
    B -->|CancelSalesOrder sukses| F["Gunakan perubahan CANCEL_RETURN dari UF-13"]
    C --> G["Validasi payload dan external ID"]
    D --> G
    E --> H["Lock snapshot produk"]
    F --> H
    G --> I{"Valid?"}
    I -->|Tidak| J["Catat sync error; jangan hapus snapshot lama"]
    I -->|Ya| H
    H --> K["Hitung quantity before, change, dan after"]
    K --> L["Update inventory snapshot secara atomik"]
    L --> M["Tulis inventory ledger dan source reference"]
    M --> N["Hitung TERSEDIA, MENIPIS, atau HABIS"]
    N --> O["Stok efektif tersedia untuk katalog dan checkout"]
    O --> C_UF05(["→ UF-05"])
    O --> C_UF08(["→ UF-08"])

    click C_UF05 "#uf-05-detail-produk-dan-cart"
    click C_UF08 "#uf-08-validasi-checkout"
```

Lanjutan: [UF-05 Produk dan Cart](#uf-05-detail-produk-dan-cart) dan
[UF-08 Checkout](#uf-08-validasi-checkout).

### UF-16 Enrichment dan Visibilitas Produk

```mermaid
flowchart TD
    A([Admin membuka katalog internal]) --> B["Cari atau filter produk sinkron"]
    B --> C["Buka detail dan status sumber field"]
    C --> D{"Aksi?"}
    D -->|Lengkapi konten| E["Isi deskripsi, SEO, slug, label, dan urutan"]
    D -->|Kelola media| F["Upload gambar atau video"]
    F --> G{"Tipe, ukuran, dan metadata valid?"}
    G -->|Tidak| H["Tolak upload"]
    H --> F
    G -->|Ya| I["Simpan object key dan metadata"]
    D -->|Ubah visibilitas| J["Set is_visible"]
    E --> K["Pastikan field POS yang tersedia tidak ditimpa"]
    I --> K
    J --> K
    K --> L["Simpan perubahan dan audit"]
    L --> C_UF01(["→ UF-01"])

    click C_UF01 "#uf-01-akses-katalog-publik"
```

Lanjutan: perubahan tampil pada
[UF-01 Katalog Publik](#uf-01-akses-katalog-publik).

### UF-17 Konfigurasi Operasional

```mermaid
flowchart TD
    A([Admin membuka konfigurasi]) --> B{"Jenis konfigurasi?"}
    B -->|PPh 22| C["Pilih klasifikasi, ambang, tarif termasuk 0 persen"]
    C --> D["Isi basis perhitungan<br/>Provisional OPN-006"]
    B -->|Stok minimum| E["Isi low-stock threshold per produk"]
    B -->|Kurir toko| F["Isi area, tarif, ETA, dan status aktif"]
    B -->|Identitas toko| G["Isi nama, alamat, kontak, dan NPWP"]
    B -->|Rekening transfer| H["Isi bank, rekening, pemilik, dan instruksi"]
    D --> I{"Validasi lulus?"}
    E --> I
    F --> I
    G --> I
    H --> I
    I -->|Tidak| J["Tampilkan error; jangan aktifkan perubahan"]
    J --> B
    I -->|Ya| K["Simpan versi/configuration aktif"]
    K --> L["Catat actor, old/new value, dan waktu"]
    L --> M{"Dampak?"}
    M -->|Pricing| C_UF06(["→ UF-06"])
    M -->|Inventory| C_UF15(["→ UF-15"])
    M -->|Shipping| C_UF07(["→ UF-07"])
    M -->|Invoice| C_UF09(["→ UF-09"])
    M -->|Payment| C_UF10(["→ UF-10"])

    click C_UF06 "#uf-06-rekalkulasi-harga-dan-pph-22"
    click C_UF07 "#uf-07-alamat-dan-pengiriman"
    click C_UF09 "#uf-09-sales-order-pos-dan-invoice"
    click C_UF10 "#uf-10-pengajuan-pembayaran"
    click C_UF15 "#uf-15-sinkronisasi-dan-stok-efektif"
```

Perubahan konfigurasi tidak mengubah snapshot order atau invoice lama.

### UF-18 Laporan dan Audit

```mermaid
flowchart TD
    A([Admin membuka laporan]) --> B["Pilih periode Asia/Jakarta"]
    B --> C["Opsional: filter status, pelanggan, area, metode kirim"]
    C --> D["Query order dan shipment snapshot"]
    D --> E["Keluarkan order CANCELLED dari omzet"]
    E --> F["Tampilkan jumlah transaksi, omzet, dan PPh 22 terpisah"]
    F --> G{"Ekspor diminta?"}
    G -->|Tidak| H["Tampilkan hasil terpaginated"]
    G -->|Ya, format disetujui| I["Queue export dan berikan hasil terotorisasi"]
    G -->|Format belum disetujui| J["Tetap gunakan tampilan layar"]
    A --> K{"Audit trail dibutuhkan?"}
    K -->|Ya| L["Filter actor, action, entity, request ID, dan periode"]
    L --> M["Tampilkan old/new value yang sudah teredaksi"]
    K -->|Tidak| B
    H --> N([Selesai])
    I --> N
    J --> N
    M --> N
```

Format ekspor CSV/XLSX tetap mengikuti keputusan final. Query berat dijalankan
melalui queue dan tidak boleh memblokir transaksi.

## 5. Traceability

| User Flow | Requirement Utama |
|---|---|
| UF-01 - UF-04 | FR-AUTH-001 - FR-AUTH-011; FR-CAT-007 - FR-CAT-008 |
| UF-05 - UF-06 | FR-CAT-001 - FR-CAT-009; FR-PRC-001 - FR-PRC-007; FR-CART-001 - FR-CART-003 |
| UF-07 | FR-CART-005; FR-SHP-001 - FR-SHP-007 |
| UF-08 | FR-CART-004 - FR-CART-007; SRS Bagian 10 |
| UF-09 | FR-POS-017, FR-POS-019 - FR-POS-020; FR-ORD-001 - FR-ORD-002 |
| UF-10 - UF-11 | FR-PAY-001 - FR-PAY-007; FR-ORD-003 |
| UF-12 | FR-ORD-004 - FR-ORD-005; FR-SHP-004 |
| UF-13 | FR-ORD-006 - FR-ORD-010; FR-POS-012, FR-POS-018 - FR-POS-020 |
| UF-14 | FR-POS-001 - FR-POS-006; FR-POS-013 - FR-POS-016 |
| UF-15 | FR-POS-003 - FR-POS-012, FR-POS-017 - FR-POS-019 |
| UF-16 | FR-CAT-002 - FR-CAT-006 |
| UF-17 | FR-PRC-003 - FR-PRC-004; FR-POS-008; FR-SHP-001; FR-PAY-001; FR-ORD-002 |
| UF-18 | FR-RPT-001 - FR-RPT-006; FR-AUD-001 - FR-AUD-004 |

## 6. Open Decisions yang Membatasi Flow

| Referensi | Dampak pada Flow |
|---|---|
| [OPN-005](../requirements/BRD.md#opn-005) | Nama operasi, payload, autentikasi, error, dan idempotency UF-09, UF-13, UF-14, UF-15. |
| [OPN-006](../requirements/BRD.md#opn-006) | Basis dan agregasi PPh 22 pada UF-06 dan UF-17. |
| [OPN-010](../requirements/BRD.md#opn-010) | Area, tarif, dan ETA kurir toko pada UF-07/UF-17. |
| [OPN-013](../requirements/BRD.md#opn-013) | Cakupan harga partai dan prioritas terhadap grosir pada UF-06. |
| [OPN-016](../requirements/BRD.md#opn-016) | Perilaku fallback ketika quote Biteship gagal pada UF-07. |
| [OPN-020](../requirements/BRD.md#opn-020) | Lifecycle fulfillment dan tracking pada UF-12. |
| [OPN-021](../requirements/BRD.md#opn-021) | Origin, sumber/default berat, penggunaan dimensi, daftar kurir, mode area ID/koordinat, akun production, dan biaya provider pada UF-07. |
| [OPN-022](../requirements/BRD.md#opn-022) | Sumber identitas toko, nomor/ownership, PDF, dan channel invoice pada UF-09. |
