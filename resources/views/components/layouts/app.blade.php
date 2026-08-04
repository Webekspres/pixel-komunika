<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        @fonts
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-50 font-sans text-zinc-950 antialiased">
        <header class="border-b border-zinc-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('assets/brand-logo.png') }}" alt="Pixel Komunika" class="h-9 w-auto">
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <flux:button href="{{ route('account.dashboard') }}" variant="ghost" size="sm">Akun</flux:button>

                        @if (auth()->user()->isAdmin())
                            <flux:button href="{{ route('admin.customers.index') }}" variant="ghost" size="sm">Customer</flux:button>
                        @endif

                        @if (auth()->user()->isActiveCustomer())
                            <flux:button href="{{ route('orders.index') }}" variant="ghost" size="sm">Order</flux:button>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <flux:button type="submit" variant="primary" color="amber" size="sm">Logout</flux:button>
                        </form>
                    @else
                        <flux:button href="{{ route('login') }}" variant="ghost" size="sm">Masuk</flux:button>
                        <flux:button href="{{ route('register') }}" variant="primary" color="amber" size="sm">Daftar</flux:button>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="pb-12">
            {{ $slot }}
        </main>

        @livewireScripts
        @fluxScripts
    </body>
</html>
