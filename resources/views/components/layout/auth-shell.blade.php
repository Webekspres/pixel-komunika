@props([
    'title',
    'description'  => null,
    'eyebrow'      => null,
    'mascotVariant' => 'base', // future: 'wave', 'point', etc.
])

{{--
    Auth Shell — Full-page, standalone, split layout.

    Left panel  (desktop only):
        - Brand yellow surface
        - Mascot illustration (floating animation)
        - Short value proposition
        - Trust badges

    Right panel:
        - Clean white card, centered
        - Flux UI form slot
        - No dashboard chrome whatsoever

    Mobile: stacks vertically, left panel collapses into
    a compact brand header row at top.
--}}
<div class="flex min-h-screen flex-col lg:flex-row" id="auth-page">

    {{-- ======================================================
         LEFT PANEL — Brand & trust surface
         ====================================================== --}}
    <div class="relative flex flex-col overflow-hidden bg-brand-yellow lg:sticky lg:top-0 lg:h-screen lg:w-[52%] xl:w-[55%]">

        {{-- Dot-grid texture --}}
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.12]"
            style="background-image: radial-gradient(circle at 1px 1px, #1a1a1a 1px, transparent 0); background-size: 22px 22px;"
            aria-hidden="true"
        ></div>

        {{-- Decorative circles --}}
        <div class="pointer-events-none absolute -right-24 -top-24 size-80 rounded-full bg-brand-black/5" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 size-56 rounded-full bg-brand-black/5" aria-hidden="true"></div>

        {{-- Inner content --}}
        <div class="relative z-10 flex flex-1 flex-col px-8 pb-12 pt-8 sm:px-12 sm:pt-10 lg:px-14 lg:pt-12">

            {{-- Brand logo --}}
            <div class="shrink-0">
                <a href="{{ route('home') }}" aria-label="Pixel Komunika beranda">
                    <img
                        src="{{ asset('assets/brand-logo.png') }}"
                        alt="Pixel Komunika"
                        class="h-9 w-auto"
                        width="144"
                        height="36"
                    >
                </a>
            </div>

            {{-- Main copy --}}
            <div class="mt-10 shrink-0 lg:mt-14">
                <p class="text-xs font-bold tracking-widest text-brand-black/45 uppercase">
                    {{ $eyebrow ?? 'B2B Customer Portal' }}
                </p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-brand-black sm:text-4xl lg:text-[2.65rem] lg:leading-tight xl:text-5xl">
                    {{ $title }}
                </h1>
                @if ($description)
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-brand-black/65 lg:text-base">
                        {{ $description }}
                    </p>
                @endif
            </div>

            {{-- Mascot — grows to fill remaining space on desktop --}}
            <div class="flex flex-1 items-end justify-center py-8 lg:py-10">
                <img
                    src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                    alt="Pixel Komunika Mascot"
                    class="animate-float w-40 drop-shadow-2xl sm:w-48 lg:w-56 xl:w-64"
                    width="256"
                    height="256"
                    loading="eager"
                >
            </div>

            {{-- Trust badges --}}
            <div class="grid shrink-0 gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                <div class="flex items-start gap-3 rounded-2xl border border-brand-black/10 bg-white/55 px-4 py-3.5 backdrop-blur-sm">
                    <div class="mt-0.5 shrink-0">
                        <x-icon name="badge-check" class="size-5 text-brand-black" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-brand-black">Akun terverifikasi</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-brand-black/60">
                            Harga, checkout & riwayat order hanya terbuka setelah akun disetujui admin.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-2xl border border-brand-black/10 bg-white/55 px-4 py-3.5 backdrop-blur-sm">
                    <div class="mt-0.5 shrink-0">
                        <x-icon name="package" class="size-5 text-brand-black" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-brand-black">Katalog dari POS</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-brand-black/60">
                            Harga & stok mengikuti data operasional toko — selalu akurat dan real-time.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Back link --}}
            <div class="mt-6 shrink-0">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-black/50 transition hover:text-brand-black"
                >
                    <x-icon name="arrow-left" class="size-3.5" aria-hidden="true" />
                    Kembali ke storefront
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================
         RIGHT PANEL — Form slot
         ====================================================== --}}
    <div class="flex min-h-screen flex-1 items-center justify-center bg-zinc-100 px-5 py-12 lg:min-h-0 lg:overflow-y-auto lg:py-16">
        <div class="w-full max-w-md">

            {{-- Mobile-only: logo strip --}}
            <div class="mb-8 flex justify-center lg:hidden">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="h-8 w-auto"
                    width="130"
                    height="32"
                >
            </div>

            {{-- Form card --}}
            <div class="rounded-3xl bg-white px-7 py-8 shadow-card ring-1 ring-zinc-200 sm:px-9 sm:py-10">

                {{-- Card header --}}
                <div class="mb-7">
                    @if ($eyebrow)
                        <p class="mb-2 text-[11px] font-bold tracking-widest text-zinc-400 uppercase">
                            {{ $eyebrow }}
                        </p>
                    @endif
                    <flux:heading size="xl" class="text-zinc-950">{{ $title }}</flux:heading>
                    @if ($description)
                        <flux:text class="mt-2 text-zinc-500">{{ $description }}</flux:text>
                    @endif
                </div>

                {{-- Form content --}}
                {{ $slot }}
            </div>

            {{-- Footer note --}}
            <p class="mt-6 text-center text-xs text-zinc-400">
                © {{ date('Y') }} Pixel Komunika. Semua transaksi diproses secara aman.
            </p>
        </div>
    </div>
</div>
