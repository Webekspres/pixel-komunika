@props([
    'title',
    'description'     => null,
    'primaryLabel'    => null,
    'primaryHref'     => null,
    'secondaryLabel'  => null,
    'secondaryHref'   => null,
    'mascot'          => false,
    'theme'           => 'yellow', // yellow | dark
    'backgroundImage' => null,
])

@php
    $isDark = $theme === 'dark';
    $hasBgImage = filled($backgroundImage);
    $hasAside = isset($aside) || ($mascot && ! $hasBgImage) || (isset($image) && ! $hasBgImage);
    $bg = $hasBgImage ? 'bg-brand-black' : ($isDark ? 'bg-brand-black' : 'bg-brand-yellow');
    $textPrimary = $isDark || $hasBgImage ? 'text-brand-white' : 'text-brand-black';
    $textMuted = $isDark || $hasBgImage ? 'text-brand-white/70' : 'text-brand-black/70';
    $primaryBtn = $isDark || $hasBgImage
        ? 'inline-flex items-center gap-2 rounded-full bg-brand-yellow px-6 py-3 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft'
        : 'inline-flex rounded-full bg-brand-black px-6 py-3 text-sm font-semibold text-brand-white transition hover:bg-brand-black/85';
    $secondaryBtn = $isDark || $hasBgImage
        ? 'inline-flex rounded-full border-2 border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/10'
        : 'inline-flex rounded-full border-2 border-brand-black/20 bg-white/40 px-6 py-3 text-sm font-semibold text-brand-black transition hover:border-brand-black/40 hover:bg-white/60';
@endphp

<section {{ $attributes->class(['relative overflow-hidden py-14 sm:py-20', $bg]) }}>
    @if ($hasBgImage)
        <div
            class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat sm:bg-[position:center_right]"
            style="background-image: url('{{ $backgroundImage }}')"
            aria-hidden="true"
        ></div>
        {{-- Stronger right fade so aside cards stay readable over product photo --}}
        <div
            class="pointer-events-none absolute inset-0 bg-linear-to-r from-brand-black via-brand-black/90 to-brand-black/55 sm:to-brand-black/45"
            aria-hidden="true"
        ></div>
    @else
        <div
            class="pointer-events-none absolute inset-0 opacity-10"
            style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 32px 32px;"
            aria-hidden="true"
        ></div>
    @endif

    <div class="container-2xl relative">
        <div @class(['grid items-center gap-8 lg:gap-12', 'lg:grid-cols-2' => $hasAside])>
            <div class="max-w-xl {{ $hasAside ? '' : 'mx-auto text-center lg:mx-0 lg:text-left' }}" data-reveal>
                <h2 class="text-3xl font-bold tracking-tight {{ $textPrimary }} sm:text-4xl lg:text-5xl">
                    {{ $title }}
                </h2>

                @if ($description)
                    <p class="mt-5 text-base leading-relaxed {{ $textMuted }} sm:text-lg">
                        {{ $description }}
                    </p>
                @endif

                @if ($primaryLabel || $secondaryLabel)
                    <div class="mt-8 flex flex-wrap items-center gap-3 {{ $hasAside ? '' : 'justify-center lg:justify-start' }}">
                        @if ($primaryLabel && $primaryHref)
                            <a href="{{ $primaryHref }}" wire:navigate class="{{ $primaryBtn }}">
                                {{ $primaryLabel }}
                                <x-icon name="arrow-right" class="size-4" />
                            </a>
                        @endif

                        @if ($secondaryLabel && $secondaryHref)
                            <a href="{{ $secondaryHref }}" class="{{ $secondaryBtn }}">
                                {{ $secondaryLabel }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            @if (isset($aside))
                <div class="w-full shrink-0">
                    {{ $aside }}
                </div>
            @elseif ($mascot && ! $hasBgImage)
                <div class="flex justify-center lg:justify-end">
                    <img
                        src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                        alt="Pixel Komunika Mascot"
                        class="w-full max-w-xs drop-shadow-xl sm:max-w-sm"
                        width="400"
                        height="400"
                        loading="lazy"
                    >
                </div>
            @elseif (isset($image) && ! $hasBgImage)
                <div class="flex justify-center lg:justify-end">
                    {{ $image }}
                </div>
            @endif
        </div>
    </div>
</section>
