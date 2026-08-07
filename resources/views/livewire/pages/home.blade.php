{{--
    Storefront Homepage — Pixel Komunika
    Surface rhythm: Hero (yellow) → Categories (white) → Products Grid (gray-50)
    → Promo Banner (yellow-muted) → Benefits (white) → CTA (yellow) → Footer (black)
--}}
<div class="min-h-screen" x-data="{ mobileMenu: false }">

    {{-- Flash Notifications --}}
    @if (session()->has('success'))
        <div class="fixed bottom-5 right-5 z-50 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="check-circle" class="size-5" />
            <span>{{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="underline ml-2 text-emerald-100 hover:text-white">Lihat Keranjang</a>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-5 right-5 z-50 rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="x-circle" class="size-5" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Storefront Navigation --}}
    <x-storefront.navbar :cart-count="$cartCount" />

    {{-- =========================================================
         HERO — Yellow
         ========================================================= --}}
    <section class="relative overflow-hidden bg-brand-yellow">

        {{-- Dot-grid background --}}
        <div
            class="pointer-events-none absolute inset-0 opacity-20"
            style="background-image: radial-gradient(circle at 1px 1px, #181818 1px, transparent 0); background-size: 28px 28px;"
            aria-hidden="true"
        ></div>

        <div class="container-2xl relative py-16 sm:py-24 lg:py-28">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Left: Copy --}}
                <div class="animate-fade-up max-w-xl">
                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-black/15 bg-white/60 px-3 py-1 text-xs font-semibold text-brand-black backdrop-blur-sm">
                            <x-icon name="badge-check" class="size-3.5 text-brand-black" />
                            B2B Terverifikasi
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-black/15 bg-white/60 px-3 py-1 text-xs font-semibold text-brand-black backdrop-blur-sm">
                            <x-icon name="tag" class="size-3.5 text-brand-black" />
                            Harga Partai & Grosir
                        </span>
                    </div>

                    <h1 class="text-4xl font-bold tracking-tight text-brand-black sm:text-5xl lg:text-6xl">
                        Aksesoris &amp;<br>
                        <span class="relative whitespace-nowrap">
                            konektivitas
                            <svg class="absolute -bottom-1 left-0 w-full" viewBox="0 0 300 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M2 9.5C50 4 150 2 298 5" stroke="#181818" stroke-width="3.5" stroke-linecap="round" opacity="0.35"/>
                            </svg>
                        </span>
                        <br>untuk bisnis Anda
                    </h1>

                    <p class="mt-5 text-base leading-relaxed text-brand-black/75 sm:text-lg">
                        Power bank, charger, audio, kartu data, voucher internet, dan pulsa —
                        <strong class="font-semibold text-brand-black">harga partai &amp; grosir</strong>
                        langsung dari master data POS toko.
                    </p>

                    {{-- CTAs --}}
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a
                            href="#katalog"
                            class="inline-flex items-center gap-2 rounded-full bg-brand-black px-7 py-3.5 text-sm font-semibold text-brand-white transition hover:bg-brand-black/85"
                        >
                            Jelajahi Katalog POS
                            <x-icon name="arrow-down" class="size-4" />
                        </a>
                        <a
                            href="{{ route('cart.index') }}"
                            wire:navigate
                            class="inline-flex items-center gap-2 rounded-full border-2 border-brand-black/20 bg-white/50 px-6 py-3.5 text-sm font-semibold text-brand-black transition hover:border-brand-black/40 hover:bg-white/70"
                        >
                            <x-icon name="shopping-cart" class="size-4" />
                            Keranjang ({{ $cartCount }})
                        </a>
                    </div>
                </div>

                {{-- Right: Mascot --}}
                <div class="animate-fade-up relative flex justify-center lg:justify-end">
                    <img
                        src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                        alt="Maskot Pixel Komunika"
                        class="animate-float relative z-10 w-full max-w-xs drop-shadow-2xl sm:max-w-sm lg:max-w-md"
                        width="450"
                        height="450"
                        fetchpriority="high"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
         LIVE CATALOG GRID — E-Commerce Experience
         ========================================================= --}}
    <section id="katalog" class="section-white py-16 sm:py-24">
        <div class="container-2xl">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <x-storefront.section-header
                    eyebrow="Master Data POS"
                    title="Katalog Produk"
                    description="Pilih produk dari inventaris toko. Tambahkan langsung ke keranjang belanja Anda."
                />

                {{-- Search & Category Filter bar --}}
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        wire:click="$set('selectedCategory', 'all')"
                        class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $selectedCategory === 'all' ? 'bg-brand-black text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}"
                    >
                        Semua Produk
                    </button>
                    @foreach ($categories as $category)
                        <button
                            wire:click="$set('selectedCategory', '{{ $category->id }}')"
                            class="rounded-full px-4 py-2 text-xs font-semibold transition {{ (string)$selectedCategory === (string)$category->id ? 'bg-brand-black text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($products as $product)
                    <div class="group relative flex flex-col justify-between rounded-3xl border border-zinc-200 bg-white p-5 shadow-xs transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <div>
                            {{-- Category & Badge --}}
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/60">
                                    {{ $product->category->name }}
                                </span>
                                @if ($product->inventorySnapshot?->quantity_available > 0)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        Stok: {{ $product->inventorySnapshot->quantity_available }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md">
                                        Stok Habis
                                    </span>
                                @endif
                            </div>

                            {{-- Product Title & SKU --}}
                            <h3 class="text-base font-bold text-zinc-900 line-clamp-2 group-hover:text-amber-600 transition-colors">
                                {{ $product->name }}
                            </h3>
                            <p class="mt-1 text-xs text-zinc-400">SKU: {{ $product->sku }}</p>
                        </div>

                        {{-- Pricing & Add to Cart --}}
                        <div class="mt-6 border-t border-zinc-100 pt-4">
                            <div class="flex items-baseline justify-between mb-4">
                                <span class="text-xs text-zinc-500">Harga Grosir:</span>
                                @if (auth()->user()?->canViewPrices())
                                    <span class="text-lg font-extrabold text-zinc-950">
                                        Rp {{ number_format($product->latestPrice?->price_wholesale_tier1 ?? 0, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                                        🔒 Verifikasi Akun
                                    </span>
                                @endif
                            </div>

                            <button
                                wire:click="addToCart({{ $product->id }})"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-amber-400 py-3 text-xs font-bold text-brand-black transition hover:bg-amber-300 active:scale-[0.98] shadow-sm"
                            >
                                <x-icon name="shopping-cart" class="size-4" />
                                <span>+ Keranjang</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <div class="inline-flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 mb-3">
                            <x-icon name="package-search" class="size-7" />
                        </div>
                        <h4 class="text-base font-semibold text-zinc-800">Tidak ada produk ditemukan</h4>
                        <p class="text-xs text-zinc-500 mt-1">Coba gunakan kata kunci pencarian atau kategori lain.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =========================================================
         BENEFITS / WHY US — White
         ========================================================= --}}
    <section id="unggulan" class="section-gray border-t border-zinc-200 py-20 sm:py-28">
        <div class="container-2xl">
            <x-storefront.section-header
                eyebrow="Kenapa kami"
                title="Dirancang untuk reseller &amp; bisnis"
                description="Portal untuk pelanggan terverifikasi — bukan toko publik terbuka. Setiap fitur dibangun untuk mendukung operasional grosir Anda."
                align="center"
                class="mb-14"
            />

            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <x-storefront.feature-card
                    icon="badge-check"
                    title="Akun terverifikasi"
                    description="Setelah disetujui admin, harga partai & grosir siap dipakai untuk pemesanan langsung dari platform."
                />
                <x-storefront.feature-card
                    icon="package"
                    title="Katalog dari POS"
                    description="Produk, stok, dan harga mengikuti master operasional toko — bukan inventaris spekulatif atau data manual."
                />
                <x-storefront.feature-card
                    icon="truck"
                    title="Pengiriman jelas"
                    description="Pilihan kurir toko atau rate pengiriman eksternal saat checkout, dengan total yang dihitung server-side secara akurat."
                />
                <x-storefront.feature-card
                    icon="receipt"
                    title="Invoice otomatis"
                    description="Setiap pesanan menghasilkan invoice yang bisa diunduh, lengkap dengan detail harga, pajak, dan ongkos kirim."
                />
                <x-storefront.feature-card
                    icon="history"
                    title="Riwayat order"
                    description="Lihat semua pesanan Anda dalam satu dashboard — dari pending hingga terkirim, lengkap dengan status terkini."
                />
                <x-storefront.feature-card
                    icon="shield-check"
                    title="Data aman"
                    description="Harga grosir hanya terlihat untuk akun yang sudah disetujui. Tamu dan akun pending tidak mendapat akses."
                />
            </div>
        </div>
    </section>

    {{-- =========================================================
         FOOTER — Black
         ========================================================= --}}
    <footer class="section-black border-t border-brand-white/8">
        <div class="container-2xl py-14 sm:py-16">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr]">

                {{-- Brand --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <img
                        src="{{ asset('assets/brand-logo.png') }}"
                        alt="Pixel Komunika"
                        class="mb-5 h-9 w-auto brightness-0 invert"
                        width="140"
                        height="36"
                        loading="lazy"
                    >
                    <p class="max-w-xs text-sm leading-relaxed text-brand-white/55">
                        E-commerce pelanggan terverifikasi untuk aksesoris elektronik, kartu &amp; voucher, dan pulsa — harga partai &amp; grosir.
                    </p>
                </div>

                {{-- Platform --}}
                <div>
                    <h3 class="mb-4 text-xs font-semibold tracking-widest text-brand-white/35 uppercase">Platform</h3>
                    <ul class="space-y-2.5 text-sm text-brand-white/60">
                        <li><a href="#katalog" class="transition hover:text-brand-yellow">Katalog produk</a></li>
                        <li><a href="#unggulan" class="transition hover:text-brand-yellow">Keunggulan</a></li>
                        <li><a href="{{ route('cart.index') }}" wire:navigate class="transition hover:text-brand-yellow">Keranjang Belanja</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h3 class="mb-4 text-xs font-semibold tracking-widest text-brand-white/35 uppercase">Legal</h3>
                    <ul class="space-y-2.5 text-sm text-brand-white/60">
                        <li><span class="text-brand-white/30">Kebijakan privasi</span></li>
                        <li><span class="text-brand-white/30">Syarat layanan</span></li>
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h3 class="mb-4 text-xs font-semibold tracking-widest text-brand-white/35 uppercase">Akun</h3>
                    <ul class="space-y-2.5 text-sm text-brand-white/60">
                        @auth
                            <li><a href="{{ route('account.dashboard') }}" class="transition hover:text-brand-yellow">Dashboard akun</a></li>
                        @else
                            <li><a href="{{ route('register') }}" class="transition hover:text-brand-yellow">Daftar</a></li>
                            <li><a href="{{ route('login') }}" class="transition hover:text-brand-yellow">Masuk</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-start justify-between gap-4 border-t border-brand-white/8 pt-8 sm:flex-row sm:items-center">
                <p class="text-xs text-brand-white/35">&copy; {{ date('Y') }} Pixel Komunika. All rights reserved.</p>
                <p class="text-xs text-brand-white/25">Dibangun dengan ❤ di Indonesia</p>
            </div>
        </div>
    </footer>

</div>
