# DESIGN.md — Color Tokens & Font

Working guideline internal untuk UI Pixel Komunika.

Sumber: [`public/assets/brand-logo.png`](public/assets/brand-logo.png).  
Nilai hex di bawah **derived dari logo** (sampling raster); ganti jika klien mengunci brand book resmi.

Belum diterapkan ke CSS — lihat cuplikan `@theme` di bawah untuk sprint UI.

## Color tokens

| Token | Hex | Peran |
| --- | --- | --- |
| `brand-yellow` | `#F8B818` | Primary accent: CTA, highlight, underline, icon cart |
| `brand-yellow-soft` | `#F8D820` | Hover/soft fill kuning (dari highlight logo) |
| `brand-black` | `#181818` | Text utama, outline, headset/wordmark |
| `brand-white` | `#FFFFFF` | Surface / background utama |
| `brand-red` | `#F81818` | Accent jarang (error kuat / detail brand); jangan jadi primary |

### Neutrals (UI)

| Token | Hex | Peran |
| --- | --- | --- |
| `gray-50` | `#FAFAFA` | Background sekunder |
| `gray-100` | `#F0F0F0` | Surface muted (ada di edge logo) |
| `gray-200` | `#E5E5E5` | Border default |
| `gray-400` | `#A3A3A3` | Placeholder |
| `gray-500` | `#737373` | Text secondary |
| `gray-700` | `#404040` | Text emphatic non-black |

### Semantic

| Token | Hex | Peran |
| --- | --- | --- |
| `success` | `#15803D` | Sukses / status positif |
| `warning` | `#F8B818` | Warning — pakai `brand-yellow`; text di atasnya harus `brand-black` |
| `danger` | `#F81818` | Error / destruktif — sama `brand-red` |

Kontras: text utama di atas kuning CTA memakai `brand-black`, bukan putih.

## Font

Satu family untuk display dan body:

**Plus Jakarta Sans** ([Google Fonts](https://fonts.google.com/specimen/Plus+Jakarta+Sans))

| Penggunaan | Weight |
| --- | --- |
| Body / paragraf | 400 Regular |
| Label / UI sekunder | 500 Medium |
| Heading / CTA | 600 SemiBold atau 700 Bold |

Alasan: rounded-modern dekat vibe wordmark “Pixel”, readable untuk UI ID/EN, mudah di Tailwind/Vite, menggantikan default Laravel `Instrument Sans` saat tema brand diaktifkan.

## Cuplikan Tailwind `@theme` (belum di-wire)

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
