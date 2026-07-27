# Repository Agent Instructions

Instruksi ini berlaku untuk seluruh repository.

## Always-Active Tooling

RTK, Graphify, dan Ponytail `ultra` wajib aktif pada setiap pekerjaan agent.
Jangan melanjutkan secara diam-diam tanpa salah satunya.

### 1. Rust Token Killer (RTK)

- Awali setiap perintah terminal dengan `rtk`.
- Gunakan `rtk proxy <command>` hanya ketika filtering RTK menghilangkan output
  yang memang dibutuhkan.
- Tool native non-shell seperti `apply_patch` tidak memerlukan prefix RTK.
- Periksa ketersediaan dengan `rtk --version` sebelum pekerjaan terminal
  pertama.

### 2. Graphify

- Gunakan Graphify untuk memahami codebase, arsitektur, relasi file, dampak
  perubahan, dan isi dokumentasi.
- Jika `graphify-out/graph.json` tersedia, lakukan
  `rtk graphify query "<pertanyaan>"` sebelum membaca repository secara luas.
- Rebuild/update graph hanya jika graph belum tersedia, sudah stale, atau
  pengguna meminta rebuild.
- Setelah perubahan material pada code atau dokumentasi, perbarui graph sesuai
  workflow Graphify yang tersedia.

### 3. Ponytail Ultra

- Muat dan terapkan skill Ponytail pada level `ultra` (`/ponytail ultra`) untuk
  seluruh pekerjaan coding, refactor, fix, review, dan pemilihan dependency.
- Terapkan urutan: tidak perlu dibuat -> reuse -> standard library -> fitur
  native -> dependency yang sudah ada -> implementasi minimum.
- Jangan menambah abstraction, dependency, boilerplate, atau fleksibilitas
  spekulatif.
- Jangan menyederhanakan security, validation, data integrity, accessibility,
  error handling yang mencegah kehilangan data, atau requirement eksplisit.

## Bootstrap Wajib

Pada awal task, cek ketiga capability:

1. `rtk --version`
2. `rtk graphify --help`
3. skill Ponytail tersedia dan dapat dibaca

Jika capability belum tersedia:

- **RTK:** pasang dari distribusi RTK resmi yang dikonfigurasi pada environment.
- **Graphify:** gunakan installer resmi; prioritaskan
  `uv tool install --upgrade graphifyy`, lalu fallback installer resmi yang
  didukung environment.
- **Ponytail:** gunakan skill installer/registry resmi environment untuk
  memasang skill `ponytail`.
- Ikuti approval, sandbox, dan network policy host saat instalasi.
- Verifikasi kembali capability setelah instalasi.
- Jika sumber resmi atau izin instalasi tidak tersedia, hentikan pekerjaan dan
  laporkan blocker. Jangan mengganti tool dengan implementasi buatan sendiri.

## Urutan Kerja

1. Jalankan bootstrap capability.
2. Gunakan Graphify untuk memperoleh konteks dan impact.
3. Terapkan Ponytail `ultra` untuk memilih perubahan terkecil yang benar.
4. Jalankan seluruh pemeriksaan terminal melalui RTK.
5. Validasi hasil secara proporsional sebelum handoff.

## Kebijakan Branch dan Push

Gunakan alur promosi `dev` -> `staging` -> `main`. Jangan melewati tingkat
promosi.

### `dev` — otomatis

- Setelah setiap iterasi selesai dan validasi relevan lulus, commit perubahan
  iterasi tersebut lalu push otomatis ke branch `dev` tanpa menunggu prompt.
- Commit hanya file yang termasuk scope iterasi. Jangan memasukkan perubahan
  milik pengguna atau pekerjaan lain yang tidak terkait.
- Jika validasi gagal, terjadi konflik, atau akses remote tidak tersedia,
  jangan push; laporkan blocker dan pertahankan perubahan lokal.

### `staging` — semi-otomatis

- Push ke `staging` hanya ketika satu fitur sudah stabil: scope fitur selesai,
  acceptance criteria terpenuhi, dan pemeriksaan relevan lulus.
- Promosi dapat dipicu agent secara mandiri ketika seluruh syarat stabil
  terbukti, atau menunggu instruksi pengguna jika kesiapan fitur masih
  mengandung keputusan bisnis maupun risiko yang belum jelas.
- Jangan mempromosikan pekerjaan parsial atau eksperimen dari `dev`.

### `main` — manual dan production-ready

- Push atau merge ke `main` hanya jika pengguna memberikan prompt eksplisit
  untuk tindakan tersebut.
- Dilarang melakukan auto-push, auto-merge, atau menganggap kata "selesai",
  "stabil", maupun "final" sebagai izin implisit untuk menyentuh `main`.
- Sebelum promosi, pastikan seluruh scope final, acceptance criteria terpenuhi,
  validasi production lulus, dan perubahan siap dirilis.

### Pengaman Git

- Jangan force-push dan jangan melewati branch protection.
- Periksa branch aktif, diff, dan status validasi sebelum setiap commit/push.
- Jika branch tujuan belum tersedia, buat branch hanya dari baseline yang sudah
  disepakati; jika baseline tidak jelas, hentikan promosi dan minta arahan.
