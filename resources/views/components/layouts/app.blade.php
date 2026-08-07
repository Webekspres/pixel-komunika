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
    <body class="min-h-screen bg-zinc-50 font-sans text-zinc-950 antialiased">

        {{--
            Internal App Shell — Sidebar + Topbar Layout
            Similar feel to Linear / Vercel / Laravel Cloud dashboards.
            Mobile: sidebar becomes a drawer controlled by Alpine.
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
                 SIDEBAR
                 ===================================================== --}}
            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-[240px] flex-col border-r border-zinc-200 bg-white transition-transform duration-200 ease-out lg:static lg:translate-x-0 lg:transition-none"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                aria-label="Navigasi aplikasi"
            >
                {{-- Logo --}}
                <div class="flex h-14 shrink-0 items-center gap-2 border-b border-zinc-100 px-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2" wire:navigate>
                        <img
                            src="{{ asset('assets/brand-logo.png') }}"
                            alt="Pixel Komunika"
                            class="h-7 w-auto"
                            width="100"
                            height="28"
                        >
                    </a>
                </div>

                {{-- Nav --}}
                <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto p-3" aria-label="Navigasi sidebar">

                    @auth
                        {{-- Customer nav --}}
                        @if (auth()->user()->isActiveCustomer())
                            <p class="mt-1 mb-1.5 px-3 text-[10px] font-semibold tracking-widest text-zinc-400 uppercase">Pelanggan</p>

                            <a
                                href="{{ route('account.dashboard') }}"
                                wire:navigate
                                class="sidebar-nav-item {{ request()->routeIs('account.*') ? 'active' : '' }}"
                            >
                                <x-icon name="layout-dashboard" class="size-4 shrink-0" />
                                <span>Dashboard</span>
                            </a>

                            <a
                                href="{{ route('orders.index') }}"
                                wire:navigate
                                class="sidebar-nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                            >
                                <x-icon name="package" class="size-4 shrink-0" />
                                <span>Order saya</span>
                            </a>

                            <a
                                href="{{ route('checkout.index') }}"
                                wire:navigate
                                class="sidebar-nav-item {{ request()->routeIs('checkout.*') ? 'active' : '' }}"
                            >
                                <x-icon name="shopping-cart" class="size-4 shrink-0" />
                                <span>Checkout</span>
                            </a>
                        @elseif (auth()->user()->isCustomer())
                            {{-- Pending customer --}}
                            <p class="mt-1 mb-1.5 px-3 text-[10px] font-semibold tracking-widest text-zinc-400 uppercase">Pelanggan</p>

                            <a
                                href="{{ route('account.dashboard') }}"
                                wire:navigate
                                class="sidebar-nav-item {{ request()->routeIs('account.*') ? 'active' : '' }}"
                            >
                                <x-icon name="layout-dashboard" class="size-4 shrink-0" />
                                <span>Akun saya</span>
                            </a>
                        @endif

                        {{-- Admin nav --}}
                        @if (auth()->user()->isAdmin())
                            <p class="mt-3 mb-1.5 px-3 text-[10px] font-semibold tracking-widest text-zinc-400 uppercase">Admin</p>

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
                        @endif

                        {{-- Shared / account --}}
                        <p class="mt-3 mb-1.5 px-3 text-[10px] font-semibold tracking-widest text-zinc-400 uppercase">Akun</p>

                        <a
                            href="{{ route('account.dashboard') }}"
                            wire:navigate
                            class="sidebar-nav-item"
                        >
                            <x-icon name="user-circle" class="size-4 shrink-0" />
                            <span>Profil</span>
                        </a>
                    @endauth

                    {{-- Spacer --}}
                    <div class="flex-1"></div>

                    {{-- Divider --}}
                    <div class="border-t border-zinc-100 pt-2 mt-2">
                        <a
                            href="{{ route('home') }}"
                            wire:navigate
                            class="sidebar-nav-item"
                        >
                            <x-icon name="store" class="size-4 shrink-0" />
                            <span>Storefront</span>
                        </a>

                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="sidebar-nav-item w-full text-left text-brand-red/70 hover:bg-red-50 hover:text-brand-red"
                                >
                                    <x-icon name="log-out" class="size-4 shrink-0" />
                                    <span>Logout</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="sidebar-nav-item">
                                <x-icon name="log-in" class="size-4 shrink-0" />
                                <span>Masuk</span>
                            </a>
                        @endauth
                    </div>
                </nav>
            </aside>

            {{-- =====================================================
                 MAIN CONTENT AREA
                 ===================================================== --}}
            <div class="flex flex-1 flex-col min-w-0">

                {{-- TOPBAR --}}
                <header class="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-3 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur-sm sm:px-6">

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
                    @isset($topbarTitle)
                        <h1 class="truncate text-sm font-semibold text-zinc-800">{{ $topbarTitle }}</h1>
                    @endisset

                    <div class="ml-auto flex items-center gap-2">
                        {{-- User info --}}
                        @auth
                            <div class="hidden items-center gap-2 sm:flex">
                                <div class="inline-flex size-7 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden text-xs font-medium text-zinc-600 lg:block">
                                    {{ Str::limit(auth()->user()->name, 20) }}
                                </span>
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
