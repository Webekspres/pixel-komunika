# DESIGN.md — Pixel Komunika Storefront Design System

Working guideline internal untuk UI/UX Pixel Komunika.

This document defines the visual direction, layout principles, responsive behavior, typography, color system, storefront architecture, and UI rules for the Pixel Komunika ecommerce platform.

---

# 1. Product Context

Pixel Komunika adalah B2B ecommerce untuk pelanggan terverifikasi.

Produk yang dijual mencakup:

- Aksesoris elektronik
  - Power bank
  - Charger
  - Kabel data
  - Handsfree / headphone
  - Flashdisk
  - Speaker
  - Keyboard
  - Mouse
  - CCTV
  - Lampu
  - Dan produk sejenis

- Kartu data dan voucher internet
  - Smartfren
  - Indosat
  - Axis
  - XL
  - Tri
  - Telkomsel
  - by.U
  - Dan operator lainnya

- Pulsa

Business model:

- Customer melakukan registrasi.
- Customer membutuhkan approval / verifikasi admin.
- Customer yang sudah terverifikasi dapat mengakses harga grosir / partai.
- Master production tetap berasal dari POS.
- Website berfungsi sebagai ecommerce untuk pelanggan terverifikasi.

Pixel Komunika bukan marketplace publik seperti Tokopedia atau Shopee.

---

# 2. Primary Design Direction

## Core Direction

Pixel Komunika harus terasa seperti:

> A premium, modern, full-width ecommerce storefront with a playful communication/gadget brand identity.

Visual reference:

- Modern Shopify storefronts
- Premium ecommerce websites
- Contemporary DTC ecommerce
- Clean retail websites

Do NOT copy the visual identity of Shopify.

Shopify is used only as a reference for:

- Fluid layouts
- Full-width sections
- Ecommerce UX
- Product discovery
- Product grids
- Cart drawer
- Responsive behavior
- Clean navigation

---

# 3. Brand Personality

The Pixel Komunika storefront should feel:

- Playful
- Cheerful
- Modern
- Friendly
- Premium
- Trustworthy
- Retail-oriented
- Technology-oriented

The website should retain the existing Pixel Komunika personality, including its cartoon / mascot visual identity.

The mascot is a brand asset, not merely decoration.

Use the mascot intentionally in:

- Hero sections
- Empty states
- Authentication
- Promotional sections
- Registration CTA
- Error / unavailable states
- Customer communication moments

Avoid turning the entire website into a cartoon interface.

The overall UI should remain mature and premium.

---

# 4. Full-Width Layout Philosophy

This is a critical design requirement.

## DO NOT build the storefront around narrow centered containers.

Avoid patterns such as:

```text
max-w-6xl
max-w-7xl
max-w-screen-xl
```

as the primary layout constraint for the storefront.

On large desktop screens, the storefront should be able to use the available viewport width.

The experience should feel similar to modern Shopify storefronts:

```text
┌───────────────────────────────────────────────────────────────┐
│                         HEADER                                │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│                       FULL WIDTH HERO                         │
│                                                               │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│                    PRODUCT / CATEGORY AREA                    │
│                                                               │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│                         PROMOTION                             │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

Not:

```text
┌───────────────────────────────────────────────────────────────┐
│                                                               │
│             ┌──────────────────────────────┐                  │
│             │                              │                  │
│             │       Narrow content         │                  │
│             │                              │                  │
│             └──────────────────────────────┘                  │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

---

# 5. Fluid Container System

Storefront sections should generally use:

```css
width: 100%;
```

with responsive horizontal padding.

Recommended approach:

```text
Mobile:
px-4

Small:
px-5

Tablet:
px-6

Desktop:
px-8

Large Desktop:
px-10

Very Large Desktop:
px-12
```

The content should expand naturally with the viewport.

Do not artificially stop the content at 1200px / 1280px / 1440px unless there is a strong UX reason.

---

# 6. Full-Bleed Sections

Sections may use different background colors while remaining full width.

Example:

```text
────────────────────────────────────────────────────────────

                    YELLOW HERO

────────────────────────────────────────────────────────────

                    WHITE SECTION

────────────────────────────────────────────────────────────

                    GRAY SECTION

────────────────────────────────────────────────────────────

                    BLACK CTA

────────────────────────────────────────────────────────────
```

The background must extend from edge to edge.

Only the content inside the section receives horizontal padding.

---

# 7. Large Desktop Behavior

The website must take advantage of large screens.

On a 1920px or larger viewport:

DO:

