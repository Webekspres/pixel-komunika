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
    <body class="min-h-screen bg-brand-white font-sans text-brand-black antialiased">
        <div class="min-h-screen bg-linear-to-b from-brand-yellow-muted/55 via-brand-white to-zinc-50">
            <x-storefront.navbar :cart-count="0" />

            @auth
                <section class="border-b border-brand-black/8 bg-white/72 backdrop-blur-sm">
                    <div class="container-2xl py-4">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-brand-black/40">Portal pelanggan</p>
                                <h1 class="mt-2 text-2xl font-bold tracking-tight text-brand-black sm:text-3xl">Akun Saya</h1>
                                <p class="mt-1 text-sm text-brand-black/55">Ringkasan akun, pesanan, profil, dan alamat Anda tetap berada dalam pengalaman storefront Pixel Komunika.</p>
                            </div>

                            <nav class="flex flex-wrap gap-2" aria-label="Navigasi akun pelanggan">
                                <a
                                    href="{{ route('account.dashboard') }}"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('account.dashboard') ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                                >
                                    Ringkasan
                                </a>
                                @if (auth()->user()->isActiveCustomer())
                                    <a
                                        href="{{ route('orders.index') }}"
                                        class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('orders.*') ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                                    >
                                        Pesanan Saya
                                    </a>
                                @endif
                                <a
                                    href="{{ route('account.profile') }}"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('account.profile') ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                                >
                                    Profil
                                </a>
                                <a
                                    href="{{ route('account.addresses.index') }}"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('account.addresses.*') ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                                >
                                    Alamat
                                </a>
                            </nav>
                        </div>
                    </div>
                </section>
            @endauth

            <main>
                {{ $slot }}
            </main>
        </div>

        @livewire('storefront.cart-drawer')
        @livewireScripts
        @fluxScripts
    </body>
</html>
