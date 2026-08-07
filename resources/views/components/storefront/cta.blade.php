@props([
    'title',
    'description'    => null,
    'primaryLabel'   => 'Daftar sekarang',
    'primaryHref'    => null,
    'secondaryLabel' => null,
    'secondaryHref'  => null,
    'align'          => 'center', // center | left
    'mascot'         => false,
    'theme'          => 'yellow-muted', // yellow-muted | yellow | dark
])

@php
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
        : 'inline-flex items-center rounded-full border-2 border-brand-black/20 bg-white/50 px-7 py-3.5 text-sm font-semibold text-brand-black transition hover:border-brand-black/40';
    $containerAlign = $align === 'center' ? 'text-center' : '';
    $flexAlign = $align === 'center' ? 'justify-center' : '';
@endphp

<section {{ $attributes->class(['py-16 sm:py-24', $bgClass]) }}>
    <div class="container-2xl">
        <div class="{{ $mascot ? 'grid items-center gap-12 lg:grid-cols-[1fr_auto]' : '' }}">
            <div class="{{ $containerAlign }}">
                <h2 class="text-3xl font-bold tracking-tight {{ $textClass }} sm:text-4xl lg:text-5xl">
                    {{ $title }}
                </h2>

                @if ($description)
                    <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed {{ $mutedClass }} sm:text-lg {{ $align === 'center' ? '' : 'mx-0' }}">
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

            @if ($mascot)
                <div class="hidden lg:block">
                    <img
                        src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                        alt="Pixel Komunika Mascot"
                        class="w-52 drop-shadow-lg xl:w-64"
                        width="256"
                        height="256"
                        loading="lazy"
                    >
                </div>
            @endif
        </div>
    </div>
</section>