- Expand product grids.
- Increase available image area.
- Allow hero artwork to become larger.
- Increase whitespace naturally.
- Allow sections to breathe.
- Use wider search bars.
- Use wider navigation spacing.

DO NOT:

- Keep everything locked to a narrow 1200px container.
- Leave huge unused whitespace on both sides.
- Make the website feel like a mobile layout centered on a desktop screen.

The storefront should feel intentionally designed for large displays.

---

# 8. Responsive Philosophy

Responsive design is mandatory.

The design must be fluid across:

- Large desktop
- Desktop
- Laptop
- Tablet
- Mobile

Do not simply shrink the desktop layout.

Each breakpoint should adapt based on usability.

---

# 9. Mobile-First Behavior

On mobile:

- Reduce horizontal padding.
- Collapse navigation.
- Use hamburger / mobile menu.
- Make search easily accessible.
- Convert filter sidebar into drawer / bottom sheet.
- Convert multi-column layouts into 1–2 columns where appropriate.
- Use touch-friendly controls.
- Keep CTA buttons accessible.
- Avoid tiny typography.
- Avoid horizontal scrolling.

The mobile experience should feel like a deliberate ecommerce experience, not a desktop website compressed into a phone.

---

# 10. Storefront Header

The storefront header should be full width.

Suggested structure:

```text
Announcement Bar
        ↓
Main Navigation
        ↓
Optional Category Navigation
```

Desktop:

```text
┌───────────────────────────────────────────────────────────────┐
│ Announcement                                                   │
├───────────────────────────────────────────────────────────────┤
│ LOGO   Kategori   Search                     Cart   Account   │
├───────────────────────────────────────────────────────────────┤
│ Power Bank | Charger | Kabel | Audio | Pulsa | Voucher | ... │
└───────────────────────────────────────────────────────────────┘
```

The header should be sticky.

When scrolling:

- Reduce unnecessary vertical height.
- Preserve access to search.
- Preserve cart.
- Preserve account access.

Avoid an oversized dashboard-like navigation.

---

# 11. Navigation

Public storefront navigation should prioritize product discovery.

Primary navigation:

- Logo
- Kategori
- Search
- Promo (only when available)
- Cart
- Login / Register for guests
- Account menu for authenticated users

Do not expose admin navigation in the storefront.

Do not show:

- Dashboard Admin
- Customer Management
- Reports
- Settings
- Permissions

---

# 12. Search

Search is a primary ecommerce interaction.

Desktop:

Use a wide search field.

Example:

```text
┌──────────────────────────────────────────────────────┐
│ 🔍 Cari charger, kabel data, headset...             │
└──────────────────────────────────────────────────────┘
```

Mobile:

Search must remain easily accessible.

Future-ready support:

- Search suggestions
- Recent searches
- Product suggestions
- Category suggestions

---

# 13. Cart Experience

The primary cart interaction should use a:

> Cart Drawer / Slide-over Cart

Desktop:

- Slide from the right.
- Approximately 35–40% viewport width.
- Does not navigate away from the current page.
- Displays cart items, quantities, subtotal, and actions.

Mobile:

Do NOT use a narrow right-side drawer.

Use a:

> Full-height Bottom Sheet / nearly full-screen cart sheet.

Mobile cart must provide:

- Scrollable items
- Sticky order summary
- Sticky Checkout CTA
- Comfortable touch targets
- Easy close action

The dedicated `/cart` page should still exist for full cart review.

The Cart Drawer is the quick shopping interaction.

---

# 14. Homepage

The homepage is an ecommerce storefront, not a corporate landing page.

Recommended structure:

1. Announcement / promotional message
2. Hero
3. Category discovery
4. Featured products
5. Promotional section
6. Benefits / trust
7. Registration CTA
8. Footer

The homepage should encourage users to discover products.

Avoid making it feel like a marketing one-pager.

---

# 15. Hero Section

The hero should communicate:

- Pixel Komunika brand
- Ecommerce purpose
- Verified customer model
- Wholesale / bulk pricing value

Use:

- Brand yellow
- Mascot
- Product imagery
- Strong typography
- Supporting copy
- Primary CTA

Example CTA:

```text
Belanja Sekarang
```

Secondary:

```text
Daftar Customer
```

For guests, explain that wholesale pricing requires verification.

---

# 16. Category Experience

Categories should be visually prominent.

Possible categories:

- Aksesoris Elektronik
- Power Bank
- Charger & Kabel
- Audio
- Storage
- Computer Accessories
- CCTV
- Pulsa
- Voucher Data

Use cards, imagery, or visual category tiles.

Avoid excessive text-only category lists.

---

# 17. Product Listing Page

