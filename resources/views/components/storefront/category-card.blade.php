@props([
    'title',
    'description' => null,
    'image'       => null,
    'icon'        => null,
    'href'        => null,
    'size'        => 'normal', // normal | large
    'theme'       => 'yellow', // yellow | dark | gray
])

@php
    $themes = [
        'yellow' => [
            'bg'    => 'bg-brand-yellow',
            'text'  => 'text-brand-black',
            'muted' => 'text-brand-black/75',
            'icon'  => 'text-brand-black',
        ],
        'dark' => [
            'bg'    => 'bg-brand-black',
            'text'  => 'text-brand-white',
            'muted' => 'text-brand-white/70',
            'icon'  => 'text-brand-white',
        ],
        'gray' => [
            'bg'    => 'bg-gray-100',
            'text'  => 'text-brand-black',
            'muted' => 'text-brand-black/65',
            'icon'  => 'text-brand-black',
        ],
    ];
    $t = $themes[$theme] ?? $themes['yellow'];
    $minHeight = $size === 'large' ? 'min-h-80 sm:min-h-[420px]' : 'min-h-44 sm:min-h-52';
@endphp

<a
    href="{{ $href ?? '#' }}"
    {{ $attributes->class([
        'group relative overflow-hidden rounded-3xl transition duration-300',
        $t['bg'],
        'hover:ring-2 hover:ring-brand-yellow hover:ring-offset-2' => $theme !== 'yellow',
    ]) }}
>
    {{-- Background image --}}
    @if ($image)
        <img
            src="{{ $image }}"
            alt=""
            aria-hidden="true"
            class="absolute inset-0 h-full w-full object-cover opacity-25 transition duration-500 group-hover:scale-105 group-hover:opacity-30"
            loading="lazy"
            width="1200"
            height="800"
        >
    @endif

    {{-- Content --}}
    <div class="relative flex {{ $minHeight }} flex-col justify-end p-6 sm:p-8">
        @if ($icon)
            <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-white/20">
                <x-icon :name="$icon" class="size-5 {{ $t['icon'] }}" />
            </div>
        @endif

        <h3 class="text-xl font-bold {{ $t['text'] }} sm:text-2xl">{{ $title }}</h3>

        @if ($description)
            <p class="mt-2 text-sm leading-relaxed {{ $t['muted'] }}">{{ $description }}</p>
        @endif

        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold {{ $t['text'] }}">
            <span>Lihat katalog</span>
            <x-icon name="arrow-right" class="size-4 transition duration-200 group-hover:translate-x-1" />
        </div>
    </div>
</a>
