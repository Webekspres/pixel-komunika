@props([
    'title' => null,
    'description' => null,
])

<flux:card {{ $attributes->class('space-y-5') }}>
    @if ($title || $description || isset($actions))
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                @if ($title)
                    <flux:heading size="lg">{{ $title }}</flux:heading>
                @endif

                @if ($description)
                    <flux:text class="mt-2">{{ $description }}</flux:text>
                @endif
            </div>

            @if (isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</flux:card>
