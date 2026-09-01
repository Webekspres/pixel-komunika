# Pixel Komunika

Website e-commerce untuk **pelanggan terverifikasi**, dikembangkan oleh
**PT Webekspres Teknologi Indonesia** untuk **Pixel Komunika** (perwakilan
klien: Sylvi).

Sistem mengelola katalog, harga partai/grosir, stok, order, invoice, pembayaran
transfer manual, pengiriman, PPh 22, dan laporan. Website menjadi source of
truth transaksi dan invoice; data produk/stok production bersumber dari POS;
estimasi ongkir memakai Biteship Maps + Rates. Production baseline: **shared
hosting** milik klien.

## Stack

- PHP 8.3+ / Laravel 13
- Blade + Livewire + Tailwind CSS + Vite
- MySQL / MariaDB
- Pest
- Queue, cache, dan session berbasis database/file (tanpa Redis di baseline)

## Branch

| Branch | Peran |
| --- | --- |
| `main` | Landing repo / production-ready. Saat ini berisi README saja sampai rilis production pertama. |
| `dev` | Development dan eksperimen — **checkout branch ini untuk bekerja**. |
| `staging` | Promosi fitur yang sudah stabil (hanya saat diminta). |

Alur promosi: `dev` → `staging` → `main`. Jangan melewati tingkat.

## Development setup

Kerja selalu dari branch `dev`:

```bash
git clone https://github.com/Webekspres/pixel-komunika.git
cd pixel-komunika
git checkout dev
```

Prasyarat: PHP 8.3+, Composer, Node.js, MySQL dengan database
`pixelkomunika_db`.

```bash
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan DB_* di .env jika perlu (default: mysql / pixelkomunika_db / root)
php artisan migrate
npm install
npm run build
composer run dev
```

`composer run dev` menjalankan server, queue listener, dan Vite. Alternatif:
`php artisan serve` + `npm run dev` di terminal terpisah.

Health check: `GET /health`

## Dokumentasi

- **Handover proyek (status, gap, operasional):** [docs/HANDOVER.md](docs/HANDOVER.md)
- Brand UI (produk, color tokens & font): [DESIGN.md](DESIGN.md)
- Requirement, design sistem, dan deliverable klien di branch `dev`:
  - Di lokal setelah `git checkout dev`: buka `docs/README.md`
  - Di GitHub:
    [docs pada branch dev](https://github.com/Webekspres/pixel-komunika/tree/dev/docs)

## Catatan

- Kredensial production tidak disimpan di repository.
- Data contoh hanya untuk development, staging, demo, dan UAT — bukan sumber
  produk/stok production.
