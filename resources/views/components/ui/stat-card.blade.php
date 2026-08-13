@props([
    'label',
    'value',
    'description' => null,
    'icon'        => null,
    'trend'       => null,  // 'up' | 'down' | null
    'trendLabel'  => null,
    'accent'      => false, // adds a left yellow accent bar
    'variant'     => 'storefront',
])

@php
    $isAdmin = $variant === 'admin';
    $wrapperClass = $isAdmin
        ? 'relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 shadow-card sm:p-6'
        : 'storefront-panel relative overflow-hidden p-5 sm:p-6';
    $labelClass = $isAdmin
        ? 'text-[11px] font-bold tracking-[0.24em] text-zinc-400 uppercase'
        : 'text-[11px] font-bold tracking-[0.24em] text-brand-black/38 uppercase';
    $valueClass = $isAdmin
        ? 'mt-2 text-2xl font-semibold tracking-tight text-zinc-950 sm:text-3xl'
        : 'mt-2 text-2xl font-bold tracking-tight text-zinc-950 sm:text-3xl';
    $descriptionClass = $isAdmin
        ? 'mt-1 text-sm leading-relaxed text-zinc-500'
        : 'mt-1 text-sm leading-relaxed text-brand-black/55';
    $iconClass = $isAdmin
        ? 'inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-yellow/20'
        : 'inline-flex size-10 shrink-0 items-center justify-center rounded-2xl bg-brand-yellow-muted';
    $iconColorClass = $isAdmin ? 'size-5 text-brand-yellow-dark' : 'size-5 text-brand-black/70';
@endphp

<section {{ $attributes->class([$wrapperClass, 'border-l-4 border-amber-400' => $accent]) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
            <p class="{{ $labelClass }}">{{ $label }}</p>
            <p class="{{ $valueClass }}">{{ $value }}</p>

            @if ($description)
                <p class="{{ $descriptionClass }}">{{ $description }}</p>
            @endif

            @if ($trend && $trendLabel)
                <div class="mt-2.5 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium
                    {{ $trend === 'up' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}"
                >
                    <x-icon :name="$trend === 'up' ? 'trending-up' : 'trending-down'" class="size-3" />
                    {{ $trendLabel }}
                </div>
            @endif
        </div>

        @if ($icon)
            <div class="{{ $iconClass }}">
                <x-icon :name="$icon" class="{{ $iconColorClass }}" />
            </div>
        @endif
    </div>
</section>
