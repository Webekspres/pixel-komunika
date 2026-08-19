@props([
    'categories' => collect(),
])

@php
    $mobileVisibleCategories = $categories->take(2);
    $mobileOverflowCategories = $categories->slice(2)->values();
@endphp

{{-- Brand assets: brand-logo.png (Laravel) — not Figma text-mark --}}
{{-- Store contact matches footer WhatsApp (0815-4640-7702) --}}
<header
    x-ref="storefrontHeader"
    class="sticky top-0 z-50 w-full border-b border-zinc-100 bg-white shadow-[0_1px_3px_rgb(0_0_0/0.06)]"
    x-data="{
        mobileSearchOpen: false,
        categoryMoreOpen: false,
        measureHeader() {
            this.\$nextTick(() => {
                const h = this.\$refs.storefrontHeader?.offsetHeight ?? 0;
                document.documentElement.style.setProperty('--storefront-header-height', h + 'px');
            });
        },
        toggleMobileSearch() {
            this.mobileSearchOpen = !this.mobileSearchOpen;
            this.categoryMoreOpen = false;
            if (this.mobileSearchOpen) {
                this.\$nextTick(() => this.\$refs.mobileSearchInput?.focus());
            }
            this.measureHeader();
        },
    }"
    x-init="
        measureHeader();
        \$watch('mobileSearchOpen', () => measureHeader());
        new ResizeObserver(() => measureHeader()).observe(\$refs.storefrontHeader);
    "
