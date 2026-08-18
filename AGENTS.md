# Repository Agent Instructions

Instruksi ini berlaku untuk seluruh repository.

## Always-Active Tooling

Ponytail `ultra` wajib aktif pada setiap pekerjaan agent.
Jangan melanjutkan secara diam-diam tanpa skill ini.

### 1. Ponytail Ultra

- Muat dan terapkan skill Ponytail pada level `ultra` (`/ponytail ultra`) untuk
  seluruh pekerjaan coding, refactor, fix, review, dan pemilihan dependency.
- Terapkan urutan: tidak perlu dibuat -> reuse -> standard library -> fitur
  native -> dependency yang sudah ada -> implementasi minimum.
- Jangan menambah abstraction, dependency, boilerplate, atau fleksibilitas
  spekulatif.
- Jangan menyederhanakan security, validation, data integrity, accessibility,
  error handling yang mencegah kehilangan data, atau requirement eksplisit.

## Bootstrap Wajib

Pada awal task, cek capability berikut:

1. skill Ponytail tersedia dan dapat dibaca

Jika capability belum tersedia:

- **Ponytail:** gunakan skill installer/registry resmi environment untuk
  memasang skill `ponytail`.
- Ikuti approval, sandbox, dan network policy host saat instalasi.
- Verifikasi kembali capability setelah instalasi.
- Jika sumber resmi atau izin instalasi tidak tersedia, hentikan pekerjaan dan
  laporkan blocker. Jangan mengganti tool dengan implementasi buatan sendiri.

## Urutan Kerja

1. Jalankan bootstrap capability.
2. Terapkan Ponytail `ultra` untuk memilih perubahan terkecil yang benar.
3. Validasi hasil secara proporsional sebelum handoff.

## Sprint Tasks

- Rincian task per sprint disimpan di `docs/sprints/` (contoh:
  `docs/sprints/SPRINT_3.md`).
- Sebelum mengerjakan task sprint, baca file sprint terkait dan verifikasi
  relevansinya terhadap kode aktual.
- File sprint bisa berasal dari sumber eksternal (misal screenshot ClickUp yang
  diubah ke markdown oleh AI), sehingga detail teknis — nama tabel/kolom,
  status, referensi dokumen, alamat/URL, dan istilah domain — wajib dicek ulang
  ke `docs/requirements/`, `docs/design/`, dan kode sebelum implementasi.
- Dokumen requirements (`BRD.md`, `FRD.md`, `SRS.md`, dll.) tetap menjadi
  sumber kebenaran; file sprint hanyalah penjabaran task.
- Jika ditemukan ketidaksesuaian antara sprint file dan kode/requirements,
  laporkan di hasil kerja dan jangan implementasikan langsung tanpa konfirmasi.

## Kebijakan Branch dan Push

Gunakan alur promosi `dev` -> `staging` -> `main`. Jangan melewati tingkat
promosi.

### `dev` — manual

- Jangan commit atau push ke `dev` secara otomatis.
- Commit dan/atau push ke `dev` hanya jika pengguna meminta secara eksplisit.
- Saat diminta: commit hanya file yang termasuk scope iterasi; jangan
  memasukkan perubahan milik pengguna atau pekerjaan lain yang tidak terkait.
- Jika validasi gagal, terjadi konflik, atau akses remote tidak tersedia,
  jangan push; laporkan blocker dan pertahankan perubahan lokal.

### `staging` — manual

- Jangan commit atau push ke `staging` secara otomatis maupun semi-otomatis.
- Push/promosi ke `staging` hanya jika pengguna meminta secara eksplisit.
- Saat diminta: pastikan satu fitur sudah stabil (scope selesai, acceptance
  criteria terpenuhi, pemeriksaan relevan lulus) sebelum mempromosikan.
- Jangan mempromosikan pekerjaan parsial atau eksperimen dari `dev`.

### `main` — manual dan production-ready

- Push atau merge ke `main` hanya jika pengguna memberikan prompt eksplisit
  untuk tindakan tersebut.
- Dilarang melakukan auto-push, auto-merge, atau menganggap kata "selesai",
  "stabil", maupun "final" sebagai izin implisit untuk menyentuh `main`.
- Sampai rilis production pertama, `main` boleh berisi landing `README.md`
  saja; aplikasi penuh tetap di `dev`/`staging`. Mengganti isi `main` ke
  artifact production tetap memerlukan prompt eksplisit.
- Sebelum promosi production, pastikan seluruh scope final, acceptance
  criteria terpenuhi, validasi production lulus, dan perubahan siap dirilis.

### Pengaman Git

- Jangan force-push dan jangan melewati branch protection.
- Periksa branch aktif, diff, dan status validasi sebelum setiap commit/push.
- Jika branch tujuan belum tersedia, buat branch hanya dari baseline yang sudah
  disepakati; jika baseline tidak jelas, hentikan promosi dan minta arahan.

## Format Commit

Setiap commit memakai Conventional Commits:

```text
<type>(<scope opsional>): <ringkasan singkat>
```

Contoh:

```text
feat(cart): hitung PPh 22 sebagai komponen terpisah
fix(auth): tolak checkout untuk pelanggan pending
```

### Type yang dipakai

| Type | Dipakai untuk |
| --- | --- |
| `feat` | Fitur baru yang menambah kemampuan produk |
| `fix` | Perbaikan bug |
| `refactor` | Ubah struktur kode tanpa mengubah perilaku |
| `remove` | Hapus fitur, modul, file, atau dependency yang tidak dipakai |
| `docs` | Dokumentasi saja (README, BRD/FRD/SRS, komentar panduan) |
| `test` | Tambah atau perbaiki test |
| `chore` | Kerja rutin yang tidak mengubah logika produk (tooling, ignore, lockfile) |
| `style` | Format/lint whitespace tanpa ubah perilaku |
| `perf` | Peningkatan performa |
| `build` | Build system, Composer/NPM dependency yang memengaruhi build |
| `ci` | Pipeline CI/CD |
| `revert` | Membatalkan commit sebelumnya |

### Aturan penulisan

1. `type` wajib; `scope` opsional (satu kata: `auth`, `cart`, `order`, `pos`,
   `shipping`, `admin`, `docs`, `agents`, dll.).
2. Ringkasan dalam bahasa Inggris atau Indonesia yang konsisten dalam satu
   commit; imperatif/deskriptif singkat; huruf kecil setelah colon; tanpa titik
   di akhir; maksimal sekitar 72 karakter.
3. Fokus pada **mengapa / dampak**, bukan daftar file.
4. Satu commit = satu tujuan. Jangan campur `feat` dan `fix` tanpa alasan.
5. Breaking change: tambahkan `!` setelah type/scope
   (`feat(api)!: ubah kontrak laporan POS`) dan/atau footer
   `BREAKING CHANGE: ...`.
6. Body opsional untuk konteks; footer opsional untuk issue
   (`Refs: #12`, `Closes: #34`).
7. Jangan commit secret (`.env`, kredensial, key production).

### Contoh singkat

```text
feat(catalog): tampilkan harga hanya untuk pelanggan aktif
fix(payment): tolak upload bukti selain gambar/pdf
refactor(order): ekstrak status transition ke domain service
remove(admin): hapus endpoint sinkronisasi manual sementara
docs(agents): standarisasi format commit conventional
chore(dev): hilangkan pail dari composer run di Windows
test(health): pastikan /health mengecek koneksi database
```