Primary route:

```text
/produk
```

The product listing should feel like a modern ecommerce catalog.

Desktop:

```text
Filter Sidebar | Product Grid
```

The page should use the full available viewport width.

Do not constrain the product grid to a narrow centered container.

Large screens should naturally display more products per row when appropriate.

Example:

Desktop:

4–6 columns depending on viewport width.

Tablet:

2–4 columns.

Mobile:

2 columns.

---

# 18. Product Filters

Desktop:

Persistent filter sidebar.

Filters may include:

- Category
- Brand
- Price Range
- Availability
- Promotion

Mobile:

Convert filters into:

- Drawer
- Bottom sheet

Use touch-friendly controls.

Active filters should appear as removable chips.

---

# 19. Product Card

Product cards should be visually clean and premium.

Include:

- Product image
- Category
- Product name
- SKU where useful
- Stock status
- Price state
- CTA

Do not overload cards with information.

Use consistent image aspect ratios.

Allow product imagery to be the visual focus.

---

# 20. Product Pricing States

Because Pixel Komunika uses customer verification:

### Guest

Price may be hidden / blurred.

Show:

```text
Login untuk melihat harga grosir
```

### Pending Approval

Show:

```text
Menunggu Verifikasi
```

### Verified Customer

Show wholesale pricing.

Do not change authorization logic during visual redesign.

---

# 21. Product Detail Page

Route:

```text
/produk/{product}
```

The PDP should use a spacious ecommerce layout.

Desktop:

```text
Product Gallery        Product Information

                         Category
                         Product Name
                         SKU
                         Stock
                         Price
                         Quantity
                         Add to Cart
```

Mobile:

Stack content naturally.

Product gallery should become swipeable.

Purchase CTA should remain easy to access.

---

# 22. Product Detail Sections

Recommended hierarchy:

1. Breadcrumb
2. Product Gallery
3. Product Name
4. SKU
5. Stock
6. Pricing
7. Quantity
8. Add to Cart
9. Description
10. Specifications
11. Related Products

Do not introduce new database requirements only for visual purposes.

---

# 23. Customer Portal

Customer Portal should NOT visually copy the Admin Dashboard.

Admin:

```text
Sidebar
+
Topbar
+
Operational Dashboard
```

Customer:

```text
Storefront Header
+
My Account Experience
```

The customer portal should feel like an extension of the ecommerce storefront.

Customer navigation:

- Dashboard
- Pesanan
- Pembayaran
- Alamat
- Profil

Avoid heavy enterprise dashboard navigation.

---

# 24. Admin UI

Internal Admin UI uses Flux UI.

Admin should use:

- Left sidebar
- Compact topbar
- Dashboard layout
- Tables
- Filters
- Stat cards
- Forms

Admin is an operational workspace.

It should intentionally feel different from the public storefront.

---

# 24.1 Data Tables & Pagination

Every admin list table uses server-side pagination. Do not render the full
dataset on a single page.

Required behavior for every list page (Pelanggan, Produk, Pesanan, Media, dll.):

- **Rows-per-page selector**: a "Lihat per" dropdown with options [5, 10, 25, 50].
  Default is 10. Changing the value resets back to page 1.
- **Clear pagination controls**: First / Previous / numbered pages / Next / Last,
  plus a summary of what is shown, e.g. `Menampilkan 1–10 dari 128`.
- **State preserved**: changing page or rows-per-page keeps the active filters,
  search query, and status tabs intact (via URL query strings).
- **Empty state**: when a page has zero rows, show the empty-state component
  instead of a blank table body.
- Use the same per-page control on every list so behavior stays consistent
  across the admin workspace.

---

# 25. Authentication

Login and Register must use dedicated pages.

Do NOT place Login / Register inside a modal.

Do NOT inherit the Admin or Customer Portal sidebar.

Authentication should use a standalone auth shell.

Recommended layout:

```text
┌──────────────────────────────┬──────────────────────────────┐
│                              │                              │
│          MASCOT              │          LOGIN               │
│                              │                              │
│  Pixel Komunika branding     │          FORM               │
│                              │                              │
│  Value proposition           │                              │
│                              │                              │
└──────────────────────────────┴──────────────────────────────┘
```

The authentication experience should feel branded and premium.

---

# 26. Typography

Primary font:

```text
Plus Jakarta Sans
```

Usage:

Body:

400

Labels:

500

Headings:

600–700

Avoid excessive font weight.

Typography should create hierarchy through:

- Size
- Weight
- Spacing
- Contrast

---

# 27. Color Tokens