>
    {{-- Guest-only register CTA — no scroll hide/show --}}
    @guest
        <div class="w-full overflow-hidden bg-brand-black">
            <div class="flex items-center justify-center gap-2 px-4 py-2 text-center text-xs font-medium text-white">
                <x-icon name="party-popper" class="size-3.5 shrink-0 text-brand-yellow" />
                <span>
                    Harga spesial untuk pelanggan terverifikasi!
                    <a href="{{ route('register') }}" class="ml-1 font-semibold underline transition-colors hover:text-brand-yellow">Daftar sekarang</a>
                </span>
            </div>
        </div>
    @endguest

    <nav class="container-2xl" aria-label="Navigasi utama">
        {{-- Main row: logo | desktop search | actions --}}
        <div class="flex h-[4.5rem] items-center gap-3 border-b border-zinc-100 sm:h-20 sm:gap-4">
            <a href="{{ route('home') }}" wire:navigate class="shrink-0" aria-label="Pixel Komunika beranda">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-12 w-auto sm:h-14"
                    width="224"
                    height="56"
                >
            </a>

            <form action="{{ route('products.index') }}" method="GET" class="relative hidden min-w-0 max-w-2xl flex-1 md:block">
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
                {{-- Mobile search toggle (left of cart) --}}
                <button
                    type="button"
                    class="inline-flex size-10 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-50 hover:text-brand-yellow md:hidden"
                    @click="toggleMobileSearch()"
                    :aria-expanded="mobileSearchOpen"
                    aria-label="Cari produk"
                >
                    <x-icon x-show="!mobileSearchOpen" name="search" class="size-5" />
                    <x-icon x-show="mobileSearchOpen" name="x" class="size-5" x-cloak />
                </button>

                <livewire:storefront.cart-badge />

                @auth
                    <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                        <button
                            type="button"
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 rounded-xl px-2 py-2 transition hover:bg-zinc-50 sm:px-3"
                        >
                            <div class="inline-flex size-7 items-center justify-center rounded-full bg-brand-yellow text-xs font-bold text-brand-black">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden max-w-[100px] truncate text-sm font-medium text-zinc-700 transition-colors hover:text-brand-yellow sm:inline">{{ auth()->user()->name }}</span>
                            <x-icon name="chevron-down" class="hidden size-3.5 text-zinc-400 sm:inline" />
                        </button>

                        <div
                            x-show="userMenuOpen"
                            x-cloak
                            x-transition
                            class="absolute right-0 z-50 mt-1 w-48 rounded-2xl border border-zinc-100 bg-white py-1.5 shadow-lg"
                        >
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50 hover:text-brand-yellow">
                                    <x-icon name="shield" class="size-4 text-zinc-400" />
                                    Admin
                                </a>
                            @else
                                <a href="{{ route('account.dashboard') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50 hover:text-brand-yellow">
                                    <x-icon name="user" class="size-4 text-zinc-400" />
                                    Akun Saya
                                </a>
                            @endif
                            @if (auth()->user()->isActiveCustomer())
                                <a href="{{ route('orders.index') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50 hover:text-brand-yellow">
                                    <x-icon name="package" class="size-4 text-zinc-400" />
                                    Pesanan Saya
                                </a>
                                <a href="{{ route('account.addresses.index') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-zinc-50 hover:text-brand-yellow">
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
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex size-10 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-50 hover:text-brand-yellow sm:hidden"
                        aria-label="Masuk"
                    >
                        <x-icon name="user" class="size-5" />
                    </a>
                    <div class="hidden items-center gap-2 sm:flex">
                        <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-zinc-700 transition-colors hover:text-brand-yellow">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-dark">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Mobile search row: only when search icon toggled --}}
        <div
            x-show="mobileSearchOpen"
            x-cloak
            x-transition
            class="border-b border-zinc-100 py-3 md:hidden"
            @transitionend="measureHeader()"
        >
            <form action="{{ route('products.index') }}" method="GET" class="relative flex gap-2">
                <div class="relative min-w-0 flex-1">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        x-ref="mobileSearchInput"
                        type="search"
                        name="cari"
                        value="{{ request('cari') }}"
                        placeholder="Cari di semua kategori..."
                        class="w-full rounded-xl border-0 bg-zinc-50 py-2.5 pr-4 pl-9 text-sm text-brand-black transition focus:bg-white focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                    >
                </div>
                <button
                    type="submit"
                    class="shrink-0 rounded-xl bg-brand-yellow px-4 py-2.5 text-sm font-bold text-brand-black transition hover:bg-brand-yellow-dark"
                >
                    Cari
                </button>
            </form>
        </div>

        {{-- Mobile category row (default, like Flowbite image 4) --}}
        <div
            x-show="!mobileSearchOpen"
            class="relative flex h-10 items-center gap-1 overflow-visible lg:hidden"
            @click.outside="categoryMoreOpen = false"
        >
            <a
                href="{{ route('products.index') }}"
                wire:navigate
                class="shrink-0 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[13px] font-medium transition-colors {{ request()->routeIs('products.index') && (! request()->filled('kategori') || request('kategori') === 'all') ? 'bg-brand-yellow/10 font-semibold text-brand-black' : 'text-zinc-600 hover:text-brand-yellow' }}"
            >
                Semua Produk
            </a>
            @foreach ($mobileVisibleCategories as $category)
                <a
                    href="{{ route('products.index', ['kategori' => $category->id]) }}"
                    wire:navigate
                    class="shrink-0 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[13px] font-medium transition-colors {{ request('kategori') == $category->id ? 'bg-brand-yellow/10 font-semibold text-brand-black' : 'text-zinc-600 hover:text-brand-yellow' }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach

            @if ($mobileOverflowCategories->isNotEmpty())
                <div class="relative shrink-0">
                    <button
                        type="button"
                        @click="categoryMoreOpen = !categoryMoreOpen"
                        :aria-expanded="categoryMoreOpen"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-600 transition hover:bg-zinc-50 hover:text-brand-yellow"
                        :class="categoryMoreOpen && 'bg-zinc-100 text-brand-black'"
                        aria-label="Kategori lainnya"
                    >
                        <x-icon name="ellipsis" class="size-5" />
                    </button>

                    <div
                        x-show="categoryMoreOpen"
                        x-cloak
                        x-transition
                        class="absolute top-full right-0 z-50 mt-1 min-w-[12rem] rounded-2xl border border-zinc-100 bg-white py-1.5 shadow-lg"
                    >
                        @foreach ($mobileOverflowCategories as $category)
                            <a
                                href="{{ route('products.index', ['kategori' => $category->id]) }}"
                                wire:navigate
                                @click="categoryMoreOpen = false"
                                class="block px-4 py-2.5 text-sm text-zinc-700 transition hover:bg-zinc-50 hover:text-brand-yellow"
                            >
                                {{ $category->name }}
                            </a>
                        @endforeach
                        <div class="my-1 border-t border-zinc-100"></div>
                        <a
                            href="{{ route('products.index') }}"
                            wire:navigate
                            @click="categoryMoreOpen = false"
                            class="block px-4 py-2.5 text-sm font-semibold text-zinc-800 transition hover:bg-zinc-50 hover:text-brand-yellow"
                        >
                            Lihat semua kategori
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Desktop category row: kategori + telepon/sosmed --}}
        <div class="hidden h-10 items-center justify-between gap-4 lg:flex">
            <div class="-mx-1 flex min-w-0 flex-1 items-center gap-0 overflow-x-auto">
                <a
                    href="{{ route('products.index') }}"
                    wire:navigate
                    class="shrink-0 whitespace-nowrap rounded-lg px-3 py-1.5 text-[13px] font-medium transition-colors {{ request()->routeIs('products.index') && (! request()->filled('kategori') || request('kategori') === 'all') ? 'bg-brand-yellow/10 font-semibold text-brand-black' : 'text-zinc-600 hover:text-brand-yellow' }}"
                >
                    Semua Produk
                </a>
                @if ($categories->isNotEmpty())
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('products.index', ['kategori' => $category->id]) }}"
                            wire:navigate
                            class="shrink-0 whitespace-nowrap rounded-lg px-3 py-1.5 text-[13px] font-medium transition-colors {{ request('kategori') == $category->id ? 'bg-brand-yellow/10 font-semibold text-brand-black' : 'text-zinc-600 hover:text-brand-yellow' }}"
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
                    class="inline-flex items-center gap-2 text-[13px] font-medium text-zinc-600 transition-colors hover:text-brand-yellow"
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
</header>
