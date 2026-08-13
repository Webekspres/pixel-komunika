@props([
    'title',
    'description'  => null,
    'eyebrow'      => null,
    'mascotVariant' => 'base',
])

{{--
    Auth Shell — split brand panel + form.
    Brand assets: brand-logo.png + Maskot-base.webp (Laravel, not Figma SVG)
--}}
<div class="flex min-h-screen flex-col lg:flex-row" id="auth-page">

    <div class="relative hidden flex-col justify-between overflow-hidden bg-brand-yellow px-10 py-10 lg:flex lg:w-[45%] xl:w-[40%] xl:px-14">
        <div class="pointer-events-none absolute inset-0 opacity-10" aria-hidden="true">
            @for ($i = 0; $i < 6; $i++)
                <div
                    class="absolute rounded-full border-4 border-brand-black"
                    style="width: {{ 120 + $i * 60 }}px; height: {{ 120 + $i * 60 }}px; top: 50%; left: 50%; transform: translate(-50%, -50%);"
                ></div>
            @endfor
        </div>

        <div class="relative z-10">
            <a href="{{ route('home') }}" aria-label="Pixel Komunika beranda">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-10 w-auto"
                    width="160"
                    height="40"
                >
            </a>
        </div>

        <div class="relative z-10 flex flex-col items-center">
            <img
                src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                alt="Pixel Komunika Mascot"
                class="animate-float w-44 drop-shadow-2xl xl:w-48"
                width="192"
                height="192"
                loading="eager"
            >
            <h2 class="mt-4 text-center text-2xl leading-tight font-black text-brand-black">
                Platform Belanja B2B<br>Terpercaya
            </h2>
            <p class="mt-3 max-w-xs text-center text-sm leading-relaxed text-brand-black/70">
                Akses harga grosir dan partai eksklusif untuk pelanggan terverifikasi.
            </p>
        </div>

        <div class="relative z-10 grid grid-cols-3 gap-3">
            @foreach ([
                ['value' => '500+', 'label' => 'Produk'],
                ['value' => '200+', 'label' => 'Reseller Aktif'],
                ['value' => '24/7', 'label' => 'Dukungan'],
            ] as $stat)
                <div class="rounded-2xl bg-white/30 px-3 py-3 text-center">
                    <p class="text-xl font-black text-brand-black">{{ $stat['value'] }}</p>
                    <p class="text-xs font-semibold text-brand-black/70">{{ $stat['label'] }}</p>
                </div>
            @endforeach
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
