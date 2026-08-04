# Pixel Komunika

Website e-commerce pelanggan terverifikasi — PT Webekspres untuk Pixel Komunika.

## Stack

Laravel 13 · Blade · Livewire · Tailwind CSS · Vite · Pest · MySQL/SQLite

Production baseline: shared hosting (queue/cache/session via database/file).

## Docs

Requirement dan design: [docs/README.md](docs/README.md)

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
php artisan serve
```

Health check: `GET /health`
