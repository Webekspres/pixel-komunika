@props([
    'cartCount' => 0,
    'categories' => collect(),
])

{{-- Brand assets: brand-logo.png (Laravel) — not Figma text-mark --}}
{{-- Store contact matches footer WhatsApp (0815-4640-7702) --}}
<header
    x-ref="storefrontHeader"
    class="sticky top-0 z-50 w-full border-b border-zinc-100 bg-white shadow-[0_1px_3px_rgb(0_0_0/0.06)]"
    x-data="{
        mobileMenu: false,
        announceVisible: true,
        lastY: 0,
        measureHeader() {
            this.\$nextTick(() => {
                const h = this.\$refs.storefrontHeader?.offsetHeight ?? 0;
                document.documentElement.style.setProperty('--storefront-header-height', h + 'px');
            });
        },
        onScroll() {
            const y = window.scrollY || 0;
            if (y < 24) {
                this.announceVisible = true;
            } else if (y > this.lastY + 4) {
                this.announceVisible = false;
            } else if (y < this.lastY - 4) {
                this.announceVisible = true;
            }
            this.lastY = y;
        },
    }"
    x-init="
        measureHeader();
        \$watch('announceVisible', () => measureHeader());
        new ResizeObserver(() => measureHeader()).observe(\$refs.storefrontHeader);
    "
    @scroll.window="onScroll()"
