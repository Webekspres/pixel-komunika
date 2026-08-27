@props([
    'title',
    'description' => null,
    'eyebrow' => null,
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
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.12]"
            style="background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255) 1px, transparent 0); background-size: 22px 22px;"
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
            <p class="mx-auto mt-3 max-w-xs text-center text-sm leading-relaxed text-white/70">
                {{ $panelDescription }}
            </p>

            <div class="mt-8 grid grid-cols-3 gap-3">
                @foreach ([
                    ['value' => '500+', 'label' => 'Produk'],
                    ['value' => '200+', 'label' => 'Reseller Aktif'],
                    ['value' => '24/7', 'label' => 'Dukungan'],
                ] as $stat)
                    <div class="rounded-2xl border border-white/10 bg-white/10 px-3 py-3 text-center backdrop-blur-sm">
                        <p class="text-xl font-black text-brand-yellow">{{ $stat['value'] }}</p>
                        <p class="text-xs font-semibold text-white/65">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
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
                    @if ($eyebrow)
                        <p class="mb-2 text-[11px] font-bold tracking-widest text-zinc-400 uppercase">{{ $eyebrow }}</p>
                    @endif
                    <h1 class="text-2xl font-black text-zinc-900">{{ $title }}</h1>
                    @if ($description)
                        <p class="mt-1.5 text-sm text-zinc-500">{{ $description }}</p>
                    @endif
                </div>

                {{ $slot }}

                <p class="mt-8 text-center text-xs text-zinc-400">
                    © {{ date('Y') }} Pixel Komunika. Semua transaksi diproses secara aman.
                </p>
            </div>
        </div>
    </div>
</div>
