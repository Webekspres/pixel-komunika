<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Portal pelanggan Pixel Komunika yang terintegrasi dengan pengalaman belanja B2B.' }}">
        <x-favicon />
        @fonts
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-2 font-sans text-brand-black antialiased">
        @php
            $navCategories = \App\Models\Category::query()->orderBy('name')->limit(8)->get();
            $user = auth()->user();
            $customerStatus = $user?->customerStatus();
            $businessName = $user?->customerProfile?->business_name;
            $unpaidCount = $user ? \App\Models\Order::where('user_id', $user->id)->whereIn('status', ['unpaid', 'payment_pending', 'payment_rejected'])->count() : 0;
        @endphp

        <div class="flex min-h-screen flex-col bg-surface-2">
            <x-storefront.navbar :categories="$navCategories" />

            @auth
                <!-- Unified Customer Portal Top Header Bar -->
                <div class="w-full border-b border-zinc-200/80 bg-white shadow-2xs">
                    <div class="container-2xl py-4 sm:py-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <!-- Left: Profile Info -->
                            <div class="flex items-center gap-3.5">
                                <div class="flex size-11 items-center justify-center rounded-xl bg-brand-yellow font-black text-brand-black text-base shadow-2xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="truncate text-base sm:text-lg font-black text-zinc-900">{{ $user->name }}</h2>
                                        <x-ui.status-badge :status="$customerStatus ?: \App\Models\CustomerProfile::PENDING" />
                                    </div>
                                    <p class="truncate text-xs sm:text-sm text-zinc-500 font-medium mt-0.5">
                                        {{ $businessName ?: 'Pelanggan Toko' }} • {{ $user->email }} @if($user->phone) • {{ $user->phone }} @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Portal Meta or Quick Badge -->
                            <div class="hidden lg:flex items-center gap-3 text-xs text-zinc-500 font-medium">
                                <div class="flex items-center gap-1.5 rounded-lg bg-zinc-50 border border-zinc-200/80 px-3 py-1.5">
                                    <x-icon name="shield-check" class="size-4 text-emerald-600" />
                                    <span>B2B Verified Portal</span>
                                </div>
                            </div>
                        </div>

                        <!-- Clean Pill Navigation Tabs -->
                        <nav class="mt-4 sm:mt-5 flex items-center gap-2 overflow-x-auto pb-1" aria-label="Navigasi akun pelanggan">
                            <!-- 1. Ringkasan -->
                            <a
                                href="{{ route('account.dashboard') }}"
                                wire:navigate
                                class="inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold whitespace-nowrap transition-all {{ request()->routeIs('account.dashboard') ? 'bg-brand-yellow text-zinc-900 shadow-2xs' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}"
                            >
                                <x-icon name="layout-dashboard" class="size-4" />
                                <span>Ringkasan</span>
                            </a>

                            <!-- 2. Pesanan Saya -->
                            @if ($user->isActiveCustomer())
                                <a
                                    href="{{ route('orders.index') }}"
                                    wire:navigate
                                    class="inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold whitespace-nowrap transition-all {{ request()->routeIs('orders.*') ? 'bg-brand-yellow text-zinc-900 shadow-2xs' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}"
                                >
                                    <x-icon name="package" class="size-4" />
                                    <span>Pesanan Saya</span>
                                    @if ($unpaidCount > 0)
                                        <span class="inline-flex items-center justify-center size-5 rounded-full bg-amber-500 text-white text-[10px] font-black">
                                            {{ $unpaidCount }}
                                        </span>
                                    @endif
                                </a>
                            @endif

                            <!-- 3. Profil Usaha -->
                            <a
                                href="{{ route('account.profile') }}"
                                wire:navigate
                                class="inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold whitespace-nowrap transition-all {{ request()->routeIs('account.profile') ? 'bg-brand-yellow text-zinc-900 shadow-2xs' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}"
                            >
                                <x-icon name="user" class="size-4" />
                                <span>Profil Usaha</span>
                            </a>

                            <!-- 4. Buku Alamat -->
                            <a
                                href="{{ route('account.addresses.index') }}"
                                wire:navigate
                                class="inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold whitespace-nowrap transition-all {{ request()->routeIs('account.addresses.*') ? 'bg-brand-yellow text-zinc-900 shadow-2xs' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}"
                            >
                                <x-icon name="map-pin" class="size-4" />
                                <span>Buku Alamat</span>
                            </a>
                        </nav>
                    </div>
                </div>
            @endauth

            <main class="flex-1">
                <div class="container-2xl py-6 sm:py-8 space-y-6 min-h-[60vh] lg:min-h-[68vh] flex flex-col justify-start">
                    {{ $slot }}
                </div>
            </main>

            <x-storefront.footer :categories="$navCategories" />
        </div>

        @livewire('storefront.cart-drawer')
        @livewireScripts
        @fluxScripts
    </body>
</html>
