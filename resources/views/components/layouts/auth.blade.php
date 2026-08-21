<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Pixel Komunika') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Masuk atau daftarkan akun pelanggan Pixel Komunika — aksesoris elektronik, kartu data, dan pulsa.' }}">
        <x-favicon />
        @fonts
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-2 font-sans text-brand-black antialiased">

        {{--
            Auth Shell — Completely standalone.
            No sidebar. No topbar. No dashboard navigation.
            This is the entry point of the platform.
        --}}

        {{ $slot }}

        @livewireScripts
        @fluxScripts
    </body>
</html>
