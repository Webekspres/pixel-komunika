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
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
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

        {{-- Badges stack in one row so the label and category never overlap --}}
        @if ($badge || $category)
            <div class="absolute inset-x-3 top-3 flex flex-wrap items-start gap-1.5">
                @if ($badge)
                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $badgeClasses }}">
                        {{ $badge }}
                    </span>
                @endif
                @if ($category)
                    <span class="inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1 text-xs font-medium text-brand-black/80">
                        @if ($icon)
                            <x-icon :name="$icon" class="size-3" />
                        @endif
                        {{ $category }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4 sm:p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
            <div class="min-w-0 flex-1">
                <h3 class="line-clamp-2 min-h-[2.75rem] text-sm leading-snug font-bold text-brand-black transition group-hover:text-brand-black sm:text-base">
                    {{ $title }}
                </h3>
                @if ($sku)
                    <p class="mt-1 text-[11px] break-all text-brand-black/65">SKU: {{ $sku }}</p>
                @endif
            </div>

            @if ($stockLabel)
                <span class="shrink-0 self-start rounded-md px-2.5 py-1 text-[11px] font-bold {{ $stockClasses }}">
                    {{ $stockLabel }}
                </span>
            @endif
        </div>

        @if ($description)
            <p class="mt-3 text-xs leading-relaxed text-brand-black/70">{{ $description }}</p>
        @endif

        <div class="mt-4 flex items-end justify-between gap-3 border-t border-brand-black/6 pt-4">
            <div>
                @if ($showPrice && $price)
                    <p class="text-xs font-semibold text-brand-black/65">Harga Grosir</p>
                    <p class="mt-1 text-base font-extrabold text-brand-black sm:text-lg">
                        {{ $price }}
                    </p>
                @elseif (!$showPrice)
                    <p class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-black/65">
                        <x-icon name="lock" class="size-3" />
                        Verifikasi Akun
                    </p>
                @endif
            </div>

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
