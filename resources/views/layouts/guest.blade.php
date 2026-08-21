<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <x-favicon />
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-2 font-sans text-brand-black antialiased">
        {{ $slot }}

        @livewire('storefront.cart-drawer')
        <x-storefront.cart-toast />
        @livewireScripts
        @fluxScripts
    </body>
</html>
