@props([
    'label',
    'value',
    'description' => null,
    'icon'        => null,
    'trend'       => null,  // 'up' | 'down' | null
    'trendLabel'  => null,
    'accent'      => false, // adds a left yellow accent bar
])

<flux:card {{ $attributes->class(['relative overflow-hidden', 'border-l-2 border-amber-400' => $accent]) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold tracking-wide text-zinc-400 uppercase">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-zinc-950 sm:text-3xl">{{ $value }}</p>

            @if ($description)
                <flux:text size="sm" class="mt-1">{{ $description }}</flux:text>
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
            <div class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                <x-icon :name="$icon" class="size-5 text-amber-600" />
            </div>
        @endif
    </div>
</flux:card>
