@props([
    'label',
    'onRemove' => null,
])

<span {{ $attributes->class('inline-flex items-center gap-1.5 rounded-md bg-zinc-100 border border-zinc-200/80 px-3 py-1 text-xs font-semibold text-zinc-800') }}>
    <span>{{ $label }}</span>
    @if ($onRemove)
        <button
            type="button"
            wire:click="{{ $onRemove }}"
            class="text-zinc-400 hover:text-zinc-700 transition-colors"
            title="Hapus filter"
        >
            <x-icon name="x" class="size-3.5" />
        </button>
    @endif
</span>