```css
@theme {
    --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

    --color-brand-yellow: #F8B818;
    --color-brand-yellow-soft: #F8D820;
    --color-brand-black: #181818;
    --color-brand-white: #FFFFFF;
    --color-brand-red: #F81818;

    --color-success: #15803D;
    --color-warning: #F8B818;
    --color-danger: #F81818;
}
```

---

# 28. Color Usage

Brand Yellow:

Primary CTA, highlights, important brand moments.

Brand Red:

Secondary accent, active states, important notifications.

Brand Black:

Primary typography and strong contrast.

White:

Primary surface.

Neutral Gray:

Supporting backgrounds and borders.

Avoid:

- Purple gradients
- AI-default gradients
- Excessive neon colors
- Corporate blue-heavy UI

---

# 29. Buttons

Primary:

Brand Yellow + Brand Black text.

Secondary:

Brand Black outline / neutral styling.

Danger:

Brand Red.

Never use white text on the brand-yellow primary button.

---

# 30. Cards

Cards should not all look like generic dashboard cards.

Storefront cards should prioritize:

- Product imagery
- Typography
- Whitespace
- Product hierarchy

Use rounded corners moderately.

Avoid excessive rounded containers everywhere.

---

# 31. Animation

Animations should be subtle.

Use animation for:

- Cart drawer
- Hover states
- Product image interaction
- Dropdowns
- Mobile menu
- Loading states

Avoid:

- Excessive parallax
- Scroll hijacking
- Long transitions
- Decorative animation everywhere

Recommended duration:

```text
150–300ms
```

---

# 32. Icons

Use Lucide Icons.

Icons should be:

- Simple
- Consistent
- Functional

Do not use emojis as primary UI icons.

Mascot illustrations may use the brand's existing illustration style.

---

# 33. Spacing

The storefront should have generous spacing.

However, whitespace must scale naturally.

Mobile:

Compact but comfortable.

Desktop:

More breathing room.

Large Desktop:

Use the additional viewport width rather than simply increasing empty margins.

---

# 34. Responsive Breakpoint Philosophy

Do not design only for:

```text
Desktop
Mobile
```

Consider:

```text
Small Mobile
Mobile
Tablet
Laptop
Desktop
Large Desktop
Ultra-wide
```

The layout should fluidly adapt between breakpoints.

---

# 35. Accessibility

Interactive elements must:

- Have accessible labels.
- Have sufficient contrast.
- Have touch-friendly dimensions.
- Support keyboard navigation where applicable.
- Clearly show focus states.

Do not rely solely on color to communicate state.

---

# 36. Performance

Storefront should prioritize performance.

Use:

- WebP / optimized images
- `loading="lazy"` where appropriate
- Responsive image sizing
- `srcset`
- Lightweight CSS animations
- Avoid unnecessary JS libraries

Do not introduce a large frontend framework solely for visual effects.

---

# 37. Frontend Toolkit

Current baseline:

- Laravel 13
- Blade
- Livewire 4
- Alpine.js
- Tailwind CSS 4
- Vite

Storefront:

- Custom Blade
- Tailwind CSS
- Alpine.js
- Lucide Icons

Internal UI:

- Flux UI

Do NOT use Flux UI as the foundation of the public storefront.

---

# 38. Design System Principle

The most important design principle:

> Full-width, fluid, responsive ecommerce experience with controlled spacing — not a narrow centered application layout.

The storefront should feel expansive on large screens while remaining comfortable and intuitive on mobile.

Pixel Komunika should look like a real premium ecommerce store, not a dashboard placed inside a centered container.

---

# 39. Implementation Guardrails

Before implementing any new component:

1. Check whether a reusable component already exists.
2. Reuse existing brand tokens.
3. Preserve existing business logic.
4. Preserve authorization rules.
5. Do not introduce unnecessary dependencies.
6. Do not introduce new database fields for visual purposes.
7. Ensure desktop and mobile behavior are both considered.
8. Avoid `max-w-*` constraints unless there is a specific UX reason.
9. Prefer fluid width with responsive horizontal padding.
10. Keep storefront and internal UI architecture separate.

---

# 40. Quality Bar

The final storefront should feel:

- Premium
- Full-width
- Fluid
- Modern
- Playful
- Responsive
- Fast
- Ecommerce-first

It should NOT feel:

- Like an admin dashboard
- Like a generic Laravel template
- Like a narrow SaaS application
- Like a corporate website
- Like a marketplace clone
- Like an AI-generated purple-gradient website

The final experience should preserve Pixel Komunika's recognizable cartoon/mascot identity while elevating it into a polished modern ecommerce storefront.
