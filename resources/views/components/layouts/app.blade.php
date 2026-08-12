<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Portal pelanggan terverifikasi Pixel Komunika — aksesoris elektronik, kartu data, dan pulsa.' }}">
        @fonts
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell min-h-screen bg-zinc-100 font-sans text-zinc-950 antialiased">

        {{--
            Internal App Shell — Fixed Left Sidebar + Topbar Layout
            Sidebar remains fixed on desktop while main content scrolls independently.
            User Profile in topbar features an interactive Alpine.js dropdown menu with Logout.
        --}}
        <div
            class="flex min-h-screen"
            x-data="{ sidebarOpen: false }"
            @keydown.escape.window="sidebarOpen = false"
        >

            {{-- =====================================================
                 SIDEBAR OVERLAY (mobile)
                 ===================================================== --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-30 bg-zinc-950/40 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
                x-cloak
                aria-hidden="true"
            ></div>

            {{-- =====================================================
                 SIDEBAR (Fixed on desktop)
                 ===================================================== --}}
            <aside
                class="fixed bottom-4 left-4 top-4 z-40 flex w-[220px] flex-col rounded-[1.5rem] border border-zinc-200 bg-white shadow-card transition-transform duration-200 ease-out lg:translate-x-0 lg:transition-none"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                aria-label="Navigasi aplikasi"
            >
                {{-- Logo --}}
                <div class="flex shrink-0 items-center justify-between gap-3 border-b border-zinc-100 px-4 py-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3" wire:navigate>
                        <img
                            src="{{ asset('assets/brand-logo.png') }}"
                            alt="Pixel Komunika"
                            class="h-8 w-auto"
                            width="112"
                            height="32"
                        >
                    </a>
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-600">
                        admin
                    </span>
                </div>

                {{-- Nav --}}
                <nav class="flex flex-1 flex-col gap-1 overflow-y-auto p-3" aria-label="Navigasi sidebar">
                    <p class="mb-2 mt-1 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-zinc-400">Workspace</p>

                    <a
                        href="{{ route('admin.dashboard') }}"
                        wire:navigate
                        class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    >
                        <x-icon name="layout-dashboard" class="size-4 shrink-0" />
                        <span>Dashboard</span>
                    </a>

                    <a
                        href="{{ route('admin.customers.index') }}"
                        wire:navigate
                        class="sidebar-nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                    >
                        <x-icon name="users" class="size-4 shrink-0" />
                        <span>Customer</span>
                    </a>

                    <a
                        href="{{ route('admin.orders.index') }}"
                        wire:navigate
                        class="sidebar-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                    >
                        <x-icon name="clipboard-list" class="size-4 shrink-0" />
                        <span>Order</span>
                    </a>

                    <a
                        href="{{ route('admin.tax-rules.index') }}"
                        wire:navigate
                        class="sidebar-nav-item {{ request()->routeIs('admin.tax-rules.*') ? 'active' : '' }}"
                    >
                        <x-icon name="percent" class="size-4 shrink-0" />
                        <span>PPh 22</span>
                    </a>

                    {{-- Spacer --}}
                    <div class="flex-1"></div>

                    {{-- Divider --}}
                    <div class="mt-3 border-t border-zinc-100 pt-3">
                        <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-zinc-400">Tautan</p>

                        <a
                            href="{{ route('home') }}"
                            wire:navigate
                            class="sidebar-nav-item"
                        >
                            <x-icon name="store" class="size-4 shrink-0" />
                            <span>Storefront</span>
                        </a>

                        @guest
                            <a href="{{ route('login') }}" class="sidebar-nav-item">
                                <x-icon name="log-in" class="size-4 shrink-0" />
                                <span>Masuk</span>
                            </a>
                        @endguest
                    </div>
                </nav>
            </aside>

            {{-- =====================================================
                 MAIN CONTENT AREA (Offset by fixed sidebar on desktop)
                 ===================================================== --}}
            <div class="flex min-w-0 flex-1 flex-col lg:pl-[252px]">

                {{-- TOPBAR --}}
                <header class="sticky top-0 z-20 flex min-h-[4rem] shrink-0 items-center gap-3 border-b border-zinc-200 bg-white/92 px-4 sm:px-6">

                    {{-- Mobile sidebar toggle --}}
                    <button
                        type="button"
                        class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 lg:hidden"
                        @click="sidebarOpen = true"
                        aria-label="Buka sidebar"
                    >
                        <x-icon name="menu" class="size-4.5" />
                    </button>

                    {{-- Page title from slot --}}
                    <div class="min-w-0">
                        @isset($topbarTitle)
                            <h1 class="truncate text-sm font-semibold text-zinc-800">{{ $topbarTitle }}</h1>
                        @endisset
                        <p class="text-[11px] text-zinc-400">Internal workspace</p>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        {{-- User Dropdown Menu --}}
                        @auth
                            <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                                <button
                                    type="button"
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-2 rounded-2xl border border-zinc-200 bg-white px-2 py-1.5 pr-3 text-left transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-1"
                                >
                                    <div class="inline-flex size-8 items-center justify-center rounded-full bg-amber-400 font-bold text-brand-black shadow-xs text-xs">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden text-left lg:block">
                                        <p class="text-xs font-semibold text-zinc-800 leading-tight">
                                            {{ Str::limit(auth()->user()->name, 20) }}
                                        </p>
                                        <p class="text-[10px] text-zinc-400">
                                            {{ auth()->user()->isAdmin() ? 'Administrator' : 'Customer' }}
                                        </p>
                                    </div>
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
                                    class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-[1.25rem] border border-zinc-200 bg-white p-2 shadow-lg"
                                    x-cloak
                                >
                                    {{-- User info header --}}
                                    <div class="border-b border-zinc-100 px-3 py-2.5">
                                        <p class="text-sm font-semibold text-zinc-950">{{ auth()->user()->name }}</p>
                                        <p class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                                        <div class="mt-2 inline-flex items-center gap-1 rounded-lg border border-amber-200/70 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                            <span>Status:</span>
                                            <span class="uppercase">{{ auth()->user()->isAdmin() ? 'ADMIN' : (auth()->user()->customerStatus() ?: 'PENDING') }}</span>
                                        </div>
                                    </div>

                                    {{-- Logout action --}}
                                    <div class="pt-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                            >
                                                <x-icon name="log-out" class="size-4 text-red-500" />
                                                <span>Keluar (Sign Out)</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </header>

                {{-- PAGE CONTENT --}}
                <main class="flex-1 pb-16">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>
