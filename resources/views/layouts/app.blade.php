<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Portal pelanggan terverifikasi Pixel Komunika — aksesoris elektronik, kartu data, dan pulsa.' }}">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-brand-white font-sans text-brand-black antialiased" x-data="{ mobileMenu: false }">

        {{-- Storefront topbar (for non-homepage pages like /checkout, /orders) --}}
        <header class="sticky top-0 z-30 border-b border-brand-black/8 bg-brand-white/92 backdrop-blur-lg">
            <div class="container-2xl">
                <div class="flex h-16 items-center gap-4">
                    <a href="{{ route('home') }}" class="shrink-0" aria-label="Pixel Komunika beranda">
                        <img src="{{ asset('assets/brand-logo.png') }}" alt="Pixel Komunika" class="h-9 w-auto" width="140" height="36">
                    </a>

                    <div class="ml-auto flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ route('account.dashboard') }}" class="hidden items-center gap-2 text-sm font-medium text-brand-black/70 transition hover:text-brand-black sm:inline-flex">
                                <x-icon name="user" class="size-4" />
                                Akun
                            </a>

                            @if (auth()->user()->isActiveCustomer())
                                <a href="{{ route('orders.index') }}" class="hidden text-sm font-medium text-brand-black/70 transition hover:text-brand-black sm:inline">
                                    Order
                                </a>
                                <a href="{{ route('checkout.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft">
                                    Checkout
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="hidden text-sm font-medium text-brand-black/70 transition hover:text-brand-black sm:inline">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
