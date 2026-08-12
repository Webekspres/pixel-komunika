@props([
    'eyebrow'   => null,
    'title',
    'description' => null,
    'backHref'    => null,
    'backLabel'   => 'Kembali',
    'variant'     => 'storefront',
])

@php
    $isAdmin = $variant === 'admin';
    $wrapperClass = $isAdmin
        ? 'flex flex-col gap-5 overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-card sm:p-7 lg:flex-row lg:items-end lg:justify-between'
        : 'storefront-panel-soft flex flex-col gap-5 overflow-hidden p-6 sm:p-7 lg:flex-row lg:items-end lg:justify-between lg:p-8';
    $eyebrowClass = $isAdmin
        ? 'mb-3 flex items-center gap-2 text-[11px] font-bold tracking-[0.28em] text-zinc-400 uppercase'
        : 'mb-3 flex items-center gap-2 text-[11px] font-bold tracking-[0.28em] text-brand-black/45 uppercase';
    $eyebrowLineClass = $isAdmin ? 'bg-zinc-300' : 'bg-brand-yellow';
    $titleClass = $isAdmin
        ? 'text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl'
        : 'text-3xl font-bold tracking-tight text-brand-black sm:text-4xl lg:text-[2.6rem]';
    $descriptionClass = $isAdmin
        ? 'mt-3 max-w-3xl text-sm leading-relaxed text-zinc-500 sm:text-base'
        : 'mt-3 max-w-3xl text-sm leading-relaxed text-brand-black/62 sm:text-base';
    $backClass = $isAdmin
        ? 'inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950'
        : 'inline-flex items-center gap-2 rounded-full border border-brand-black/10 bg-white/80 px-4 py-2 text-sm font-semibold text-brand-black/72 transition hover:border-brand-black/20 hover:text-brand-black';
@endphp

<div {{ $attributes->class($wrapperClass) }}>
    <div class="min-w-0 flex-1">
        @if ($eyebrow)
            <p class="{{ $eyebrowClass }}">
                <span class="inline-block h-px w-8 {{ $eyebrowLineClass }}"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 class="{{ $titleClass }}">{{ $title }}</h1>

        @if ($description)
            <p class="{{ $descriptionClass }}">{{ $description }}</p>
        @endif
    </div>

    <div class="flex shrink-0 items-center gap-2">
        @if ($backHref)
            <a href="{{ $backHref }}" class="{{ $backClass }}">
                <x-icon name="arrow-left" class="size-4" />
                {{ $backLabel }}
            </a>
        @endif

        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>
</div>
