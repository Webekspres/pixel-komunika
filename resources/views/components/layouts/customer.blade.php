<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Portal pelanggan Pixel Komunika yang tetap menyatu dengan pengalaman storefront.' }}">
        @fonts
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-2 font-sans text-brand-black antialiased">
        @php
            $cartService = app(\App\Services\CartService::class);
            $storefrontCart = $cartService->getOrCreateCart(auth()->user(), session()->getId());
            $storefrontCartCount = $cartService->getCartSummary($storefrontCart)['total_items'] ?? 0;
            $navCategories = \App\Models\Category::query()->orderBy('name')->limit(8)->get();
            $customerStatus = auth()->user()?->customerStatus();
            $businessName = auth()->user()?->customerProfile?->business_name;
        @endphp

        <div class="flex min-h-screen flex-col bg-surface-2">
            <x-storefront.navbar :cart-count="$storefrontCartCount" :categories="$navCategories" />

            @auth
                <div class="w-full border-b border-zinc-100 bg-white">
                    <div class="container-2xl py-4">
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-full bg-brand-yellow">
                                <span class="text-sm font-bold text-brand-black">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-zinc-900">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ $businessName ?: auth()->user()->email }}</p>
                            </div>
                            @if ($customerStatus === \App\Models\CustomerProfile::ACTIVE)
                                <span class="ml-auto rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                            @elseif ($customerStatus)
                                <span class="ml-auto rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 uppercase">{{ $customerStatus }}</span>
                            @endif
                        </div>

                        <nav class="flex gap-1 overflow-x-auto pb-1" aria-label="Navigasi akun pelanggan">
                            <a
                                href="{{ route('account.dashboard') }}"
                                wire:navigate
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('account.dashboard') ? 'bg-brand-yellow text-brand-black' : 'text-zinc-600 hover:bg-zinc-100' }}"
                            >
                                <x-icon name="layout-dashboard" class="size-3.5" />
                                Ringkasan
                            </a>
                            @if (auth()->user()->isActiveCustomer())
                                <a
                                    href="{{ route('orders.index') }}"
                                    wire:navigate
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('orders.*') ? 'bg-brand-yellow text-brand-black' : 'text-zinc-600 hover:bg-zinc-100' }}"
                                >
                                    <x-icon name="package" class="size-3.5" />
                                    Pesanan Saya
                                </a>
                            @endif
                            <a
                                href="{{ route('account.profile') }}"
                                wire:navigate
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('account.profile') ? 'bg-brand-yellow text-brand-black' : 'text-zinc-600 hover:bg-zinc-100' }}"
                            >
                                <x-icon name="user" class="size-3.5" />
                                Profil
                            </a>
                            <a
                                href="{{ route('account.addresses.index') }}"
                                wire:navigate
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('account.addresses.*') ? 'bg-brand-yellow text-brand-black' : 'text-zinc-600 hover:bg-zinc-100' }}"
                            >
                                <x-icon name="map-pin" class="size-3.5" />
                                Alamat
                            </a>
                        </nav>
                    </div>
                </div>
            @endauth

            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-storefront.footer :categories="$navCategories" />
        </div>

        @livewire('storefront.cart-drawer')
        @livewireScripts
        @fluxScripts
    </body>
</html>
