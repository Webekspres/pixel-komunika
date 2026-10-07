@props([
    'title' => 'Tidak ada produk yang cocok',
    'description' => 'Tidak ada produk yang sesuai dengan filter atau kata kunci saat ini. Hapus filter untuk melihat semua produk.',
    'onReset' => 'resetFilters',
])

<div {{ $attributes->class('flex flex-col items-center justify-center py-16 px-4 text-center') }}>
    <div class="mb-6">
        <img
            src="{{ asset('assets/mascot/Maskot-base.webp') }}"
            alt=""
            class="w-36 h-36 object-contain"
            width="144"
            height="144"
        >
    </div>

    <h3 class="text-xl font-bold text-zinc-900 mb-2">{{ $title }}</h3>
    <p class="text-sm text-zinc-500 max-w-md leading-relaxed mb-6">{{ $description }}</p>

    <button
        wire:click="{{ $onReset }}"
        type="button"
        class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-brand-yellow px-6 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition-colors"
    >
        <x-icon name="rotate-ccw" class="size-4" />
        <span>Hapus filter dan pencarian</span>
    </button>
</div>
