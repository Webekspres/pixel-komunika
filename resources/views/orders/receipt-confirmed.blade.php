<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Penerimaan - Pixel Komunika</title>
    <x-favicon />
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-100 font-sans text-zinc-900 antialiased p-6">
    <div class="mx-auto max-w-lg rounded-2xl border border-zinc-200 bg-white p-8 shadow-2xs text-center space-y-3">
        <h1 class="text-xl font-bold tracking-tight">
            @if ($alreadyConfirmed ?? false)
                Pesanan sudah dikonfirmasi
            @else
                Terima kasih — pesanan selesai
            @endif
        </h1>
        <p class="text-sm text-zinc-600">
            Pesanan <span class="font-mono font-semibold text-zinc-900">{{ $order->order_number }}</span>
            @if ($alreadyConfirmed ?? false)
                telah dikonfirmasi sebelumnya.
            @else
                telah ditandai selesai.
            @endif
        </p>
        <a href="{{ route('home') }}" class="inline-flex mt-4 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft">
            Kembali ke beranda
        </a>
    </div>
</body>
</html>
