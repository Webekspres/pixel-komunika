<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-brand-black antialiased">
        <header class="border-b border-brand-black/10 bg-brand-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('assets/brand-logo.png') }}" alt="Pixel Komunika" class="h-9 w-auto">
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('account.dashboard') }}" class="text-brand-black/70 transition hover:text-brand-red">
                            Akun
                        </a>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.customers.index') }}" class="text-brand-black/70 transition hover:text-brand-red">
                                Customer
                            </a>
                        @endif

                        @if (auth()->user()->isActiveCustomer())
                            <a href="{{ route('orders.index') }}" class="text-brand-black/70 transition hover:text-brand-red">
                                Order
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-brand-yellow px-4 py-2 font-semibold text-brand-black">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-brand-black/70 transition hover:text-brand-red">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-full bg-brand-yellow px-4 py-2 font-semibold text-brand-black">
                            Daftar
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
