<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Portal pelanggan terverifikasi Pixel Komunika — aksesoris elektronik, kartu data, dan pulsa.' }}">
        <x-favicon />
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-2 font-sans text-brand-black antialiased">
        <div class="min-h-screen bg-linear-to-b from-brand-yellow-muted/65 via-brand-white to-zinc-50">
            <header class="sticky top-0 z-30 border-b border-brand-black/8 bg-brand-white/88 backdrop-blur-xl">
                <div class="container-2xl">
                    <div class="flex min-h-[4.5rem] flex-wrap items-center gap-3 py-3">
                        <a href="{{ route('home') }}" class="shrink-0" aria-label="Pixel Komunika beranda">
                            <img src="{{ asset('assets/brand-logo.png') }}" alt="Pixel Komunika" class="h-9 w-auto sm:h-10" width="160" height="40">
                        </a>

                        <div class="hidden items-center gap-2 lg:flex">
                            <a href="{{ route('products.index') }}" class="storefront-pill transition hover:border-brand-black/20 hover:text-brand-black">
                                <x-icon name="store" class="size-3.5" />
                                <span>Katalog</span>
                            </a>
                            <a href="{{ route('cart.index') }}" class="storefront-pill transition hover:border-brand-black/20 hover:text-brand-black">
                                <x-icon name="shopping-cart" class="size-3.5" />
                                <span>Keranjang</span>
                            </a>
                        </div>

                        <div class="ml-auto flex items-center gap-2 sm:gap-3">
                            @auth
                                <a href="{{ route('account.dashboard') }}" class="hidden items-center gap-2 rounded-full border border-brand-black/10 bg-white/70 px-4 py-2 text-sm font-semibold text-brand-black/72 transition hover:border-brand-black/20 hover:text-brand-black sm:inline-flex">
                                    <x-icon name="user" class="size-4" />
                                    Akun
                                </a>

                                @if (auth()->user()->isActiveCustomer())
                                    <a href="{{ route('orders.index') }}" class="hidden rounded-full border border-brand-black/10 bg-white/70 px-4 py-2 text-sm font-semibold text-brand-black/72 transition hover:border-brand-black/20 hover:text-brand-black md:inline-flex">
                                        Order
                                    </a>
                                    <a href="{{ route('checkout.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-brand-black px-5 py-2.5 text-sm font-semibold text-brand-white transition hover:bg-brand-black/88">
                                        Checkout
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-brand-black/70 transition hover:text-brand-black sm:inline">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex rounded-full bg-brand-black px-5 py-2.5 text-sm font-semibold text-brand-white transition hover:bg-brand-black/88">
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
        </div>

        @livewire('storefront.cart-drawer')
        <x-storefront.cart-toast />
        @livewireScripts
        @fluxScripts
    </body>
</html>
