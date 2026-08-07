<div class="min-h-screen bg-zinc-50 pb-24 lg:pb-12">

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

    {{-- Main PDP Container --}}
    <div class="container-2xl py-8 sm:py-10">

        {{-- Breadcrumb --}}
        <div class="mb-8">
            <x-storefront.breadcrumb :items="[
                ['label' => 'Katalog Produk', 'href' => route('products.index')],
                ['label' => $product->category->name, 'href' => route('products.index', ['kategori' => $product->category_id])],
                ['label' => $product->name, 'href' => null],
            ]" />
        </div>

        {{-- Product Details Section --}}
        <div class="grid gap-10 lg:grid-cols-2 bg-white rounded-3xl border border-zinc-200 p-6 sm:p-10 shadow-xs mb-12">

            {{-- Product Gallery / Preview --}}
            <div class="space-y-4">
                <div class="aspect-square w-full rounded-2xl bg-zinc-100 border border-zinc-200/80 flex items-center justify-center p-8 overflow-hidden relative group">
                    <x-icon name="package" class="size-32 text-zinc-300 group-hover:scale-105 transition-transform duration-300" />
                    
                    {{-- Badges on preview --}}
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100/90 backdrop-blur-xs px-3 py-1 rounded-full">
                            {{ $product->category->name }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Product Details Information --}}
            <div class="flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    {{-- Brand & Stock badge --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-zinc-400">
                            Merek: <strong class="text-zinc-700">{{ $product->brand?->name ?? 'Pixel Komunika' }}</strong>
                        </span>

                        @if ($product->inventorySnapshot?->quantity_available > 0)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                Stok Ready: {{ $product->inventorySnapshot->quantity_available }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-200">
                                Stok Habis
                            </span>
                        @endif
                    </div>

                    {{-- Title & SKU --}}
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-950">
                        {{ $product->name }}
                    </h1>
                    <p class="text-xs text-zinc-400 font-mono">SKU: {{ $product->sku }}</p>

                    {{-- Price Box --}}
                    <div class="rounded-2xl bg-zinc-50 border border-zinc-200 p-5 space-y-2">
                        <span class="text-xs text-zinc-500 font-medium block">Harga Grosir B2B (Tier 1):</span>
                        
                        @if (auth()->user()?->canViewPrices())
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-extrabold text-zinc-950">
                                    Rp {{ number_format($product->latestPrice?->price_wholesale_tier1 ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-zinc-400">/ unit</span>
                            </div>

                            @if ($product->latestPrice?->price_wholesale_tier2)
                                <p class="text-xs text-amber-700 font-semibold pt-1">
                                    💡 Tier 2: Rp {{ number_format($product->latestPrice->price_wholesale_tier2, 0, ',', '.') }} untuk pesanan dalam jumlah besar.
                                </p>
                            @endif
                        @else
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                                <span>🔒 Harga grosir hanya dapat dilihat oleh pelanggan terverifikasi.</span>
                                <a href="{{ route('login') }}" class="font-bold underline ml-2 shrink-0">Masuk Akun</a>
                            </div>
                        @endif
                    </div>

                    {{-- Specification List --}}
                    <div class="space-y-2 pt-2">
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Spesifikasi Fisik</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs text-zinc-600 bg-zinc-50/50 p-4 rounded-xl border border-zinc-100">
                            <div>
                                <span class="text-zinc-400 block">Berat Unit:</span>
                                <span class="font-semibold text-zinc-800">{{ $product->weight_grams }} gram</span>
                            </div>
                            <div>
                                <span class="text-zinc-400 block">Dimensi (P x L x T):</span>
                                <span class="font-semibold text-zinc-800">{{ $product->length_cm ?? '-' }} x {{ $product->width_cm ?? '-' }} x {{ $product->height_cm ?? '-' }} cm</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Area (Desktop & Tablet) --}}
                <div class="border-t border-zinc-200 pt-6 space-y-4">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        
                        {{-- Quantity Selector --}}
                        <div class="flex items-center justify-between border border-zinc-300 rounded-2xl overflow-hidden bg-white px-2 py-1">
                            <button
                                wire:click="decrementQuantity"
                                type="button"
                                class="size-9 flex items-center justify-center text-zinc-600 hover:bg-zinc-100 font-bold rounded-xl text-base"
                            >-</button>
                            <span class="px-6 font-bold text-zinc-900 text-sm">{{ $quantity }}</span>
                            <button
                                wire:click="incrementQuantity"
                                type="button"
                                class="size-9 flex items-center justify-center text-zinc-600 hover:bg-zinc-100 font-bold rounded-xl text-base"
                            >+</button>
                        </div>

                        {{-- Add to Cart --}}
                        <button
                            wire:click="addToCart"
                            type="button"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-amber-400 py-4 px-6 font-bold text-brand-black hover:bg-amber-300 transition-all shadow-md active:scale-[0.99]"
                        >
                            <x-icon name="shopping-cart" class="size-5" />
                            <span>+ Tambahkan ke Keranjang</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products Section --}}
        @if ($relatedProducts->isNotEmpty())
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-zinc-950">Produk Terkait di Kategori {{ $product->category->name }}</h3>
                
                <div class="grid gap-5 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $rel)
                        <div class="group relative flex flex-col justify-between rounded-3xl border border-zinc-200 bg-white p-4 shadow-xs hover:-translate-y-1 hover:shadow-md transition-all">
                            <div>
                                <h4 class="text-xs font-bold text-zinc-900 line-clamp-2 group-hover:text-amber-600 transition-colors">
                                    <a href="{{ route('products.show', $rel) }}" wire:navigate>{{ $rel->name }}</a>
                                </h4>
                                <p class="text-[10px] text-zinc-400 mt-1">SKU: {{ $rel->sku }}</p>
                            </div>

                            <div class="mt-4 border-t border-zinc-100 pt-3 flex items-center justify-between">
                                @if (auth()->user()?->canViewPrices())
                                    <span class="text-xs font-bold text-zinc-950">
                                        Rp {{ number_format($rel->latestPrice?->price_wholesale_tier1 ?? 0, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-amber-800 font-semibold">🔒 Terkunci</span>
                                @endif

                                <a href="{{ route('products.show', $rel) }}" wire:navigate class="text-xs font-bold text-amber-700 hover:underline">
                                    Lihat →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Mobile Sticky Bottom Purchase Bar --}}
    <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-zinc-200 p-4 lg:hidden shadow-2xl">
        <div class="flex items-center gap-3">
            <div class="flex items-center border border-zinc-300 rounded-xl overflow-hidden bg-white shrink-0">
                <button wire:click="decrementQuantity" class="px-3 py-2 text-zinc-600 font-bold text-xs">-</button>
                <span class="px-3 py-2 font-bold text-zinc-900 text-xs">{{ $quantity }}</span>
                <button wire:click="incrementQuantity" class="px-3 py-2 text-zinc-600 font-bold text-xs">+</button>
            </div>

            <button
                wire:click="addToCart"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400 py-3 text-xs font-bold text-brand-black shadow-xs"
            >
                <x-icon name="shopping-cart" class="size-4" />
                <span>+ Keranjang</span>
            </button>
        </div>
    </div>
</div>
