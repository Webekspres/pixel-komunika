@props([
    'cartCount' => 0,
    'categories' => collect(),
])

{{-- Brand assets: brand-logo.png (Laravel) — not Figma text-mark --}}
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
    {{-- Announcement bar — hide on scroll down, show on scroll up --}}
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
        <div class="flex h-16 items-center gap-4">
            <a href="{{ route('home') }}" wire:navigate class="shrink-0" aria-label="Pixel Komunika beranda">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-9 w-auto sm:h-10"
                    width="160"
                    height="40"
                >
            </a>

            <form action="{{ route('products.index') }}" method="GET" class="relative hidden max-w-2xl flex-1 md:block">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-zinc-400" />
                <input
                    type="search"
                    name="cari"
                    value="{{ request('cari') }}"
                    placeholder="Cari charger, kabel data, headset, power bank..."
                    class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pr-4 pl-10 text-sm text-brand-black transition focus:border-brand-yellow focus:bg-white focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                >
            </form>

            <div class="ml-auto flex items-center gap-1">
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

                <button
                    type="button"
                    class="inline-flex size-10 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-50 md:hidden"
                    @click="mobileMenu = !mobileMenu"
                    :aria-expanded="mobileMenu"
                    aria-label="Buka menu"
                >
                    <x-icon x-show="!mobileMenu" name="menu" class="size-5" />
                    <x-icon x-show="mobileMenu" name="x" class="size-5" x-cloak />
                </button>
            </div>
        </div>

        @if ($categories->isNotEmpty())
            <div class="hidden h-10 items-center gap-0 overflow-x-auto lg:flex -mx-1">
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
            </div>
        @endif
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
                class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pr-4 pl-9 text-sm focus:border-brand-yellow focus:outline-none"
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
