@props([
    'categories' => [],
    'brands' => [],
    'selectedCategory' => 'all',
    'selectedBrand' => 'all',
    'inStockOnly' => false,
    'minPrice' => null,
    'maxPrice' => null,
    'group' => 'filter',
])

<aside {{ $attributes->class('w-full space-y-6') }}>
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-brand-black/8 pb-4">
        <div class="flex items-center gap-2">
            <x-icon name="sliders-horizontal" class="size-4 text-brand-black" />
            <h3 class="text-base font-bold text-brand-black">Filter Produk</h3>
        </div>
        
        <button
            wire:click="resetFilters"
            type="button"
            class="text-xs font-semibold text-brand-black/55 transition-colors hover:text-brand-black"
        >
            Hapus Semua
        </button>
    </div>

    {{-- Filter by Category --}}
    <div class="space-y-3 border-b border-brand-black/8 pb-5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-brand-black">Kategori</h4>
        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
            <label class="flex cursor-pointer items-center gap-2.5 text-xs text-brand-black/70 hover:text-brand-black">
                <input
                    type="radio"
                    name="category_filter_{{ $group }}"
                    wire:model.live="selectedCategory"
                    value="all"
                    class="size-4 border-zinc-300 accent-amber-500 text-amber-500 focus:ring-amber-400"
                />
                <span class="font-medium">Semua Kategori</span>
            </label>
            @foreach ($categories as $cat)
                <label class="flex cursor-pointer items-center justify-between text-xs text-brand-black/70 hover:text-brand-black">
                    <div class="flex items-center gap-2.5">
                        <input
                            type="radio"
                            name="category_filter_{{ $group }}"
                            wire:model.live="selectedCategory"
                            value="{{ (string) $cat->id }}"
                            class="size-4 accent-amber-500 text-amber-500 border-zinc-300 focus:ring-amber-400"
                        />
                        <span class="font-medium">{{ $cat->name }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Filter by Brand --}}
    @if ($brands->isNotEmpty())
        <div class="space-y-3 border-b border-brand-black/8 pb-5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-brand-black">Merek / Brand</h4>
            <div class="flex flex-wrap gap-1.5">
                <button
                    wire:click="$set('selectedBrand', 'all')"
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $selectedBrand === 'all' ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                >
                    Semua
                </button>
                @foreach ($brands as $brand)
                    <button
                        wire:click="$set('selectedBrand', '{{ $brand->id }}')"
                        type="button"
                        class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ (string)$selectedBrand === (string)$brand->id ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                    >
                        {{ $brand->name }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Availability Toggle --}}
    <div class="space-y-3 border-b border-brand-black/8 pb-5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-brand-black">Ketersediaan Stok</h4>
        <label class="flex cursor-pointer items-center justify-between">
            <span class="text-xs font-medium text-brand-black/72">Hanya Produk Ready</span>
            <input
                type="checkbox"
                wire:model.live="inStockOnly"
                class="sr-only peer"
            />
            <div class="relative w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-400"></div>
        </label>
    </div>

    {{-- Price Range Filter --}}
    <div class="space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-brand-black">Kisaran Harga (Rp)</h4>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <span class="mb-1 block text-[10px] text-brand-black/45">Minimal</span>
                <input
                    type="number"
                    wire:model.live.debounce.400ms="minPrice"
                    placeholder="0"
                    class="w-full rounded-2xl border border-brand-black/10 bg-white px-3 py-2.5 text-xs text-brand-black focus:outline-none focus:ring-1 focus:ring-amber-400"
                />
            </div>
            <div>
                <span class="mb-1 block text-[10px] text-brand-black/45">Maksimal</span>
                <input
                    type="number"
                    wire:model.live.debounce.400ms="maxPrice"
                    placeholder="Tanpa batas"
                    class="w-full rounded-2xl border border-brand-black/10 bg-white px-3 py-2.5 text-xs text-brand-black focus:outline-none focus:ring-1 focus:ring-amber-400"
                />
            </div>
        </div>
    </div>
</aside>
