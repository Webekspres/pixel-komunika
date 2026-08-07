@props([
    'title',
    'description' => null,
    'eyebrow'     => 'Internal access',
])

{{--
    Auth Shell — Split layout:
    - Left panel: Brand yellow with mascot + trust signals (hidden on mobile)
    - Right panel: Flux card with form
--}}
<section class="flex min-h-[calc(100vh-56px)] items-center">
    <div class="mx-auto grid w-full max-w-5xl gap-0 px-4 py-10 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-14 xl:max-w-6xl">

        {{-- ===================================================
             LEFT PANEL — Brand yellow with mascot
             =================================================== --}}
        <div class="relative hidden overflow-hidden rounded-l-3xl bg-brand-yellow p-10 lg:flex lg:flex-col lg:justify-between">

            {{-- Dot-grid background --}}
            <div
                class="pointer-events-none absolute inset-0 opacity-15"
                style="background-image: radial-gradient(circle at 1px 1px, #181818 1px, transparent 0); background-size: 24px 24px;"
                aria-hidden="true"
            ></div>

            {{-- Top content --}}
            <div class="relative z-10">
                <img
                    src="{{ asset('assets/brand-logo.png') }}"
                    alt="Pixel Komunika"
                    class="mb-8 h-8 w-auto"
                    width="130"
                    height="32"
                >

                <h1 class="text-3xl font-bold tracking-tight text-brand-black xl:text-4xl">
                    {{ $title }}
                </h1>

                @if ($description)
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-brand-black/70">
                        {{ $description }}
                    </p>
                @endif
            </div>

            {{-- Mascot --}}
            <div class="relative z-10 flex justify-center py-6">
                <img
                    src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                    alt="Pixel Komunika Mascot"
                    class="animate-float w-48 drop-shadow-xl xl:w-56"
                    width="224"
                    height="224"
                    loading="eager"
                >
            </div>

            {{-- Trust signals --}}
            <div class="relative z-10 grid gap-3">
                <div class="rounded-2xl border border-brand-black/10 bg-white/60 p-4 backdrop-blur-sm">
                    <div class="flex items-center gap-2.5">
                        <x-icon name="badge-check" class="size-5 shrink-0 text-brand-black" />
                        <p class="text-sm font-semibold text-brand-black">Verified-customer workflow</p>
                    </div>
                    <p class="mt-1.5 pl-7 text-xs leading-relaxed text-brand-black/65">
                        Akses harga, checkout, dan order hanya terbuka untuk akun customer aktif.
                    </p>
                </div>
                <div class="rounded-2xl border border-brand-black/10 bg-white/60 p-4 backdrop-blur-sm">
                    <div class="flex items-center gap-2.5">
                        <x-icon name="package" class="size-5 shrink-0 text-brand-black" />
                        <p class="text-sm font-semibold text-brand-black">Katalog dari POS</p>
                    </div>
                    <p class="mt-1.5 pl-7 text-xs leading-relaxed text-brand-black/65">
                        Harga &amp; stok mengikuti data operasional — selalu akurat.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===================================================
             RIGHT PANEL — Form
             =================================================== --}}
        <div class="flex items-center rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8 lg:rounded-l-none lg:rounded-r-3xl">
            <div class="w-full">

                {{-- Mobile logo --}}
                <div class="mb-6 flex justify-center lg:hidden">
                    <img
                        src="{{ asset('assets/brand-logo.png') }}"
                        alt="Pixel Komunika"
                        class="h-8 w-auto"
                        width="130"
                        height="32"
                    >
                </div>

                {{-- Eyebrow --}}
                <p class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">{{ $eyebrow }}</p>

                <flux:heading size="xl" class="mt-2">{{ $title }}</flux:heading>

                @if ($description)
                    <flux:text class="mt-2 text-zinc-500">{{ $description }}</flux:text>
                @endif

                <div class="mt-7">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</section>
