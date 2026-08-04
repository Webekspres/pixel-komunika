@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Kembali',
])

<div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
    <div>
        @if ($eyebrow)
            <p class="text-sm font-medium text-zinc-500">{{ $eyebrow }}</p>
        @endif

        <flux:heading size="xl" class="mt-1">{{ $title }}</flux:heading>

        @if ($description)
            <flux:text class="mt-2 max-w-3xl">{{ $description }}</flux:text>
        @endif
    </div>

    <div class="flex items-center gap-2">
        @if ($backHref)
            <flux:button :href="$backHref" variant="ghost" size="sm" icon="arrow-left">{{ $backLabel }}</flux:button>
        @endif

        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>
</div>
