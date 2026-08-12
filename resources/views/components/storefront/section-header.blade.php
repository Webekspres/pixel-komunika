@props([
    'eyebrow'     => null,
    'title',
    'description' => null,
    'align'       => 'left', // left | center
    'titleSize'   => 'lg',   // lg | xl
])

@php
    $titleClass = match ($titleSize) {
        'xl' => 'text-4xl font-bold tracking-tight text-brand-black sm:text-5xl',
        default => 'text-3xl font-bold tracking-tight text-brand-black sm:text-4xl',
    };
    $wrapClass = $align === 'center'
        ? 'mx-auto max-w-3xl text-center'
        : 'max-w-3xl';
@endphp

<div {{ $attributes->class($wrapClass) }}>
    @if ($eyebrow)
        <p class="mb-3 inline-flex items-center gap-2 text-sm font-semibold tracking-widest text-brand-black/50 uppercase">
            <span class="inline-block h-px w-6 bg-brand-yellow"></span>
            {{ $eyebrow }}
        </p>
    @endif

    <h2 class="{{ $titleClass }}">{{ $title }}</h2>

    @if ($description)
        <p class="mt-4 text-base leading-relaxed text-brand-black/65 sm:text-lg">
            {{ $description }}
        </p>
    @endif

    @if (isset($action))
        <div class="{{ $align === 'center' ? 'mt-6 flex justify-center' : 'mt-6' }}">
            {{ $action }}
        </div>
    @endif
</div>
