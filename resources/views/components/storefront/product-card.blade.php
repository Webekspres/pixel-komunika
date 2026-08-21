@props([
    'title',
    'image'        => null,
    'icon'         => null,
    'description'  => null,
    'badge'        => null,
    'badgeVariant' => 'available', // available | limited | unavailable
    'price'        => null,
    'showPrice'    => false,
    'href'         => null,
    'category'     => null,
    'sku'          => null,
    'stockLabel'   => null,
    'stockVariant' => 'available', // available | unavailable
    'ctaHref'      => null,
    'ctaLabel'     => 'Lihat detail',
    'ctaVariant'   => 'secondary', // secondary | primary
])

@php
    $badgeClasses = match ($badgeVariant) {
        'limited'     => 'bg-brand-black text-brand-white',
        'unavailable' => 'bg-gray-200 text-gray-600',
        default       => 'bg-brand-yellow text-brand-black',
    };
    $tag = $href ? 'a' : 'div';
    $stockClasses = match ($stockVariant) {
        'unavailable' => 'bg-red-50 text-red-600',
        default => 'bg-emerald-50 text-emerald-700',
    };
    $ctaClasses = $ctaVariant === 'primary'
        ? 'bg-brand-black text-brand-white hover:bg-brand-black/88'
        : 'bg-zinc-100 text-brand-black hover:bg-zinc-200';
@endphp

<{{ $tag }}
    @if ($href)
        href="{{ $href }}"
        wire:navigate
    @endif
    {{ $attributes->class([
        'product-card product-card-hover group block',
        'cursor-pointer' => $href,
    ]) }}
>
    {{-- Image Container --}}
    <div class="relative aspect-4/3 overflow-hidden bg-linear-to-br from-brand-yellow-muted via-white to-zinc-50">
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
                width="800"
                height="600"
            >
        @else
            <div class="flex h-full w-full items-center justify-center">
                <div class="inline-flex size-20 items-center justify-center rounded-[1.75rem] bg-brand-white shadow-card">
                    <x-icon :name="$icon ?: 'package'" class="size-10 text-brand-black/28" />
                </div>
            </div>
        @endif

        {{-- Badge --}}
        @if ($badge)
            <span class="absolute top-3 left-3 inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $badgeClasses }}">
                {{ $badge }}
            </span>
        @endif

        {{-- Category badge --}}
        @if ($category)
            <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-md bg-white/90 px-2.5 py-1 text-xs font-medium text-brand-black/70 backdrop-blur-sm">
                @if ($icon)
                    <x-icon :name="$icon" class="size-3" />
                @endif
                {{ $category }}
            </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4 sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h3 class="line-clamp-2 min-h-[2.75rem] text-sm leading-snug font-bold text-brand-black transition group-hover:text-brand-black sm:text-base">
                    {{ $title }}
                </h3>
                @if ($sku)
                    <p class="mt-1 text-[11px] text-brand-black/38">SKU: {{ $sku }}</p>
                @endif
            </div>

            @if ($stockLabel)
                <span class="shrink-0 rounded-md px-2.5 py-1 text-[10px] font-bold {{ $stockClasses }}">
                    {{ $stockLabel }}
                </span>
            @endif
        </div>

        @if ($description)
            <p class="mt-3 text-xs leading-relaxed text-brand-black/55">{{ $description }}</p>
        @endif

        <div class="mt-4 flex items-end justify-between gap-3 border-t border-brand-black/6 pt-4">
            <div>
                @if ($showPrice && $price)
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-brand-black/35">Harga Grosir</p>
                    <p class="mt-1 text-base font-extrabold text-brand-black sm:text-lg">
                        {{ $price }}
                    </p>
                @elseif (!$showPrice)
                    <p class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-black/45">
                        <x-icon name="lock" class="size-3" />
                        Verifikasi Akun
                    </p>
                @endif
            </div>

            @if ($href)
                <span class="inline-flex size-8 items-center justify-center rounded-md bg-brand-yellow transition duration-200 group-hover:translate-x-0.5">
                    <x-icon name="arrow-right" class="size-4 text-brand-black" />
                </span>
            @endif
        </div>

        @if (isset($actions))
            <div class="mt-4">
                {{ $actions }}
            </div>
        @elseif ($ctaHref)
            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                <a
                    href="{{ $ctaHref }}"
                    class="inline-flex items-center justify-center rounded-md px-4 py-2.5 text-xs font-bold transition {{ $ctaClasses }}"
                >
                    {{ $ctaLabel }}
                </a>
            </div>
        @endif
    </div>
</{{ $tag }}>
