@props([
    'title',
    'description'     => null,
    'primaryLabel'    => 'Daftar sekarang',
    'primaryHref'     => null,
    'secondaryLabel'  => null,
    'secondaryHref'   => null,
    'align'           => 'center', // center | left
    'mascot'          => false,
    'theme'           => 'yellow-muted', // yellow-muted | yellow | dark
    'backgroundImage' => null,
])

@php
    $hasBgImage = filled($backgroundImage);
    $showMascot = $mascot && ! $hasBgImage;
    $bgClass = match ($theme) {
        'yellow'       => 'bg-brand-yellow',
        'dark'         => 'bg-brand-black',
        default        => 'bg-brand-yellow-muted',
    };
    $textClass = $theme === 'dark' ? 'text-brand-white' : 'text-brand-black';
    $mutedClass = $theme === 'dark' ? 'text-brand-white/65' : 'text-brand-black/65';
    $btnPrimary = $theme === 'dark'
        ? 'inline-flex items-center gap-2 rounded-full bg-brand-yellow px-7 py-3.5 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft'
        : 'inline-flex items-center gap-2 rounded-full bg-brand-black px-7 py-3.5 text-sm font-semibold text-brand-white transition hover:bg-brand-black/85';
    $btnSecondary = $theme === 'dark'
        ? 'inline-flex items-center rounded-full border-2 border-white/25 px-7 py-3.5 text-sm font-semibold text-white transition hover:border-white/50'
        : 'inline-flex items-center rounded-full border-2 border-brand-black/20 bg-white/55 px-7 py-3.5 text-sm font-semibold text-brand-black backdrop-blur-sm transition hover:border-brand-black/40 hover:bg-white/70';
    $containerAlign = $align === 'center' ? 'text-center' : '';
    $flexAlign = $align === 'center' ? 'justify-center' : '';
    $patternColor = $theme === 'dark' ? 'rgb(255 255 255)' : 'rgb(0 0 0)';
@endphp

<section {{ $attributes->class(['relative overflow-hidden py-16 sm:py-20', $bgClass]) }}>
    @if ($hasBgImage)
        <div
            class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat sm:bg-[position:center_right]"
            style="background-image: url('{{ $backgroundImage }}')"
            aria-hidden="true"
        ></div>
        {{-- Soft left wash so black copy stays readable over yellow + baked-in mascot --}}
        <div
            class="pointer-events-none absolute inset-0 bg-linear-to-r from-brand-yellow via-brand-yellow/88 to-brand-yellow/25 sm:to-transparent"
            aria-hidden="true"
        ></div>
    @else
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.08]"
            style="background-image: radial-gradient(circle at 1px 1px, {{ $patternColor }} 1px, transparent 0); background-size: 28px 28px;"
            aria-hidden="true"
        ></div>
    @endif

    <div class="container-2xl relative">
        <div
            data-reveal
            @class([
                'items-center gap-8 lg:gap-10',
                'grid lg:grid-cols-[minmax(0,1fr)_auto]' => $showMascot,
                // Reserve right space so baked-in mascot in BG isn't covered by copy on large screens
                'lg:min-h-[14rem]' => $hasBgImage,
            ])
        >
            <div class="{{ $containerAlign }} max-w-xl {{ $align === 'center' && ! $showMascot ? 'mx-auto' : '' }}">
                <h2 class="text-3xl font-bold tracking-tight {{ $textClass }} sm:text-4xl lg:text-5xl">
                    {{ $title }}
                </h2>

                @if ($description)
                    <p class="mt-5 max-w-xl text-base leading-relaxed {{ $mutedClass }} sm:text-lg {{ $align === 'center' && ! $showMascot ? 'mx-auto' : '' }}">
                        {{ $description }}
                    </p>
                @endif

                <div class="mt-8 flex flex-wrap items-center gap-3 {{ $flexAlign }}">
                    @if ($primaryLabel && $primaryHref)
                        <a href="{{ $primaryHref }}" class="{{ $btnPrimary }}">
                            {{ $primaryLabel }}
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    @endif

                    @if ($secondaryLabel && $secondaryHref)
                        <a href="{{ $secondaryHref }}" class="{{ $btnSecondary }}">
                            {{ $secondaryLabel }}
                        </a>
                    @endif
                </div>
            </div>

            @if ($showMascot)
                <div class="hidden shrink-0 justify-self-end lg:block">
                    <img
                        src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                        alt="Pixel Komunika Mascot"
                        class="w-44 drop-shadow-lg xl:w-52"
                        width="208"
                        height="208"
                        loading="lazy"
                    >
                </div>
            @endif
        </div>
    </div>
</section>
