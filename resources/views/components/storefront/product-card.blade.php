@props([
    'title',
    'image',
    'icon'         => null,
    'description'  => null,
    'badge'        => null,
    'badgeVariant' => 'available', // available | limited | unavailable
    'price'        => null,
    'showPrice'    => false,
    'href'         => null,
    'category'     => null,
])

@php
    $badgeClasses = match ($badgeVariant) {
        'limited'     => 'bg-brand-black text-brand-white',
        'unavailable' => 'bg-gray-200 text-gray-600',
        default       => 'bg-brand-yellow text-brand-black',
    };
    $tag = $href ? 'a' : 'div';
    $linkAttr = $href ? "href=\"{$href}\"" : '';
@endphp

<{{ $tag }} {{ $href ? "href=\"{$href}\"" : '' }}
    {{ $attributes->class([
        'product-card product-card-hover group block',
        'cursor-pointer' => $href,
    ]) }}
>
    {{-- Image Container --}}
    <div class="relative aspect-4/3 overflow-hidden bg-gray-50">
        <img
            src="{{ $image }}"
            alt="{{ $title }}"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            loading="lazy"
            width="800"
            height="600"
        >

        {{-- Badge --}}
        @if ($badge)
            <span class="absolute top-3 left-3 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClasses }}">
                {{ $badge }}
            </span>
        @endif

        {{-- Category badge --}}
        @if ($category)
            <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-white/90 px-2.5 py-1 text-xs font-medium text-brand-black/70 backdrop-blur-sm">
                @if ($icon)
                    <x-icon :name="$icon" class="size-3" />
                @endif
                {{ $category }}
            </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4 sm:p-5">
        <h3 class="text-sm font-semibold leading-snug text-brand-black transition group-hover:text-brand-black sm:text-base">
            {{ $title }}
        </h3>

        @if ($description)
            <p class="mt-1 text-xs leading-relaxed text-brand-black/55">{{ $description }}</p>
        @endif

        <div class="mt-3 flex items-center justify-between">
            @if ($showPrice && $price)
                <p class="text-base font-bold text-brand-black sm:text-lg">
                    {{ $price }}
                </p>
            @elseif (!$showPrice)
                <p class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-black/45">
                    <x-icon name="lock" class="size-3" />
                    Harga partai — aktifkan akun
                </p>
            @else
                <span></span>
            @endif

            <span class="inline-flex size-8 items-center justify-center rounded-full bg-brand-yellow opacity-0 transition duration-200 group-hover:opacity-100">
                <x-icon name="arrow-right" class="size-3.5 text-brand-black" />
            </span>
        </div>
    </div>
</{{ $tag }}>
