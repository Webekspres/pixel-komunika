{{-- Tanpa query DB atau komponen Livewire: halaman ini juga tampil saat DB/app gagal. --}}
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>{{ $title }} - Pixel Komunika</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-surface-2 font-sans text-brand-black antialiased">
        <main class="mx-auto flex min-h-screen w-full max-w-xl flex-col items-center justify-center px-4 py-12 text-center">
            <a href="{{ url('/') }}" class="mb-8 inline-flex min-h-11 items-center" aria-label="Pixel Komunika beranda">
                <img src="{{ asset('assets/brand-logo.png') }}" alt="Pixel Komunika" class="h-10 w-auto" width="160" height="40">
            </a>
            <img src="{{ asset('assets/mascot/Maskot-base.webp') }}" alt="" class="mb-6 h-36 w-auto" width="144" height="144">
            <p class="text-sm font-semibold text-zinc-600">Kode {{ $code }}</p>
            <h1 class="mt-1 text-2xl font-black text-zinc-900 sm:text-3xl">{{ $title }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-zinc-700">{{ $message }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ url('/produk') }}" class="inline-flex min-h-11 items-center rounded-xl bg-brand-yellow px-5 text-sm font-bold text-brand-black hover:bg-brand-yellow-soft">Lihat Katalog Produk</a>
                <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center rounded-xl border border-zinc-300 bg-white px-5 text-sm font-bold text-zinc-800 hover:bg-zinc-50">Ke Beranda</a>
                <a href="https://wa.me/6281546407702" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center rounded-xl px-5 text-sm font-semibold text-zinc-800 underline underline-offset-4">Hubungi admin</a>
            </div>
        </main>
    </body>
</html>
