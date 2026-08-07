@props([
    'cartCount' => 0,
])

<header
    class="sticky top-0 z-40 border-b border-brand-black/8 bg-brand-white/95 backdrop-blur-md"
    x-data="{ scrolled: false, mobileMenu: false }"
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

            {{-- Desktop Navigation Links --}}
            <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                <a
                    href="{{ route('products.index') }}"
                    wire:navigate
                    class="rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('products.*') ? 'bg-amber-400/20 text-amber-900 font-bold' : 'text-brand-black/75 hover:bg-brand-black/5 hover:text-brand-black' }}"
                >
                    Katalog Produk
                </a>

                <a
                    href="{{ route('products.index') }}#kategori"
                    wire:navigate
                    class="rounded-lg px-3.5 py-2 text-sm font-medium text-brand-black/75 transition-colors hover:bg-brand-black/5 hover:text-brand-black"
                >
                    Kategori
                </a>

                <a
                    href="{{ route('home') }}#unggulan"
                    wire:navigate
                    class="rounded-lg px-3.5 py-2 text-sm font-medium text-brand-black/75 transition-colors hover:bg-brand-black/5 hover:text-brand-black"
                >
                    Keunggulan
                </a>
            </nav>

            {{-- Right Actions Area --}}
            <div class="ml-auto flex items-center gap-2 sm:gap-3">

                {{-- Global Search Bar (Desktop) --}}
                <form action="{{ route('products.index') }}" method="GET" class="relative hidden lg:block">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-brand-black/40" />
                    <input
                        type="search"
                        name="cari"
                        value="{{ request('cari') }}"
                        placeholder="Cari katalog & SKU..."
                        class="w-52 rounded-full border border-brand-black/15 bg-zinc-50 py-2 pr-4 pl-9 text-xs text-brand-black transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 xl:w-64"
                    >
                </form>

                {{-- Cart Trigger Button --}}
                <button
                    type="button"
                    @click="$dispatch('open-cart-drawer')"
                    class="relative inline-flex size-10 items-center justify-center rounded-full text-brand-black/75 transition hover:bg-brand-black/5 hover:text-brand-black cursor-pointer"
                    aria-label="Keranjang belanja"
                >
                    <x-icon name="shopping-cart" class="size-5" />
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 inline-flex size-5 items-center justify-center rounded-full bg-amber-400 text-[11px] font-extrabold text-brand-black shadow-xs">
                            {{ $cartCount }}
                        </span>
                    @endif
                </button>

                {{-- User Avatar Dropdown (Authenticated) / Auth Links (Guest) --}}
                @auth
                    <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                        <button
                            type="button"
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 rounded-full border border-zinc-200/80 bg-zinc-50 p-1.5 pr-3 text-left transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400"
                        >
                            <div class="inline-flex size-7 items-center justify-center rounded-full bg-amber-400 font-extrabold text-brand-black text-xs shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden text-xs font-bold text-zinc-800 lg:block max-w-[120px] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <x-icon name="chevron-down" class="size-3.5 text-zinc-400 transition-transform duration-150" ::class="userMenuOpen ? 'rotate-180' : ''" />
                        </button>

                        {{-- Dropdown Card --}}
                        <div
                            x-show="userMenuOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-60 origin-top-right rounded-2xl border border-zinc-200 bg-white p-2 shadow-xl z-50"
                            x-cloak
                        >
                            {{-- Header User Info --}}
                            <div class="border-b border-zinc-100 px-3 py-2.5">
                                <p class="text-xs font-bold text-zinc-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[11px] text-zinc-400 mt-0.5">{{ auth()->user()->email }}</p>
                                <div class="mt-2 inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800 border border-amber-200/60">
                                    <span>Status:</span>
                                    <span class="uppercase">{{ auth()->user()->isAdmin() ? 'ADMIN' : (auth()->user()->customerStatus() ?: 'PENDING') }}</span>
                                </div>
                            </div>

                            {{-- Navigation Menu --}}
                            <div class="py-1.5 space-y-0.5">
                                <a
                                    href="{{ route('account.dashboard') }}"
                                    wire:navigate
                                    class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100"
                                >
                                    <x-icon name="user-circle" class="size-4 text-zinc-400" />
                                    <span>Dashboard Akun</span>
                                </a>

                                @if (auth()->user()->isActiveCustomer())
                                    <a
                                        href="{{ route('orders.index') }}"
                                        wire:navigate
                                        class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100"
                                    >
                                        <x-icon name="package" class="size-4 text-zinc-400" />
                                        <span>Riwayat Order</span>
                                    </a>

                                    <a
                                        href="{{ route('checkout.index') }}"
                                        wire:navigate
                                        class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100"
                                    >
                                        <x-icon name="shopping-bag" class="size-4 text-zinc-400" />
                                        <span>Checkout</span>
                                    </a>
                                @endif

                                @if (auth()->user()->isAdmin())
                                    <a
                                        href="{{ route('admin.customers.index') }}"
                                        wire:navigate
                                        class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                                    >
                                        <x-icon name="shield-check" class="size-4 text-amber-600" />
                                        <span>Panel Admin</span>
                                    </a>
                                @endif
                            </div>

                            {{-- Sign Out Button --}}
                            <div class="border-t border-zinc-100 pt-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50"
                                    >
                                        <x-icon name="log-out" class="size-4 text-red-500" />
                                        <span>Keluar (Sign Out)</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="hidden text-xs font-bold text-brand-black/75 transition hover:text-brand-black sm:inline"
                    >
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center gap-1 rounded-full bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-soft shadow-xs"
                    >
                        Daftar
                    </a>
                @endauth

                {{-- Mobile Menu Toggle --}}
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

    {{-- Mobile Dropdown Drawer Menu --}}
    <div
        x-show="mobileMenu"
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-t border-brand-black/8 bg-brand-white md:hidden"
        x-cloak
        @click.outside="mobileMenu = false"
    >
        <nav class="container-2xl grid gap-1 py-3">
            <a
                href="{{ route('products.index') }}"
                wire:navigate
                @click="mobileMenu = false"
                class="rounded-lg px-3 py-2.5 text-sm font-semibold text-brand-black/80 hover:bg-brand-black/5"
            >
                Katalog Produk
            </a>
            <a
                href="{{ route('products.index') }}#kategori"
                wire:navigate
                @click="mobileMenu = false"
                class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5"
            >
                Kategori
            </a>
            <a
                href="{{ route('home') }}#unggulan"
                wire:navigate
                @click="mobileMenu = false"
                class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5"
            >
                Keunggulan
            </a>

            <div class="my-2 border-t border-brand-black/8"></div>

            @auth
                <a href="{{ route('account.dashboard') }}" wire:navigate class="rounded-lg px-3 py-2.5 text-sm font-bold text-zinc-900 hover:bg-zinc-100">Dashboard Akun</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-1">
                    @csrf
                    <button type="submit" class="w-full text-left rounded-lg px-3 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Sign Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-brand-black/70 hover:bg-brand-black/5">Masuk</a>
                <a href="{{ route('register') }}" class="mt-1 inline-flex w-full justify-center rounded-full bg-brand-yellow px-4 py-2.5 text-sm font-bold text-brand-black">Daftar Akun</a>
            @endauth
        </nav>
    </div>
</header>
