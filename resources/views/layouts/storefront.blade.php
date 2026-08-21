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
        @php
            // Brand assets: public/assets/brand-logo.png + public/assets/mascot/Maskot-base.webp (not Figma SVG)
            $navCategories = \App\Models\Category::query()->orderBy('name')->limit(8)->get();
        @endphp

        <div class="flex min-h-screen flex-col bg-surface-2">
            <x-storefront.navbar :categories="$navCategories" />

            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-storefront.footer :categories="$navCategories" />
        </div>

        @livewire('storefront.cart-drawer')
        <x-storefront.cart-toast />
        @livewireScripts
        @fluxScripts
    </body>
</html>
