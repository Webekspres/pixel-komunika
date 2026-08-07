@props([
    'eyebrow'   => null,
    'title',
    'description' => null,
    'backHref'    => null,
    'backLabel'   => 'Kembali',
])

<div {{ $attributes->class('flex flex-col gap-5 md:flex-row md:items-start md:justify-between') }}>
    <div class="min-w-0 flex-1">
        @if ($eyebrow)
            <p class="mb-1.5 flex items-center gap-2 text-xs font-semibold tracking-widest text-zinc-400 uppercase">
                {{ $eyebrow }}
            </p>
        @endif

        <flux:heading size="xl" class="mt-0 text-zinc-950">{{ $title }}</flux:heading>

        @if ($description)
            <flux:text class="mt-2 max-w-3xl text-zinc-500">{{ $description }}</flux:text>
        @endif
    </div>

    <div class="flex shrink-0 items-center gap-2">
        @if ($backHref)
            <flux:button :href="$backHref" variant="ghost" size="sm" icon="arrow-left">{{ $backLabel }}</flux:button>
        @endif

        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>
</div>
