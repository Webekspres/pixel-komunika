@props([
    'title',
    'description' => null,
    'panelTitle' => 'Platform Belanja B2B Terpercaya',
    'panelDescription' => 'Akses harga grosir dan partai eksklusif untuk pelanggan terverifikasi.',
])

{{--
    Auth Shell — split brand panel + form.
    Dark hero-style panel: auth-panel.webp + dark pattern (not yellow wash).
--}}
<div class="flex min-h-screen flex-col lg:flex-row" id="auth-page">

    <div class="relative hidden flex-col justify-between overflow-hidden bg-brand-black px-10 py-10 lg:flex lg:w-[45%] xl:w-[40%] xl:px-14">
        <div
            class="pointer-events-none absolute inset-0 bg-cover bg-[position:center_40%] bg-no-repeat"
            style="background-image: url('{{ asset('assets/hero/auth-panel.webp') }}')"
            aria-hidden="true"
        ></div>
        {{-- Soft dark wash like hero — keeps copy readable without yellow overlay --}}
        <div
            class="pointer-events-none absolute inset-0 bg-linear-to-b from-brand-black/80 via-brand-black/45 to-brand-black/85"
            aria-hidden="true"
        ></div>

        <div class="relative z-10">
            <a href="{{ route('home') }}" aria-label="Pixel Komunika beranda">
                <img
                    src="{{ asset('assets/brand-logo-white.png') }}"
                    alt="Pixel Komunika"
                    class="h-10 w-auto"
                    width="160"
                    height="40"
                >
            </a>
        </div>

        <div class="relative z-10 mt-auto">
            <h2 class="text-center text-2xl leading-tight font-black text-white">
                {{ $panelTitle }}
            </h2>
            <p class="mx-auto mt-3 max-w-xs text-center text-sm leading-relaxed text-white/90">
                {{ $panelDescription }}
            </p>

        </div>
    </div>

    <div class="flex flex-1 flex-col bg-white">
        <div class="flex items-center gap-2 border-b border-zinc-100 px-5 py-4 lg:hidden">
            <a href="{{ route('home') }}" aria-label="Pixel Komunika beranda">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-8 w-auto"
                    width="130"
                    height="32"
                >
            </a>
        </div>

        <div class="flex flex-1 flex-col justify-center overflow-y-auto px-5 py-8 sm:px-10 lg:px-14 xl:px-20">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-7">
                    <h1 class="text-2xl font-black text-zinc-900">{{ $title }}</h1>
                    @if ($description)
                        <p class="mt-1.5 text-sm text-zinc-600">{{ $description }}</p>
                    @endif
                </div>

                {{ $slot }}

                <p class="mt-8 text-center text-xs text-zinc-500">
                    © {{ date('Y') }} Pixel Komunika
                </p>
            </div>
        </div>
    </div>
</div>
