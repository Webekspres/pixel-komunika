# DESIGN.md — Color Tokens & Font

Working guideline internal untuk UI Pixel Komunika.

Sumber: [`public/assets/brand-logo.png`](public/assets/brand-logo.png).  
Nilai hex di bawah **derived dari logo** (sampling raster); ganti jika klien mengunci brand book resmi.

## Product context

Katalog yang dijual (konteks desain/copy; master production tetap dari POS):

1. **Aksesoris elektronik** — power bank, charger/kabel data, handsfree/headphone, flashdisk, speaker, keyboard, mouse, CCTV, lampu, dan sejenisnya.
2. **Kartu data & voucher internet** — operator seperti Smartfren, Indosat, Axis, XL, Tri, Telkomsel, by.U, dll.
3. **Pulsa**.

Aplikasi baru tetap **e-commerce pelanggan terverifikasi** (harga partai/grosir, approval admin) — bukan toko publik terbuka.

## UI direction

- Vibe **ceria, retail komunikasi/gadget**: hero kuning + maskot, kontras hitam/putih.
- **Beranda (`/`)** — bukan landing marketing one-pager: sticky header, hero brand-first, ikhtisar kategori, highlight katalog (harga tersamar sampai akun disetujui), keunggulan, footer. CTA utama **Daftar / Masuk** (akun terverifikasi), bukan “Shopping” publik.
- **Kuning** = brand primary (CTA utama, highlight). **Merah** = accent sekunder (nav aktif, CTA sekunder) — selaras site pemasaran lama, tanpa mengganti primary kuning.
- Hindari: look corporate dingin, purple gradient AI-default, dashboard-first di permukaan branding.
- Placeholder foto di `public/assets/placeholders/` (dari Unsplash) bersifat sementara; diganti aset produk/klien.

## Frontend toolkit

Keputusan kurasi stack UI (selaras SRS §4.1). Jangan install tier C sampai fitur butuh.

### A — Baseline (sudah / native)

Laravel 13, Blade, Livewire 4, Alpine.js, Tailwind CSS 4, Vite, `wire:navigate` / `wire:loading`, CSS + Tailwind transition, WebP + `loading="lazy"` + `srcset` Blade, Cloudflare CDN, Pest, Pint.

### B — Ambil saat storefront / Identity

| Item | Catatan |
| --- | --- |
| Lucide Icons | Nav, kategori, keunggulan, placeholder cart/user |
| Flux UI | Hanya untuk app/internal UI (auth, account, admin), bukan fondasi storefront publik |
| Sticky header | CSS `position: sticky` |
| Toast (flash + Alpine/Livewire) | Auth, cart, approval — tanpa lib toast berat |
| Drawer cart | Saat fitur cart |
| Skeleton loading | `wire:loading` + util Tailwind |
| Floating search | Saat search katalog |
| Spatie Permission / Activitylog | Identity & audit |
| Laravel Debugbar | Dev only |

### C — Nanti (trigger konkret)

Splide (carousel), Fancybox (galeri), GSAP (hero/scroll yang CSS tidak cukup), Spatie Media Library / Sitemap / Backup, GA + Clarity, PHPStan.

### D — Skip

Lenis, critical-CSS toolchain terpisah, optimistic UI luas pada harga/stok, page-transition library, scroll progress indicator.

Catatan arsitektur jangka panjang frontend: lihat
[`docs/design/FRONTEND_UI_ARCHITECTURE.md`](docs/design/FRONTEND_UI_ARCHITECTURE.md).

## Color tokens

| Token | Hex | Peran |
| --- | --- | --- |
| `brand-yellow` | `#F8B818` | Primary accent: CTA utama, highlight, underline |
| `brand-yellow-soft` | `#F8D820` | Hover/soft fill kuning |
| `brand-black` | `#181818` | Text utama, outline, headset/wordmark |
| `brand-white` | `#FFFFFF` | Surface / background utama |
| `brand-red` | `#F81818` | Accent sekunder (nav aktif, CTA sekunder) + error |

### Neutrals (UI)

| Token | Hex | Peran |
| --- | --- | --- |
| `gray-50` | `#FAFAFA` | Background sekunder |
| `gray-100` | `#F0F0F0` | Surface muted |
| `gray-200` | `#E5E5E5` | Border default |
| `gray-400` | `#A3A3A3` | Placeholder |
| `gray-500` | `#737373` | Text secondary |
| `gray-700` | `#404040` | Text emphatic non-black |

### Semantic

| Token | Hex | Peran |
| --- | --- | --- |
| `success` | `#15803D` | Sukses / status positif |
| `warning` | `#F8B818` | Warning — text di atasnya harus `brand-black` |
| `danger` | `#F81818` | Error / destruktif |

Kontras: text di atas kuning CTA memakai `brand-black`, bukan putih.

## Font

**Plus Jakarta Sans** ([Google Fonts](https://fonts.google.com/specimen/Plus+Jakarta+Sans))

| Penggunaan | Weight |
| --- | --- |
| Body / paragraf | 400 Regular |
| Label / UI sekunder | 500 Medium |
| Heading / CTA | 600 SemiBold atau 700 Bold |

## Tailwind `@theme`

```css
@theme {
    --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

    --color-brand-yellow: #f8b818;
    --color-brand-yellow-soft: #f8d820;
    --color-brand-black: #181818;
    --color-brand-white: #ffffff;
    --color-brand-red: #f81818;

    --color-success: #15803d;
    --color-warning: #f8b818;
    --color-danger: #f81818;
}
```

## Open

- Hex resmi dari brand book klien
- Self-host font vs CDN (shared hosting: prefer self-host via Vite/`@fonts`)
