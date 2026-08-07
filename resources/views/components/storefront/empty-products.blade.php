@props([
    'title' => 'Tidak Ada Produk Ditemukan',
    'description' => 'Maaf, kami tidak dapat menemukan produk yang sesuai dengan filter atau kata kunci pencarian Anda.',
    'onReset' => 'resetFilters',
])

<div {{ $attributes->class('flex flex-col items-center justify-center py-16 px-4 text-center') }}>
    <div class="relative mb-6">
        <div class="absolute inset-0 rounded-full bg-amber-100 blur-xl opacity-60"></div>
        <img
            src="{{ asset('assets/mascot/Maskot-base.webp') }}"
            alt="Pixel Komunika Mascot"
            class="relative z-10 w-36 h-36 object-contain drop-shadow-md"
            width="144"
            height="144"
        >
    </div>

    <h3 class="text-xl font-bold text-zinc-900 mb-2">{{ $title }}</h3>
    <p class="text-sm text-zinc-500 max-w-md leading-relaxed mb-6">{{ $description }}</p>

    <button
        wire:click="{{ $onReset }}"
        type="button"
        class="inline-flex items-center gap-2 rounded-2xl bg-amber-400 px-6 py-3 text-xs font-bold text-brand-black hover:bg-amber-300 transition-all shadow-sm"
    >
        <x-icon name="rotate-ccw" class="size-4" />
        <span>Reset Filter & Pencarian</span>
    </button>
</div>
