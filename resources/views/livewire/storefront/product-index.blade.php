<div class="min-h-screen bg-surface-2" x-data="{ mobileFilterOpen: false }" @keydown.escape.window="mobileFilterOpen = false">

    @if (session()->has('success'))
        <div class="fixed right-5 bottom-5 z-50 flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="check-circle" class="size-5" />
            <span>{{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="ml-2 text-emerald-100 underline hover:text-white">Lihat Keranjang</a>
        </div>
    @endif

    <div class="container-2xl py-6 sm:py-8 lg:py-10">

        <div class="mb-6">
            <x-storefront.breadcrumb :items="[['label' => 'Katalog Produk', 'href' => null]]" />
        </div>

        <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-black tracking-tight text-zinc-950 sm:text-4xl">Katalog Produk</h1>
                <p class="mt-2 max-w-2xl text-sm text-zinc-500">
                    Jelajahi inventaris operasional toko. Harga grosir dan partai terbuka untuk akun terverifikasi.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="storefront-pill">{{ $products->total() }} produk</span>
                @if ($selectedCategory !== 'all')
                    <span class="storefront-pill">Kategori terfilter</span>
                @endif
                @if ($inStockOnly)
                    <span class="storefront-pill">Ready stock</span>
                @endif
            </div>
        </div>

        {{-- Main Layout: 2 Columns (Filter Sidebar + Toolbar/Grid) --}}
        <div class="grid gap-8 lg:grid-cols-[auto_1fr]">

            {{-- Desktop Sidebar --}}
            <div class="hidden w-64 shrink-0 lg:block">
                <div class="storefront-panel sticky top-24 p-6">
                    <x-storefront.filter-sidebar
                        group="desktop"
                        :categories="$categories"
                        :brands="$brands"
                        :selectedCategory="$selectedCategory"
                        :selectedBrand="$selectedBrand"
                        :inStockOnly="$inStockOnly"
                        :minPrice="$minPrice"
                        :maxPrice="$maxPrice"
                    />
                </div>
            </div>

            {{-- Main Content Area --}}
            <div class="space-y-6">

                {{-- Toolbar --}}
                <div class="storefront-panel-soft flex flex-col items-stretch justify-between gap-4 p-4 sm:flex-row sm:items-center">
                    
                    {{-- Search Input & Mobile Filter Toggle --}}
                    <div class="flex items-center gap-2 flex-1">
                        <div class="relative flex-1">
                            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-zinc-500" />
                            <input
                                type="search"
                                wire:model.live.debounce.300ms="search"
                                aria-label="Cari nama produk atau SKU"
                                placeholder="Cari nama produk atau SKU..."
                                class="min-h-11 w-full rounded-md border border-brand-black/20 bg-white pr-4 pl-10 text-sm text-zinc-900 focus:border-brand-black focus:ring-2 focus:ring-brand-black focus:outline-none"
                            />
                        </div>

                        {{-- Mobile Filter Button --}}
                        <button
                            @click="mobileFilterOpen = true"
                            type="button"
                            :aria-expanded="mobileFilterOpen"
                            class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md border border-brand-black/20 bg-white px-3.5 text-xs font-bold text-zinc-800 hover:bg-zinc-100 lg:hidden"
                        >
                            <x-icon name="sliders-horizontal" class="size-4" />
                            <span>Filter</span>
                        </button>
                    </div>

                    {{-- Sort Dropdown & Result Count --}}
                    <div class="flex items-center justify-between gap-4 border-t border-zinc-100 pt-3 sm:justify-end sm:border-t-0 sm:pt-0">
                        <span class="text-xs text-zinc-500 font-medium">
                            Menampilkan <strong class="text-zinc-900">{{ $products->total() }}</strong> produk
                        </span>

                        <div class="flex items-center gap-2">
                            <label for="catalog-sort" class="text-xs text-zinc-600 hidden xl:inline">Urutkan:</label>
                            <select
                                id="catalog-sort"
                                aria-label="Urutkan produk"
                                wire:model.live="sort"
                                class="min-h-11 rounded-md border border-brand-black/20 bg-white px-3 text-xs font-semibold text-zinc-800 focus:border-brand-black focus:ring-2 focus:ring-brand-black focus:outline-none"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="name_asc">Nama (A - Z)</option>
                                <option value="name_desc">Nama (Z - A)</option>
                                @if (auth()->user()?->canViewPrices())
                                <option value="price_low">Harga: Terendah → Tertinggi</option>
                                <option value="price_high">Harga: Tertinggi → Terendah</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filter Chips Row --}}
                @if ($selectedCategory !== 'all' || $selectedBrand !== 'all' || !empty($search) || $inStockOnly || $minPrice || $maxPrice)
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-xs text-zinc-600 font-medium mr-1">Filter Aktif:</span>

                        @if ($selectedCategory !== 'all')
                            @php($catObj = $categories->firstWhere('id', $selectedCategory))
                            <x-storefront.filter-chip :label="'Kategori: '.($catObj?->name ?? $selectedCategory)" onRemove="$set('selectedCategory', 'all')" />
                        @endif

                        @if ($selectedBrand !== 'all')
                            @php($brandObj = $brands->firstWhere('id', $selectedBrand))
                            <x-storefront.filter-chip :label="'Merek: '.($brandObj?->name ?? $selectedBrand)" onRemove="$set('selectedBrand', 'all')" />
                        @endif

                        @if (!empty($search))
                            <x-storefront.filter-chip :label="'Cari: &quot;'.$search.'&quot;'" onRemove="$set('search', '')" />
                        @endif

                        @if ($inStockOnly)
                            <x-storefront.filter-chip label="Stok Ready" onRemove="$set('inStockOnly', false)" />
                        @endif

                        @if ($minPrice)
                            <x-storefront.filter-chip :label="'Min: Rp '.number_format($minPrice, 0, ',', '.')" onRemove="$set('minPrice', null)" />
                        @endif

                        @if ($maxPrice)
                            <x-storefront.filter-chip :label="'Max: Rp '.number_format($maxPrice, 0, ',', '.')" onRemove="$set('maxPrice', null)" />
                        @endif

                        <button
                            wire:click="resetFilters"
                            type="button"
                            class="ml-2 inline-flex min-h-11 items-center text-xs font-semibold text-red-700 hover:underline"
                        >
                            Hapus Semua
                        </button>
                    </div>
                @endif

                {{-- Product Grid --}}
                @if ($products->isEmpty())
                    <div>
                        <x-storefront.empty-products />
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4">
                        @foreach ($products as $product)
                            @php($primaryMedia = $product->media->firstWhere('is_primary', true) ?? $product->media->first())
                            <x-storefront.product-card
                                :title="$product->displayName()"
                                :image="$primaryMedia?->url()"
                                :badge="$product->enrichment?->label"
                                :category="$product->category->name"
                                :sku="$product->sku"
                                :stock-label="$product->inventorySnapshot?->quantity_available > 0 ? 'Stok: '.$product->inventorySnapshot->quantity_available : 'Habis'"
                                :stock-variant="$product->inventorySnapshot?->quantity_available > 0 ? 'available' : 'unavailable'"
                                :show-price="auth()->user()?->canViewPrices()"
                                :price="'Rp '.number_format($product->listPriceAmount() ?? 0, 0, ',', '.')"
                            >
                                <x-slot:actions>
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            wire:navigate
                                            class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-md bg-zinc-100 px-4 text-xs font-bold text-brand-black transition hover:bg-zinc-200"
                                        >
                                            Detail
                                        </a>

                                        @if (auth()->guest() || auth()->user()->canViewPrices())
                                        <button
                                            wire:click="addToCart({{ $product->id }})"
                                            type="button"
                                            class="inline-flex min-h-11 flex-1 items-center justify-center gap-1 rounded-md bg-brand-yellow px-4 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-soft"
                                        >
                                            <x-icon name="shopping-cart" class="size-3.5" />
                                            <span>+ Keranjang</span>
                                        </button>
                                        @endif
                                    </div>
                                </x-slot:actions>
                            </x-storefront.product-card>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile Filter Drawer / Bottom Sheet --}}
    <div
        x-show="mobileFilterOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-zinc-950/50 lg:hidden"
        x-cloak
    >
        <div
            @click.outside="mobileFilterOpen = false"
            x-trap.noscroll="mobileFilterOpen"
            role="dialog"
            aria-modal="true"
            aria-labelledby="mobile-filter-title"
            class="fixed inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-xl bg-white p-6 shadow-2xl space-y-6"
        >
            <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
                <h3 id="mobile-filter-title" class="font-bold text-zinc-900 text-lg">Filter Katalog</h3>
                <button type="button" @click="mobileFilterOpen = false" class="inline-flex size-11 items-center justify-center rounded-lg text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900" aria-label="Tutup filter">
                    <x-icon name="x" class="size-6" />
                </button>
            </div>

            <x-storefront.filter-sidebar
                group="mobile"
                :categories="$categories"
                :brands="$brands"
                :selectedCategory="$selectedCategory"
                :selectedBrand="$selectedBrand"
                :inStockOnly="$inStockOnly"
                :minPrice="$minPrice"
                :maxPrice="$maxPrice"
            />

            <div class="sticky bottom-0 bg-white pt-3 border-t border-zinc-100 flex items-center gap-3">
                <button
                    wire:click="resetFilters"
                    @click="mobileFilterOpen = false"
                    type="button"
                    class="min-h-11 flex-1 rounded-md border border-zinc-300 text-xs font-bold text-zinc-700 text-center"
                >
                    Reset
                </button>
                <button
                    @click="mobileFilterOpen = false"
                    type="button"
                    class="min-h-11 flex-1 rounded-md bg-brand-yellow text-xs font-bold text-brand-black text-center"
                >
                    Lihat {{ $products->total() }} produk
                </button>
            </div>
        </div>
    </div>
</div>
