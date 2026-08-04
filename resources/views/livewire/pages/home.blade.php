{{-- Placeholder images: public/assets/placeholders/ (Unsplash); replace with client/product assets. --}}
<div class="min-h-screen">
    <header class="sticky top-0 z-30 border-b border-brand-black/10 bg-brand-white/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-6xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" wire:navigate class="shrink-0">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-9 w-auto sm:h-10"
                >
            </a>

            <nav class="hidden items-center gap-5 text-sm font-medium text-brand-black/75 md:flex">
                <a href="#kategori" class="transition hover:text-brand-red">Kategori</a>
                <a href="#highlight" class="transition hover:text-brand-red">Highlight</a>
                <a href="#unggulan" class="transition hover:text-brand-red">Keunggulan</a>
            </nav>

            <div class="ml-auto flex min-w-0 flex-1 items-center justify-end gap-2 sm:gap-3 md:max-w-md md:flex-none lg:max-w-sm">
                <label class="relative hidden min-w-0 flex-1 sm:block">
                    <span class="sr-only">Cari katalog (segera)</span>
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-brand-black/40" />
                    <input
                        type="search"
                        disabled
                        placeholder="Cari katalog (segera)"
                        class="w-full cursor-not-allowed rounded-full border border-brand-black/10 bg-gray-50 py-2 pr-3 pl-9 text-sm text-brand-black/50"
                    >
                </label>

                <button
                    type="button"
                    disabled
                    title="Keranjang segera hadir"
                    class="inline-flex size-10 cursor-not-allowed items-center justify-center rounded-full text-brand-black/40"
                >
                    <x-icon name="shopping-cart" class="size-5" />
                    <span class="sr-only">Keranjang (segera)</span>
                </button>

                @auth
                    <a href="{{ route('account.dashboard') }}" class="hidden text-sm font-medium text-brand-black/80 transition hover:text-brand-red sm:inline">
                        Akun
                    </a>
                    @if (auth()->user()->isActiveCustomer())
                        <a
                            href="{{ route('checkout.index') }}"
                            class="rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft"
                        >
                            Checkout
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-medium text-brand-black/80 transition hover:text-brand-red sm:inline">
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft"
                    >
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <section class="relative overflow-hidden bg-brand-yellow">
        <div
            class="pointer-events-none absolute inset-0 opacity-25"
            style="background: repeating-conic-gradient(from 0deg at 72% 40%, rgba(255,255,255,0.4) 0deg 8deg, transparent 8deg 16deg);"
            aria-hidden="true"
        ></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-6 py-16 sm:py-20 lg:grid-cols-2 lg:gap-8 lg:px-8 lg:py-24">
            <div class="animate-fade-up max-w-xl">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="mb-6 h-14 w-auto sm:h-16"
                >
                <h1 class="text-4xl font-bold tracking-tight text-brand-black sm:text-5xl lg:text-6xl">
                    Aksesoris &amp; konektivitas untuk bisnis Anda
                </h1>
                <p class="mt-5 text-base leading-relaxed text-brand-black/80 sm:text-lg">
                    Power bank, charger, audio, kartu data, voucher internet, dan pulsa — harga partai &amp; grosir setelah akun disetujui.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    @auth
                        <a
                            href="{{ route('account.dashboard') }}"
                            class="inline-flex rounded-full bg-brand-black px-6 py-3 text-sm font-semibold text-brand-white transition hover:bg-brand-black/90"
                        >
                            Buka akun
                        </a>
                        @if (auth()->user()->isActiveCustomer())
                            <a
                                href="{{ route('orders.index') }}"
                                class="inline-flex rounded-full border-2 border-brand-black/20 bg-brand-white/50 px-6 py-3 text-sm font-semibold text-brand-black transition hover:border-brand-red hover:text-brand-red"
                            >
                                Lihat order
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex rounded-full bg-brand-black px-6 py-3 text-sm font-semibold text-brand-white transition hover:bg-brand-black/90"
                        >
                            Daftar sekarang
                        </a>
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex rounded-full border-2 border-brand-black/20 bg-brand-white/50 px-6 py-3 text-sm font-semibold text-brand-black transition hover:border-brand-red hover:text-brand-red"
                        >
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>

            <div class="animate-fade-up relative flex justify-center lg:justify-end" style="animation-delay: 120ms;">
                <img
                    src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                    alt="Maskot Pixel Komunika"
                    class="animate-float relative z-10 w-full max-w-md drop-shadow-xl lg:max-w-lg"
                    width="512"
                    height="512"
                >
            </div>
        </div>
    </section>

    <section id="kategori" class="bg-brand-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="max-w-xl">
                    <h2 class="text-3xl font-bold tracking-tight text-brand-black sm:text-4xl">
                        Ikhtisar kategori
                    </h2>
                    <p class="mt-3 text-brand-black/70">
                        Katalog mengikuti data POS. Tiga jalur utama yang dilayani Pixel Komunika:
                    </p>
                </div>
            </div>

            <div class="mt-10 grid gap-5 lg:grid-cols-3 lg:grid-rows-2">
                <a
                    href="{{ auth()->check() ? route('account.dashboard') : route('register') }}"
                    class="group relative overflow-hidden rounded-2xl bg-brand-yellow lg:col-span-2 lg:row-span-2"
                >
                    <img
                        src="{{ asset('assets/placeholders/accessories.webp') }}"
                        alt=""
                        class="absolute inset-0 h-full w-full object-cover opacity-35 transition duration-500 group-hover:scale-105"
                        loading="lazy"
                        width="1200"
                        height="800"
                    >
                    <div class="relative flex min-h-64 flex-col justify-end p-6 sm:min-h-80 sm:p-8 lg:min-h-full">
                        <x-icon name="battery-charging" class="mb-3 size-8 text-brand-black" />
                        <h3 class="text-2xl font-bold text-brand-black">Aksesoris elektronik</h3>
                        <p class="mt-2 max-w-md text-sm leading-relaxed text-brand-black/80">
                            Power bank, charger &amp; kabel, handsfree, flashdisk, speaker, keyboard, mouse, CCTV, lampu, dan lainnya.
                        </p>
                    </div>
                </a>

                <a href="{{ auth()->check() ? route('account.dashboard') : route('register') }}" class="group relative overflow-hidden rounded-2xl bg-gray-100">
                    <img
                        src="{{ asset('assets/placeholders/voucher.webp') }}"
                        alt=""
                        class="absolute inset-0 h-full w-full object-cover opacity-40 transition duration-500 group-hover:scale-105"
                        loading="lazy"
                        width="800"
                        height="600"
                    >
                    <div class="relative flex min-h-40 flex-col justify-end p-5 sm:p-6">
                        <x-icon name="radio" class="mb-2 size-6 text-brand-black" />
                        <h3 class="text-lg font-bold text-brand-black">Kartu &amp; voucher</h3>
                        <p class="mt-1 text-sm text-brand-black/75">
                            Kartu data dan voucher internet operator.
                        </p>
                    </div>
                </a>

                <a href="{{ auth()->check() ? route('account.dashboard') : route('register') }}" class="group relative overflow-hidden rounded-2xl bg-brand-black">
                    <img
                        src="{{ asset('assets/placeholders/pulsa.webp') }}"
                        alt=""
                        class="absolute inset-0 h-full w-full object-cover opacity-35 transition duration-500 group-hover:scale-105"
                        loading="lazy"
                        width="800"
                        height="600"
                    >
                    <div class="relative flex min-h-40 flex-col justify-end p-5 sm:p-6">
                        <x-icon name="smartphone" class="mb-2 size-6 text-brand-white" />
                        <h3 class="text-lg font-bold text-brand-white">Pulsa</h3>
                        <p class="mt-1 text-sm text-brand-white/80">
                            Isi ulang untuk operasional dan penjualan ulang.
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section id="highlight" class="border-t border-gray-200 bg-gray-50 py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="max-w-xl">
                <h2 class="text-3xl font-bold tracking-tight text-brand-black sm:text-4xl">
                    Permintaan tinggi
                </h2>
                <p class="mt-3 text-brand-black/70">
                    Contoh item yang sering dicari. Harga partai tampil setelah akun disetujui.
                </p>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['img' => 'product-1.webp', 'badge' => 'Tersedia', 'badgeClass' => 'bg-brand-yellow text-brand-black', 'title' => 'Power bank 10.000 mAh', 'icon' => 'battery-charging'],
                    ['img' => 'product-2.webp', 'badge' => 'Stok terbatas', 'badgeClass' => 'bg-brand-black text-brand-white', 'title' => 'Headphone wireless', 'icon' => 'headphones'],
                    ['img' => 'product-3.webp', 'badge' => 'Tersedia', 'badgeClass' => 'bg-brand-yellow text-brand-black', 'title' => 'Keyboard office', 'icon' => 'cable'],
                    ['img' => 'product-4.webp', 'badge' => 'Tersedia', 'badgeClass' => 'bg-brand-yellow text-brand-black', 'title' => 'Mouse wireless', 'icon' => 'package'],
                ] as $item)
                    <article class="overflow-hidden rounded-2xl border border-brand-black/10 bg-brand-white transition hover:-translate-y-0.5 hover:border-brand-yellow/60">
                        <div class="relative aspect-4/3 bg-gray-100">
                            <img
                                src="{{ asset('assets/placeholders/'.$item['img']) }}"
                                alt=""
                                class="h-full w-full object-cover"
                                loading="lazy"
                                width="800"
                                height="600"
                            >
                            <span class="absolute top-3 left-3 rounded-full px-2.5 py-1 text-xs font-semibold {{ $item['badgeClass'] }}">
                                {{ $item['badge'] }}
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="mb-2 flex items-center gap-2 text-brand-black/50">
                                <x-icon :name="$item['icon']" class="size-4" />
                                <span class="text-xs font-medium tracking-wide uppercase">Placeholder</span>
                            </div>
                            <h3 class="font-semibold text-brand-black">{{ $item['title'] }}</h3>
                            <p class="mt-1 text-xs text-brand-black/55">Harga partai</p>
                            @if (auth()->user()?->canViewPrices())
                                <p class="mt-1 text-lg font-bold text-brand-black">
                                    Rp 999.000
                                </p>
                            @else
                                <p class="mt-1 text-sm font-semibold text-brand-black/55">
                                    Login aktif untuk melihat harga
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="unggulan" class="bg-brand-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-brand-black sm:text-4xl">
                    Kenapa berbelanja di sini
                </h2>
                <p class="mt-3 text-brand-black/70">
                    Portal untuk pelanggan terverifikasi — bukan toko publik terbuka.
                </p>
            </div>

            <div class="mt-12 grid gap-10 sm:grid-cols-3">
                <div class="text-center sm:text-left">
                    <div class="mx-auto mb-4 inline-flex size-12 items-center justify-center rounded-full bg-brand-yellow sm:mx-0">
                        <x-icon name="badge-check" class="size-6 text-brand-black" />
                    </div>
                    <h3 class="text-lg font-bold text-brand-black">Akun terverifikasi</h3>
                    <p class="mt-2 text-sm leading-relaxed text-brand-black/70">
                        Setelah disetujui admin, harga partai &amp; grosir siap dipakai untuk pemesanan.
                    </p>
                </div>
                <div class="text-center sm:text-left">
                    <div class="mx-auto mb-4 inline-flex size-12 items-center justify-center rounded-full bg-brand-yellow sm:mx-0">
                        <x-icon name="package" class="size-6 text-brand-black" />
                    </div>
                    <h3 class="text-lg font-bold text-brand-black">Katalog dari POS</h3>
                    <p class="mt-2 text-sm leading-relaxed text-brand-black/70">
                        Produk, stok, dan harga mengikuti master operasional toko — bukan inventaris spekulatif.
                    </p>
                </div>
                <div class="text-center sm:text-left">
                    <div class="mx-auto mb-4 inline-flex size-12 items-center justify-center rounded-full bg-brand-yellow sm:mx-0">
                        <x-icon name="truck" class="size-6 text-brand-black" />
                    </div>
                    <h3 class="text-lg font-bold text-brand-black">Pengiriman jelas</h3>
                    <p class="mt-2 text-sm leading-relaxed text-brand-black/70">
                        Pilihan kurir toko atau rate pengiriman eksternal saat checkout, dengan total yang dihitung server-side.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="daftar" class="border-t border-gray-200 bg-brand-yellow/25 py-14">
        <div class="mx-auto max-w-6xl px-6 text-center lg:px-8">
            <h2 class="text-2xl font-bold text-brand-black sm:text-3xl">Siap bergabung?</h2>
            <p class="mx-auto mt-3 max-w-xl text-brand-black/70">
                Guest dan akun pending tetap bisa melihat katalog. Harga, checkout, dan riwayat order hanya terbuka setelah akun disetujui admin.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a
                        id="masuk"
                        href="{{ route('account.dashboard') }}"
                        class="inline-flex rounded-full border-2 border-brand-black/15 px-6 py-3 text-sm font-semibold text-brand-black"
                    >
                        Buka akun
                    </a>
                @else
                    <a
                        id="masuk"
                        href="{{ route('login') }}"
                        class="inline-flex rounded-full border-2 border-brand-black/15 px-6 py-3 text-sm font-semibold text-brand-black"
                    >
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex rounded-full bg-brand-yellow/80 px-6 py-3 text-sm font-semibold text-brand-black"
                    >
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <footer class="border-t border-brand-black/10 bg-brand-black py-12 text-brand-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-6 sm:grid-cols-2 lg:grid-cols-4 lg:px-8">
            <div class="sm:col-span-2 lg:col-span-1">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="mb-4 h-10 w-auto brightness-0 invert"
                >
                <p class="text-sm leading-relaxed text-brand-white/70">
                    E-commerce pelanggan terverifikasi untuk aksesoris elektronik, kartu &amp; voucher, dan pulsa.
                </p>
            </div>
            <div>
                <h3 class="mb-3 text-sm font-semibold tracking-wide uppercase">Platform</h3>
                <ul class="space-y-2 text-sm text-brand-white/70">
                    <li><a href="#kategori" class="transition hover:text-brand-yellow">Kategori</a></li>
                    <li><a href="#highlight" class="transition hover:text-brand-yellow">Highlight</a></li>
                    <li><span class="text-brand-white/40">Pengiriman (segera)</span></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-3 text-sm font-semibold tracking-wide uppercase">Legal</h3>
                <ul class="space-y-2 text-sm text-brand-white/70">
                    <li><span class="text-brand-white/40">Kebijakan privasi (segera)</span></li>
                    <li><span class="text-brand-white/40">Syarat layanan (segera)</span></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-3 text-sm font-semibold tracking-wide uppercase">Akun</h3>
                <ul class="space-y-2 text-sm text-brand-white/70">
                    @auth
                        <li><a href="{{ route('account.dashboard') }}" class="transition hover:text-brand-yellow">Akun</a></li>
                    @else
                        <li><a href="{{ route('register') }}" class="transition hover:text-brand-yellow">Daftar</a></li>
                        <li><a href="{{ route('login') }}" class="transition hover:text-brand-yellow">Masuk</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="mx-auto mt-10 max-w-6xl border-t border-brand-white/10 px-6 pt-6 text-sm text-brand-white/50 lg:px-8">
            <p>&copy; {{ date('Y') }} Pixel Komunika</p>
        </div>
    </footer>
</div>
