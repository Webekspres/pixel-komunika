@props([
    'items' => [], // array of ['label' => string, 'href' => string|null]
])

<nav {{ $attributes->class('flex flex-wrap items-center gap-2 text-xs font-semibold text-brand-black/45') }} aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 rounded-full bg-white/70 px-3 py-1.5 transition hover:text-brand-black">
        <x-icon name="home" class="size-3.5" />
        <span>Beranda</span>
    </a>

    @foreach ($items as $item)
        <x-icon name="chevron-right" class="size-3 shrink-0 text-brand-black/25" />

        @if (!empty($item['href']))
            <a href="{{ $item['href'] }}" class="truncate rounded-full bg-white/55 px-3 py-1.5 transition hover:text-brand-black sm:max-w-none">
                {{ $item['label'] }}
            </a>
        @else
            <span class="truncate rounded-full bg-brand-black px-3 py-1.5 font-bold text-brand-white sm:max-w-none">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