>
    {{-- Announcement / CTA bar — hide on scroll down, show on scroll up --}}
    <div
        x-show="announceVisible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="w-full overflow-hidden bg-brand-black"
        @transitionend="measureHeader()"
    >
        <div class="flex items-center justify-center gap-2 px-4 py-2 text-center text-xs font-medium text-white">
            <x-icon name="party-popper" class="size-3.5 shrink-0 text-brand-yellow" />
            <span>
                Harga spesial untuk pelanggan terverifikasi!
                <a href="{{ route('register') }}" class="ml-1 font-semibold underline transition-colors hover:text-brand-yellow">Daftar sekarang</a>
            </span>
        </div>
    </div>

    <nav class="container-2xl" aria-label="Navigasi utama">
        {{-- Main row: logo | search | cart+akun --}}
        <div class="flex h-[4.5rem] items-center gap-3 border-b border-zinc-100 sm:h-20 sm:gap-4">
            <button
                type="button"
                class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-50 md:hidden"
                @click="mobileMenu = !mobileMenu"
                :aria-expanded="mobileMenu"
                aria-label="Buka menu"
            >
                <x-icon x-show="!mobileMenu" name="menu" class="size-5" />
                <x-icon x-show="mobileMenu" name="x" class="size-5" x-cloak />
            </button>

            <a href="{{ route('home') }}" wire:navigate class="shrink-0" aria-label="Pixel Komunika beranda">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-12 w-auto sm:h-14"
                    width="224"
                    height="56"
                >
            </a>

            <form action="{{ route('products.index') }}" method="GET" class="relative mx-auto hidden min-w-0 max-w-2xl flex-1 md:block">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-zinc-400" />
                <input
                    type="search"
                    name="cari"
                    value="{{ request('cari') }}"
                    placeholder="Cari charger, kabel data, headset, power bank..."
                    class="w-full rounded-xl border-0 bg-zinc-50 py-2.5 pr-4 pl-10 text-sm text-brand-black transition focus:bg-white focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                >
            </form>

            <div class="ml-auto flex shrink-0 items-center gap-1">
                <button
                    type="button"
                    @click="$dispatch('open-cart-drawer')"
                    class="relative inline-flex size-10 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-50"
                    aria-label="Keranjang belanja"
                >
                    <x-icon name="shopping-cart" class="size-5" />
                    @if ($cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 inline-flex size-5 items-center justify-center rounded-full bg-brand-red text-[10px] font-bold text-white">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    @endif
                </button>

                @auth
                    <div class="relative hidden sm:block" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                        <button
                            type="button"
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-zinc-50"
                        >
                            <div class="inline-flex size-7 items-center justify-center rounded-full bg-brand-yellow text-xs font-bold text-brand-black">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="max-w-[100px] truncate text-sm font-medium text-zinc-700">{{ auth()->user()->name }}</span>
                            <x-icon name="chevron-down" class="size-3.5 text-zinc-400" />
                        </button>

                        <div
                            x-show="userMenuOpen"
                            x-cloak
                            x-transition
                            class="absolute right-0 z-50 mt-1 w-48 rounded-2xl border border-zinc-100 bg-white py-1.5 shadow-lg"
                        >
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50">
                                    <x-icon name="shield" class="size-4 text-zinc-400" />
                                    Admin
                                </a>
                            @else
                                <a href="{{ route('account.dashboard') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50">
                                    <x-icon name="user" class="size-4 text-zinc-400" />
                                    Akun Saya
                                </a>
                            @endif
                            @if (auth()->user()->isActiveCustomer())
                                <a href="{{ route('orders.index') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50">
                                    <x-icon name="package" class="size-4 text-zinc-400" />
                                    Pesanan Saya
                                </a>
                                <a href="{{ route('account.addresses.index') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50">
                                    <x-icon name="map-pin" class="size-4 text-zinc-400" />
                                    Alamat
                                </a>
                            @endif
                            <div class="my-1 border-t border-zinc-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50">
                                    <x-icon name="log-out" class="size-4" />
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden items-center gap-2 sm:flex">
                        <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 hover:text-zinc-900">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-dark">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Category row: kategori kiri + telepon kanan --}}
        <div class="hidden h-10 items-center justify-between gap-4 lg:flex">
            <div class="-mx-1 flex min-w-0 flex-1 items-center gap-0 overflow-x-auto">
                @if ($categories->isNotEmpty())
                    <a
                        href="{{ route('products.index') }}"
                        wire:navigate
                        class="shrink-0 whitespace-nowrap rounded-lg px-3 py-1.5 text-[13px] font-medium transition-colors {{ request()->routeIs('products.index') && (! request()->filled('kategori') || request('kategori') === 'all') ? 'bg-brand-yellow/10 font-semibold text-brand-black' : 'text-zinc-600 hover:bg-zinc-50 hover:text-brand-black' }}"
                    >
                        Semua Produk
                    </a>
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('products.index', ['kategori' => $category->id]) }}"
                            wire:navigate
                            class="shrink-0 whitespace-nowrap rounded-lg px-3 py-1.5 text-[13px] font-medium transition-colors {{ request('kategori') == $category->id ? 'bg-brand-yellow/10 font-semibold text-brand-black' : 'text-zinc-600 hover:bg-zinc-50 hover:text-brand-black' }}"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                @endif
            </div>

            <div class="flex shrink-0 items-center gap-3">
                <a
                    href="https://wa.me/6281546407702"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-2 text-[13px] font-medium text-zinc-600 transition-colors hover:text-brand-black"
                >
                    <x-icon name="phone" class="size-3.5 shrink-0 text-brand-yellow" />
                    <span>0815-4640-7702</span>
                </a>

                <div class="flex items-center gap-1 border-l border-zinc-100 pl-3" aria-label="Sosial media">
                    <a
                        href="https://www.instagram.com/pixelkomunika/"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-[#E4405F] transition hover:bg-[#E4405F]/10"
                        aria-label="Instagram Pixel Komunika"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a
                        href="https://www.youtube.com/watch?v=hw5eJZovY3A"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-[#FF0000] transition hover:bg-[#FF0000]/10"
                        aria-label="YouTube Pixel Komunika"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a
                        href="https://www.tiktok.com/@pixelkomunika/"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-[#010101] transition hover:bg-zinc-100"
                        aria-label="TikTok Pixel Komunika"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.76-1.61.26-.45.35-.97.4-1.48.1-1.81.06-3.63.07-5.44.01-4.16-.01-8.33.01-12.5z"/></svg>
                    </a>
                    <a
                        href="https://www.facebook.com/pixelkomunikabdg/"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-[#1877F2] transition hover:bg-[#1877F2]/10"
                        aria-label="Facebook Pixel Komunika"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div
        x-show="mobileMenu"
        x-cloak
        x-transition
        class="space-y-3 border-t border-zinc-100 bg-white px-4 py-4 md:hidden"
        @click.outside="mobileMenu = false"
    >
        <form action="{{ route('products.index') }}" method="GET" class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
            <input
                type="search"
                name="cari"
                placeholder="Cari produk..."
                class="w-full rounded-xl border-0 bg-zinc-50 py-2.5 pr-4 pl-9 text-sm focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
            >
        </form>

        @guest
            <div class="flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 rounded-xl border-2 border-zinc-200 px-4 py-2.5 text-center text-sm font-semibold text-zinc-700">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 rounded-xl bg-brand-yellow px-4 py-2.5 text-center text-sm font-semibold text-brand-black">Daftar</a>
            </div>
        @endguest

        @if ($categories->isNotEmpty())
            <div class="grid grid-cols-2 gap-1.5">
                <a
                    href="{{ route('products.index') }}"
                    wire:navigate
                    @click="mobileMenu = false"
                    class="rounded-xl bg-zinc-50 px-3 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100"
                >
                    Semua Produk
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('products.index', ['kategori' => $category->id]) }}"
                        wire:navigate
                        @click="mobileMenu = false"
                        class="rounded-xl bg-zinc-50 px-3 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <a
            href="https://wa.me/6281546407702"
            target="_blank"
            rel="noopener"
            class="flex items-center gap-2.5 rounded-xl bg-zinc-50 px-3 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100"
        >
            <x-icon name="phone" class="size-4 shrink-0 text-brand-yellow" />
            0815-4640-7702
        </a>

        <div class="grid grid-cols-4 gap-1.5" aria-label="Sosial media">
            <a
                href="https://www.instagram.com/pixelkomunika/"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#E4405F]/10 px-2 py-2.5 text-[#E4405F] transition hover:bg-[#E4405F]/20"
                aria-label="Instagram Pixel Komunika"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
            <a
                href="https://www.youtube.com/watch?v=hw5eJZovY3A"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#FF0000]/10 px-2 py-2.5 text-[#FF0000] transition hover:bg-[#FF0000]/20"
                aria-label="YouTube Pixel Komunika"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
            <a
                href="https://www.tiktok.com/@pixelkomunika/"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-zinc-100 px-2 py-2.5 text-[#010101] transition hover:bg-zinc-200"
                aria-label="TikTok Pixel Komunika"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.76-1.61.26-.45.35-.97.4-1.48.1-1.81.06-3.63.07-5.44.01-4.16-.01-8.33.01-12.5z"/></svg>
            </a>
            <a
                href="https://www.facebook.com/pixelkomunikabdg/"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#1877F2]/10 px-2 py-2.5 text-[#1877F2] transition hover:bg-[#1877F2]/20"
                aria-label="Facebook Pixel Komunika"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
            </a>
        </div>

        @auth
            <div class="space-y-1 border-t border-zinc-100 pt-3">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="block rounded-lg px-3 py-2.5 text-sm font-semibold">Dashboard Admin</a>
                @else
                    <a href="{{ route('account.dashboard') }}" wire:navigate class="block rounded-lg px-3 py-2.5 text-sm font-semibold">Akun Saya</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-bold text-red-600">Keluar</button>
                </form>
            </div>
        @endauth
    </div>
</header>
