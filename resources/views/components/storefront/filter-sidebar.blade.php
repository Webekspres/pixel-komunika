@props([
    'categories' => [],
    'brands' => [],
    'selectedCategory' => 'all',
    'selectedBrand' => 'all',
    'inStockOnly' => false,
    'minPrice' => null,
    'maxPrice' => null,
])

<aside {{ $attributes->class('w-full space-y-6') }}>
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
        <div class="flex items-center gap-2">
            <x-icon name="sliders-horizontal" class="size-4 text-zinc-900" />
            <h3 class="font-bold text-zinc-900 text-base">Filter Produk</h3>
        </div>
        
        <button
            wire:click="resetFilters"
            type="button"
            class="text-xs font-semibold text-amber-700 hover:text-amber-800 transition-colors"
        >
            Hapus Semua
        </button>
    </div>

    {{-- Filter by Category --}}
    <div class="space-y-3 border-b border-zinc-100 pb-5">
        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Kategori</h4>
        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
            <label class="flex items-center gap-2.5 text-xs text-zinc-700 hover:text-zinc-900 cursor-pointer">
                <input
                    type="radio"
                    name="category_filter"
                    wire:model.live="selectedCategory"
                    value="all"
                    class="size-4 text-amber-500 border-zinc-300 focus:ring-amber-400"
                />
                <span class="font-medium">Semua Kategori</span>
            </label>
            @foreach ($categories as $cat)
                <label class="flex items-center justify-between text-xs text-zinc-700 hover:text-zinc-900 cursor-pointer">
                    <div class="flex items-center gap-2.5">
                        <input
                            type="radio"
                            name="category_filter"
                            wire:model.live="selectedCategory"
                            value="{{ $cat->id }}"
                            class="size-4 text-amber-500 border-zinc-300 focus:ring-amber-400"
                        />
                        <span class="font-medium">{{ $cat->name }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Filter by Brand --}}
    @if ($brands->isNotEmpty())
        <div class="space-y-3 border-b border-zinc-100 pb-5">
            <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Merek / Brand</h4>
            <div class="flex flex-wrap gap-1.5">
                <button
                    wire:click="$set('selectedBrand', 'all')"
                    type="button"
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors {{ $selectedBrand === 'all' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}"
                >
                    Semua
                </button>
                @foreach ($brands as $brand)
                    <button
                        wire:click="$set('selectedBrand', '{{ $brand->id }}')"
                        type="button"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors {{ (string)$selectedBrand === (string)$brand->id ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}"
                    >
                        {{ $brand->name }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Availability Toggle --}}
    <div class="space-y-3 border-b border-zinc-100 pb-5">
        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Ketersediaan Stok</h4>
        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-xs font-medium text-zinc-700">Hanya Produk Ready</span>
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
        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Kisaran Harga (Rp)</h4>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <span class="text-[10px] text-zinc-400 block mb-1">Minimal</span>
                <input
                    type="number"
                    wire:model.live.debounce.400ms="minPrice"
                    placeholder="0"
                    class="w-full text-xs p-2 rounded-xl border border-zinc-200 focus:outline-none focus:ring-1 focus:ring-amber-400"
                />
            </div>
            <div>
                <span class="text-[10px] text-zinc-400 block mb-1">Maksimal</span>
                <input
                    type="number"
                    wire:model.live.debounce.400ms="maxPrice"
                    placeholder="Tanpa batas"
                    class="w-full text-xs p-2 rounded-xl border border-zinc-200 focus:outline-none focus:ring-1 focus:ring-amber-400"
                />
            </div>
        </div>
    </div>
</aside>
