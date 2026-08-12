<div class="min-h-screen bg-zinc-50/70" x-data="{ mobileFilterOpen: false }">

    {{-- Notification Toast --}}
    @if (session()->has('success'))
        <div class="fixed bottom-5 right-5 z-50 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="check-circle" class="size-5" />
            <span>{{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="underline ml-2 text-emerald-100 hover:text-white">Lihat Keranjang</a>
        </div>
    @endif

    {{-- Storefront Header --}}
    <x-storefront.navbar :cart-count="$cartCount" />

    {{-- Main Container --}}
    <div class="container-2xl py-6 sm:py-8 lg:py-10">

        {{-- Breadcrumb --}}
        <div class="mb-6">
            <x-storefront.breadcrumb :items="[['label' => 'Katalog Produk', 'href' => null]]" />
        </div>

        {{-- Page Header Title --}}
        <div class="storefront-panel-soft mb-8 flex flex-col gap-5 p-6 sm:p-7 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.28em] text-brand-black/45">
                    <span class="inline-block h-px w-8 bg-brand-yellow"></span>
                    katalog storefront
                </p>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl lg:text-[2.6rem]">Katalog Produk POS</h1>
                <p class="mt-2 text-sm text-zinc-500 max-w-2xl">
                    Jelajahi seluruh inventaris operasional toko. Harga grosir dan partai terbuka untuk akun terverifikasi.
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
        <div class="grid gap-8 lg:grid-cols-[260px_1fr] xl:grid-cols-[280px_1fr]">

            {{-- Desktop Sidebar --}}
            <div class="hidden lg:block">
                <div class="storefront-panel sticky top-24 p-6">
                    <x-storefront.filter-sidebar
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
                            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-zinc-400" />
                            <input
                                type="search"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Cari nama produk atau SKU..."
                            class="w-full rounded-2xl border border-brand-black/10 bg-white py-3 pr-4 pl-10 text-xs text-zinc-900 focus:outline-none focus:ring-1 focus:ring-amber-400"
                            />
                        </div>

                        {{-- Mobile Filter Button --}}
                        <button
                            @click="mobileFilterOpen = true"
                            type="button"
                            class="inline-flex shrink-0 items-center gap-2 rounded-2xl border border-brand-black/10 bg-white px-3.5 py-3 text-xs font-bold text-zinc-800 hover:bg-zinc-100 lg:hidden"
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
                            <span class="text-xs text-zinc-400 hidden xl:inline">Urutkan:</span>
                            <select
                                wire:model.live="sort"
                                class="rounded-2xl border border-brand-black/10 bg-white px-3 py-2.5 text-xs font-semibold text-zinc-800 focus:outline-none focus:ring-1 focus:ring-amber-400"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="name_asc">Nama (A - Z)</option>
                                <option value="name_desc">Nama (Z - A)</option>
                                <option value="price_low">Harga: Terendah → Tertinggi</option>
                                <option value="price_high">Harga: Tertinggi → Terendah</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filter Chips Row --}}
                @if ($selectedCategory !== 'all' || $selectedBrand !== 'all' || !empty($search) || $inStockOnly || $minPrice || $maxPrice)
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-xs text-zinc-400 font-medium mr-1">Filter Aktif:</span>

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
                            class="text-xs font-semibold text-red-600 hover:text-red-700 ml-2"
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
                    <div class="grid gap-5 grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($products as $product)
                            <x-storefront.product-card
                                :title="$product->name"
                                :category="$product->category->name"
                                :sku="$product->sku"
                                :stock-label="$product->inventorySnapshot?->quantity_available > 0 ? 'Stok: '.$product->inventorySnapshot->quantity_available : 'Habis'"
                                :stock-variant="$product->inventorySnapshot?->quantity_available > 0 ? 'available' : 'unavailable'"
                                :show-price="auth()->user()?->canViewPrices()"
                                :price="'Rp '.number_format($product->listPriceAmount() ?? 0, 0, ',', '.')"
                            >
                                <x-slot:actions>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            wire:navigate
                                            class="inline-flex items-center justify-center rounded-2xl bg-zinc-100 px-4 py-3 text-xs font-bold text-brand-black transition hover:bg-zinc-200"
                                        >
                                            Detail
                                        </a>

                                        <button
                                            wire:click="addToCart({{ $product->id }})"
                                            type="button"
                                            class="inline-flex items-center justify-center gap-1 rounded-2xl bg-brand-yellow px-4 py-3 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-soft"
                                        >
                                            <x-icon name="shopping-cart" class="size-3.5" />
                                            <span>+ Cart</span>
                                        </button>
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
        class="fixed inset-0 z-50 bg-zinc-950/40 backdrop-blur-xs lg:hidden"
        x-cloak
    >
        <div
            @click.outside="mobileFilterOpen = false"
            class="fixed inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-white p-6 shadow-2xl space-y-6"
        >
            <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
                <h3 class="font-bold text-zinc-900 text-lg">Filter Katalog</h3>
                <button @click="mobileFilterOpen = false" class="text-zinc-400 hover:text-zinc-700">
                    <x-icon name="x" class="size-6" />
                </button>
            </div>

            <x-storefront.filter-sidebar
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
                    class="flex-1 py-3 rounded-2xl border border-zinc-200 text-xs font-bold text-zinc-700 text-center"
                >
                    Reset
                </button>
                <button
                    @click="mobileFilterOpen = false"
                    type="button"
                    class="flex-1 py-3 rounded-2xl bg-amber-400 text-xs font-bold text-brand-black text-center shadow-xs"
                >
                    Terapkan
                </button>
            </div>
        </div>
    </div>
</div>
