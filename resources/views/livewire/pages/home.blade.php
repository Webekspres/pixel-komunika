{{--
    Storefront Homepage — Pixel Komunika
    Surface rhythm: Hero (yellow) → Categories (white) → Featured (gray-50)
    → Promo Banner (yellow-muted) → Benefits (white) → CTA (yellow) → Footer (black)
    Placeholder images: public/assets/placeholders/ — replace with client assets.
--}}
<div class="min-h-screen" x-data="{ mobileMenu: false }">

    {{-- =========================================================
         STICKY NAVIGATION
         ========================================================= --}}
    <header
        class="sticky top-0 z-40 border-b border-brand-black/8 bg-brand-white/92 backdrop-blur-lg"
        x-data="{ scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 12"
        :class="scrolled ? 'shadow-sm' : ''"
    >
        <div class="container-2xl">
            <div class="flex h-16 items-center gap-6 sm:h-[68px]">

                {{-- Logo --}}
                <a href="{{ route('home') }}" wire:navigate class="shrink-0" aria-label="Pixel Komunika beranda">
                    <img
                        src="{{ asset('assets/brand-logo.png') }}"
                        alt="Pixel Komunika"
                        class="h-9 w-auto sm:h-10"
                        width="160"
                        height="40"
                    >
                </a>

                {{-- Desktop nav --}}
                <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                    <a href="#kategori" class="rounded-lg px-3 py-2 text-sm font-medium text-brand-black/65 transition hover:bg-brand-black/5 hover:text-brand-black">
                        Kategori
                    </a>
                    <a href="#highlight" class="rounded-lg px-3 py-2 text-sm font-medium text-brand-black/65 transition hover:bg-brand-black/5 hover:text-brand-black">
                        Highlight
                    </a>
                    <a href="#unggulan" class="rounded-lg px-3 py-2 text-sm font-medium text-brand-black/65 transition hover:bg-brand-black/5 hover:text-brand-black">
                        Keunggulan
                    </a>
                </nav>

                {{-- Right side --}}
                <div class="ml-auto flex items-center gap-2 sm:gap-3">

                    {{-- Search (desktop) --}}
                    <label class="relative hidden lg:block">
                        <span class="sr-only">Cari katalog</span>
                        <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-brand-black/35" />
                        <input
                            type="search"
                            disabled
                            placeholder="Cari katalog…"
                            class="w-52 cursor-not-allowed rounded-full border border-brand-black/10 bg-gray-50 py-2 pr-4 pl-9 text-sm text-brand-black/40 transition focus:outline-none xl:w-64"
                        >
                    </label>

                    {{-- Cart icon --}}
                    <button
                        type="button"
                        disabled
                        title="Keranjang segera hadir"
                        class="relative inline-flex size-9 cursor-not-allowed items-center justify-center rounded-full text-brand-black/40 transition hover:bg-brand-black/5"
                        aria-label="Keranjang belanja"
                    >
                        <x-icon name="shopping-cart" class="size-[18px]" />
                    </button>

                    @auth
                        <a
                            href="{{ route('account.dashboard') }}"
                            class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-brand-black/70 transition hover:bg-brand-black/5 hover:text-brand-black sm:inline-flex"
                        >
                            <x-icon name="user" class="size-4" />
                            <span class="hidden lg:inline">Akun</span>
                        </a>

                        @if (auth()->user()->isActiveCustomer())
                            <a
                                href="{{ route('checkout.index') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft"
                            >
                                Checkout
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="hidden text-sm font-medium text-brand-black/70 transition hover:text-brand-black sm:inline"
                        >
                            Masuk
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center gap-1.5 rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft"
                        >
                            Daftar
                        </a>
                    @endauth

                    {{-- Mobile menu toggle --}}
                    <button
                        type="button"
                        class="inline-flex size-9 items-center justify-center rounded-lg text-brand-black/60 transition hover:bg-brand-black/5 md:hidden"
                        @click="mobileMenu = !mobileMenu"
                        :aria-expanded="mobileMenu"
                        aria-label="Buka menu"
                    >
                        <x-icon x-show="!mobileMenu" name="menu" class="size-5" />
                        <x-icon x-show="mobileMenu" name="x" class="size-5" x-cloak />
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div
            x-show="mobileMenu"
            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="border-t border-brand-black/8 bg-brand-white"
            x-cloak
            @click.outside="mobileMenu = false"
        >
            <nav class="container-2xl grid gap-1 py-3">
                <a href="#kategori" @click="mobileMenu = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5">Kategori</a>
                <a href="#highlight" @click="mobileMenu = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5">Highlight</a>
                <a href="#unggulan" @click="mobileMenu = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5">Keunggulan</a>

                <div class="my-2 border-t border-brand-black/8"></div>

                @auth
                    <a href="{{ route('account.dashboard') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5">Akun Saya</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5">Masuk</a>
                    <a href="{{ route('register') }}" class="mt-1 inline-flex w-full justify-center rounded-full bg-brand-yellow px-4 py-2.5 text-sm font-semibold text-brand-black hover:bg-brand-yellow-soft">Daftar sekarang</a>
                @endauth
            </nav>
        </div>
    </header>

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

        {{-- Yellow glow bottom --}}
        <div class="pointer-events-none absolute bottom-0 left-1/2 h-48 w-full -translate-x-1/2 opacity-40" style="background: radial-gradient(ellipse at center, #f8b818 0%, transparent 70%);" aria-hidden="true"></div>

        <div class="container-2xl relative py-20 sm:py-28 lg:py-32">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Left: Copy --}}
                <div class="animate-fade-up max-w-xl">

                    {{-- Trust badge row --}}
                    <div class="mb-7 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-black/15 bg-white/60 px-3 py-1 text-xs font-semibold text-brand-black backdrop-blur-sm">
                            <x-icon name="badge-check" class="size-3.5 text-brand-black" />
                            B2B Terverifikasi
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-black/15 bg-white/60 px-3 py-1 text-xs font-semibold text-brand-black backdrop-blur-sm">
                            <x-icon name="tag" class="size-3.5 text-brand-black" />
                            Harga Partai & Grosir
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-black/15 bg-white/60 px-3 py-1 text-xs font-semibold text-brand-black backdrop-blur-sm">
                            <x-icon name="layers" class="size-3.5 text-brand-black" />
                            3 Kategori Utama
                        </span>
                    </div>

                    <h1 class="text-5xl font-bold tracking-tight text-brand-black sm:text-6xl lg:text-7xl">
                        Aksesoris &amp;<br>
                        <span class="relative whitespace-nowrap">
                            konektivitas
                            <svg class="absolute -bottom-1 left-0 w-full" viewBox="0 0 300 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M2 9.5C50 4 150 2 298 5" stroke="#181818" stroke-width="3.5" stroke-linecap="round" opacity="0.35"/>
                            </svg>
                        </span>
                        <br>untuk bisnis Anda
                    </h1>

                    <p class="mt-6 text-lg leading-relaxed text-brand-black/75 sm:text-xl">
                        Power bank, charger, audio, kartu data, voucher internet, dan pulsa —
                        <strong class="font-semibold text-brand-black">harga partai &amp; grosir</strong>
                        setelah akun disetujui.
                    </p>

                    {{-- CTAs --}}
                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        @auth
                            <a
                                href="{{ route('account.dashboard') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-brand-black px-7 py-3.5 text-sm font-semibold text-brand-white transition hover:bg-brand-black/85"
                            >
                                Buka akun saya
                                <x-icon name="arrow-right" class="size-4" />
                            </a>
                            @if (auth()->user()->isActiveCustomer())
                                <a
                                    href="{{ route('orders.index') }}"
                                    class="inline-flex items-center rounded-full border-2 border-brand-black/20 bg-white/50 px-7 py-3.5 text-sm font-semibold text-brand-black transition hover:border-brand-black/40 hover:bg-white/70"
                                >
                                    Lihat order
                                </a>
                            @endif
                        @else
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-brand-black px-7 py-3.5 text-sm font-semibold text-brand-white transition hover:bg-brand-black/85"
                            >
                                Daftar sekarang
                                <x-icon name="arrow-right" class="size-4" />
                            </a>
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center rounded-full border-2 border-brand-black/20 bg-white/50 px-7 py-3.5 text-sm font-semibold text-brand-black transition hover:border-brand-black/40 hover:bg-white/70"
                            >
                                Masuk ke akun
                            </a>
                        @endauth
                    </div>

                    {{-- Social proof strip --}}
                    <div class="mt-10 flex items-center gap-5 border-t border-brand-black/15 pt-6">
                        <div class="text-center">
                            <p class="text-xl font-bold text-brand-black">3+</p>
                            <p class="text-xs text-brand-black/55">Kategori produk</p>
                        </div>
                        <div class="h-8 w-px bg-brand-black/15"></div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-brand-black">B2B</p>
                            <p class="text-xs text-brand-black/55">Pelanggan terverifikasi</p>
                        </div>
                        <div class="h-8 w-px bg-brand-black/15"></div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-brand-black">POS</p>
                            <p class="text-xs text-brand-black/55">Data real-time</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Mascot --}}
                <div class="animate-fade-up relative flex justify-center lg:justify-end" style="animation-delay: 150ms;">

                    {{-- Floating icon badges --}}
                    <div class="animate-fade-in absolute top-4 right-4 inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 shadow-lg" style="animation-delay: 400ms;">
                        <x-icon name="battery-charging" class="size-4 text-brand-yellow" />
                        <span class="text-xs font-semibold text-brand-black">Power Bank</span>
                    </div>

                    <div class="animate-fade-in absolute bottom-16 left-0 inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 shadow-lg" style="animation-delay: 600ms;">
                        <x-icon name="headphones" class="size-4 text-brand-yellow" />
                        <span class="text-xs font-semibold text-brand-black">Audio</span>
                    </div>

                    <div class="animate-fade-in absolute top-1/2 right-0 hidden -translate-y-1/2 items-center gap-2 rounded-full bg-brand-yellow px-3 py-2 shadow-lg xl:inline-flex" style="animation-delay: 800ms;">
                        <x-icon name="smartphone" class="size-4 text-brand-black" />
                        <span class="text-xs font-semibold text-brand-black">Pulsa & Data</span>
                    </div>

                    <img
                        src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                        alt="Maskot Pixel Komunika"
                        class="animate-float relative z-10 w-full max-w-xs drop-shadow-2xl sm:max-w-sm lg:max-w-md xl:max-w-lg"
                        width="512"
                        height="512"
                        fetchpriority="high"
                    >
                </div>
            </div>
        </div>

        {{-- Wave divider --}}
        <div class="relative -mb-px">
            <svg viewBox="0 0 1440 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full" aria-hidden="true" preserveAspectRatio="none">
                <path d="M0,32 C240,64 480,0 720,32 C960,64 1200,0 1440,32 L1440,64 L0,64 Z" fill="#ffffff"/>
            </svg>
        </div>
    </section>

    {{-- =========================================================
         CATEGORIES — White
         ========================================================= --}}
    <section id="kategori" class="section-white py-20 sm:py-28">
        <div class="container-2xl">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-storefront.section-header
                    eyebrow="Kategori produk"
                    title="Tiga jalur utama"
                    description="Katalog mengikuti data POS. Semua produk yang tersedia berasal dari inventaris operasional toko."
                />

                <a
                    href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-full border border-brand-black/15 px-5 py-2.5 text-sm font-medium text-brand-black/70 transition hover:border-brand-yellow hover:text-brand-black"
                >
                    Lihat semua
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>

            {{-- Bento grid --}}
            <div class="mt-12 grid gap-4 lg:grid-cols-3 lg:grid-rows-2 lg:gap-5">

                {{-- Large card (2×2) --}}
                <x-storefront.category-card
                    title="Aksesoris elektronik"
                    description="Power bank, charger & kabel, handsfree, flashdisk, speaker, keyboard, mouse, CCTV, lampu, dan lainnya."
                    image="{{ asset('assets/placeholders/accessories.webp') }}"
                    icon="battery-charging"
                    theme="yellow"
                    size="large"
                    href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                    class="lg:col-span-2 lg:row-span-2"
                />

                {{-- Small card 1 --}}
                <x-storefront.category-card
                    title="Kartu & voucher internet"
                    description="Kartu data dan voucher internet operator."
                    image="{{ asset('assets/placeholders/voucher.webp') }}"
                    icon="radio"
                    theme="gray"
                    href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                />

                {{-- Small card 2 --}}
                <x-storefront.category-card
                    title="Pulsa"
                    description="Isi ulang untuk operasional dan penjualan ulang."
                    image="{{ asset('assets/placeholders/pulsa.webp') }}"
                    icon="smartphone"
                    theme="dark"
                    href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                />
            </div>
        </div>
    </section>

    {{-- =========================================================
         FEATURED PRODUCTS — Gray-50
         ========================================================= --}}
    <section id="highlight" class="section-gray border-y border-brand-black/6 py-20 sm:py-28">
        <div class="container-2xl">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-storefront.section-header
                    eyebrow="Permintaan tinggi"
                    title="Produk favorit"
                    description="Contoh item yang sering dicari. Harga partai tampil setelah akun disetujui admin."
                />

                <div class="flex shrink-0 items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-black/10 bg-white px-3 py-1.5 text-xs font-medium text-brand-black/55">
                        <span class="inline-block size-1.5 animate-pulse rounded-full bg-green-500"></span>
                        Data live POS
                    </span>
                </div>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['img' => 'product-1.webp', 'badge' => 'Tersedia',      'badgeVariant' => 'available', 'title' => 'Power bank 10.000 mAh',  'icon' => 'battery-charging', 'category' => 'Aksesoris'],
                    ['img' => 'product-2.webp', 'badge' => 'Stok terbatas', 'badgeVariant' => 'limited',   'title' => 'Headphone wireless',      'icon' => 'headphones',       'category' => 'Audio'],
                    ['img' => 'product-3.webp', 'badge' => 'Tersedia',      'badgeVariant' => 'available', 'title' => 'Keyboard office',         'icon' => 'cable',            'category' => 'Aksesoris'],
                    ['img' => 'product-4.webp', 'badge' => 'Tersedia',      'badgeVariant' => 'available', 'title' => 'Mouse wireless',          'icon' => 'package',          'category' => 'Aksesoris'],
                ] as $product)
                    <x-storefront.product-card
                        :title="$product['title']"
                        :image="asset('assets/placeholders/'.$product['img'])"
                        :badge="$product['badge']"
                        :badge-variant="$product['badgeVariant']"
                        :icon="$product['icon']"
                        :category="$product['category']"
                        :show-price="auth()->user()?->canViewPrices() ?? false"
                        price="Rp 999.000"
                        href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                    />
                @endforeach
            </div>

            {{-- Secondary CTA --}}
            <div class="mt-12 text-center">
                <a
                    href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-brand-black/15 px-6 py-3 text-sm font-medium text-brand-black/70 transition hover:border-brand-yellow hover:text-brand-black"
                >
                    Lihat katalog lengkap
                    <x-icon name="arrow-right" class="size-4" />
                </a>
                <p class="mt-3 text-xs text-brand-black/40">Katalog penuh dan harga tersedia setelah akun diverifikasi.</p>
            </div>
        </div>
    </section>

    {{-- =========================================================
         PROMOTIONAL BANNER — Yellow-muted
         ========================================================= --}}
    <x-storefront.banner
        title="Harga partai untuk bisnis Anda"
        description="Daftarkan usaha Anda dan dapatkan akses ke harga grosir, riwayat order, dan dukungan pengiriman langsung ke lokasi."
        primary-label="Daftar sekarang"
        primary-href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
        secondary-label="{{ auth()->check() ? '' : 'Punya akun? Masuk' }}"
        secondary-href="{{ auth()->check() ? '' : route('login') }}"
        :mascot="true"
        theme="yellow"
    />

    {{-- =========================================================
         BENEFITS / WHY US — White
         ========================================================= --}}
    <section id="unggulan" class="section-white py-20 sm:py-28">
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
         REGISTRATION CTA — Yellow (with mascot)
         ========================================================= --}}
    @guest
        <x-storefront.cta
            title="Siap bergabung?"
            description="Guest dan akun pending tetap bisa melihat katalog. Harga, checkout, dan riwayat order hanya terbuka setelah akun disetujui admin."
            primary-label="Daftar sekarang"
            primary-href="{{ route('register') }}"
            secondary-label="Sudah punya akun? Masuk"
            secondary-href="{{ route('login') }}"
            :mascot="true"
            theme="yellow"
            align="left"
        />
    @else
        <x-storefront.cta
            title="Akun Anda aktif"
            description="Jelajahi katalog lengkap, lihat harga partai, dan mulai checkout langsung dari dashboard akun Anda."
            primary-label="Buka dashboard akun"
            primary-href="{{ route('account.dashboard') }}"
            :mascot="true"
            theme="yellow"
            align="left"
        />
    @endguest

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

                    {{-- Trust badges --}}
                    <div class="mt-6 flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 px-3 py-1 text-xs text-brand-white/50">
                            <x-icon name="badge-check" class="size-3" />
                            B2B
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 px-3 py-1 text-xs text-brand-white/50">
                            <x-icon name="shield-check" class="size-3" />
                            Terverifikasi
                        </span>
                    </div>
                </div>

                {{-- Platform --}}
                <div>
                    <h3 class="mb-4 text-xs font-semibold tracking-widest text-brand-white/35 uppercase">Platform</h3>
                    <ul class="space-y-2.5 text-sm text-brand-white/60">
                        <li><a href="#kategori" class="transition hover:text-brand-yellow">Kategori produk</a></li>
                        <li><a href="#highlight" class="transition hover:text-brand-yellow">Produk highlight</a></li>
                        <li><a href="#unggulan" class="transition hover:text-brand-yellow">Keunggulan</a></li>
                        <li><span class="text-brand-white/30">Pengiriman (segera)</span></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h3 class="mb-4 text-xs font-semibold tracking-widest text-brand-white/35 uppercase">Legal</h3>
                    <ul class="space-y-2.5 text-sm text-brand-white/60">
                        <li><span class="text-brand-white/30">Kebijakan privasi (segera)</span></li>
                        <li><span class="text-brand-white/30">Syarat layanan (segera)</span></li>
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h3 class="mb-4 text-xs font-semibold tracking-widest text-brand-white/35 uppercase">Akun</h3>
                    <ul class="space-y-2.5 text-sm text-brand-white/60">
                        @auth
                            <li><a href="{{ route('account.dashboard') }}" class="transition hover:text-brand-yellow">Dashboard akun</a></li>
                            @if (auth()->user()->isActiveCustomer())
                                <li><a href="{{ route('orders.index') }}" class="transition hover:text-brand-yellow">Riwayat order</a></li>
                            @endif
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
