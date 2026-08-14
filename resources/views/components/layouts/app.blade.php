<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Admin workspace Pixel Komunika.' }}">
        <x-favicon />
        @fonts
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell min-h-screen bg-neutral-50 font-sans text-zinc-950 antialiased">
        <div
            class="flex min-h-screen"
            x-data="{ sidebarOpen: false }"
            @keydown.escape.window="sidebarOpen = false"
        >
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-zinc-950/50 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
                x-cloak
                aria-hidden="true"
            ></div>

            {{-- Dark sidebar (Figma AdminShell parity; logo Laravel) --}}
            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-56 flex-col bg-brand-black transition-transform duration-200 ease-out lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                aria-label="Navigasi admin"
            >
                <div class="flex shrink-0 items-center gap-2.5 border-b border-white/10 px-4 py-4">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="inline-flex items-center gap-2.5">
                        <img
                            src="{{ asset('assets/brand-logo.png') }}"
                            alt="Pixel Komunika"
                            class="h-8 w-auto brightness-0 invert"
                            width="112"
                            height="32"
                        >
                    </a>
                    <span class="rounded-lg bg-brand-yellow px-2 py-0.5 text-[10px] font-black tracking-wide text-brand-black uppercase">Admin</span>
                </div>

                <nav class="flex flex-1 flex-col overflow-y-auto py-3" aria-label="Navigasi sidebar">
                    <p class="px-4 pt-2 pb-1 text-[10px] font-bold tracking-widest text-zinc-500 uppercase">Utama</p>
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <x-icon name="layout-dashboard" class="size-4 shrink-0" />
                        <span>Dashboard</span>
                    </a>

                    <p class="px-4 pt-4 pb-1 text-[10px] font-bold tracking-widest text-zinc-500 uppercase">Penjualan</p>
                    <a href="{{ route('admin.orders.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <x-icon name="shopping-bag" class="size-4 shrink-0" />
                        <span>Pesanan</span>
                    </a>
                    <a href="{{ route('admin.payments.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <x-icon name="credit-card" class="size-4 shrink-0" />
                        <span>Pembayaran</span>
                    </a>

                    <p class="px-4 pt-4 pb-1 text-[10px] font-bold tracking-widest text-zinc-500 uppercase">Katalog</p>
                    <a href="{{ route('admin.products.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <x-icon name="package" class="size-4 shrink-0" />
                        <span>Produk</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <x-icon name="tag" class="size-4 shrink-0" />
                        <span>Kategori</span>
                    </a>
                    <a href="{{ route('admin.brands.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <x-icon name="award" class="size-4 shrink-0" />
                        <span>Merek</span>
                    </a>

                    <p class="px-4 pt-4 pb-1 text-[10px] font-bold tracking-widest text-zinc-500 uppercase">Pelanggan</p>
                    <a href="{{ route('admin.customers.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <x-icon name="users" class="size-4 shrink-0" />
                        <span>Pelanggan</span>
                    </a>

                    <p class="px-4 pt-4 pb-1 text-[10px] font-bold tracking-widest text-zinc-500 uppercase">Analitik</p>
                    <a href="{{ route('admin.reports.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <x-icon name="bar-chart-3" class="size-4 shrink-0" />
                        <span>Laporan</span>
                    </a>

                    <p class="px-4 pt-4 pb-1 text-[10px] font-bold tracking-widest text-zinc-500 uppercase">Sistem</p>
                    <a href="{{ route('admin.tax-rules.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.tax-rules.*') ? 'active' : '' }}">
                        <x-icon name="percent" class="size-4 shrink-0" />
                        <span>PPh 22</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" wire:navigate class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <x-icon name="settings" class="size-4 shrink-0" />
                        <span>Pengaturan</span>
                    </a>
                </nav>

                <div class="shrink-0 border-t border-white/10 p-4">
                    @auth
                        <div class="mb-3 flex items-center gap-2.5">
                            <div class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-brand-yellow text-xs font-bold text-brand-black">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[10px] text-zinc-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    @endauth
                    <a href="{{ route('home') }}" wire:navigate class="admin-nav-item mx-0">
                        <x-icon name="store" class="size-4 shrink-0" />
                        <span>Storefront</span>
                    </a>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col lg:pl-56">
                <header class="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-3 border-b border-neutral-100 bg-white px-4 sm:px-6">
                    <button
                        type="button"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 lg:hidden"
                        @click="sidebarOpen = true"
                        aria-label="Buka sidebar"
                    >
                        <x-icon name="menu" class="size-4.5" />
                    </button>

                    <div class="relative hidden max-w-sm flex-1 sm:block">
                        <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-3.5 -translate-y-1/2 text-zinc-400" />
                        <input
                            type="search"
                            disabled
                            placeholder="Cari pelanggan, produk, pesanan..."
                            class="w-full cursor-not-allowed rounded-lg border border-neutral-200 bg-neutral-50 py-1.5 pr-3 pl-8 text-xs text-zinc-500"
                            title="Pencarian global segera hadir"
                        >
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            type="button"
                            disabled
                            class="relative inline-flex size-8 cursor-not-allowed items-center justify-center rounded-lg text-zinc-400"
                            title="Notifikasi segera hadir"
                            aria-label="Notifikasi"
                        >
                            <x-icon name="bell" class="size-4" />
                            <span class="absolute top-1.5 right-1.5 size-1.5 rounded-full bg-brand-red"></span>
                        </button>

                        @auth
                            <div class="relative border-l border-neutral-200 pl-2" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                                <button
                                    type="button"
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-2 rounded-xl px-1.5 py-1 text-left transition hover:bg-zinc-50"
                                >
                                    <div class="inline-flex size-7 items-center justify-center rounded-full bg-brand-yellow text-xs font-bold text-brand-black">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <span class="hidden text-xs font-semibold text-zinc-700 sm:block">{{ Str::limit(auth()->user()->name, 18) }}</span>
                                    <x-icon name="chevron-down" class="size-3.5 text-zinc-400" />
                                </button>

                                <div
                                    x-show="userMenuOpen"
                                    x-cloak
                                    x-transition
                                    class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-2xl border border-zinc-200 bg-white p-2 shadow-lg"
                                >
                                    <div class="border-b border-zinc-100 px-3 py-2.5">
                                        <p class="text-sm font-semibold text-zinc-950">{{ auth()->user()->name }}</p>
                                        <p class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                            <x-icon name="log-out" class="size-4 text-red-500" />
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>
                </header>

                <main class="flex-1 pb-16">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>
