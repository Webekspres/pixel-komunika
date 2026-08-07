@props([
    'items' => [], // array of ['label' => string, 'href' => string|null]
])

<nav {{ $attributes->class('flex items-center gap-2 text-xs text-zinc-500 font-medium') }} aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-zinc-900 transition-colors flex items-center gap-1">
        <x-icon name="home" class="size-3.5" />
        <span>Beranda</span>
    </a>

    @foreach ($items as $item)
        <x-icon name="chevron-right" class="size-3 text-zinc-400 shrink-0" />

        @if (!empty($item['href']))
            <a href="{{ $item['href'] }}" class="hover:text-zinc-900 transition-colors truncate max-w-[150px] sm:max-w-none">
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-zinc-900 font-semibold truncate max-w-[180px] sm:max-w-none">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
