@props([
    'label',
    'value',
    'description' => null,
])

<flux:card class="space-y-2">
    <p class="text-sm font-medium text-zinc-500">{{ $label }}</p>
    <p class="text-2xl font-semibold tracking-tight">{{ $value }}</p>
    @if ($description)
        <flux:text size="sm">{{ $description }}</flux:text>
    @endif
</flux:card>
