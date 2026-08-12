<div class="min-h-screen bg-zinc-50/70 pb-24 lg:pb-12">

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
    <div class="container-2xl py-6 sm:py-8 lg:py-10">

        {{-- Breadcrumb --}}
        <div class="mb-8">
            <x-storefront.breadcrumb :items="[
                ['label' => 'Katalog Produk', 'href' => route('products.index')],
                ['label' => $product->category->name, 'href' => route('products.index', ['kategori' => $product->category_id])],
                ['label' => $product->name, 'href' => null],
            ]" />
        </div>

        {{-- Product Details Section --}}
        <div class="storefront-panel mb-12 grid gap-8 overflow-hidden p-6 sm:p-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(22rem,0.95fr)] lg:p-10">

            {{-- Product Gallery / Preview --}}
            <div class="space-y-4">
                <div class="relative flex aspect-square w-full items-center justify-center overflow-hidden rounded-[2rem] border border-brand-black/8 bg-linear-to-br from-brand-yellow-muted via-white to-zinc-50 p-8 group">
                    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 1px 1px, #181818 1px, transparent 0); background-size: 28px 28px;" aria-hidden="true"></div>
                    <x-icon name="package" class="relative z-10 size-32 text-zinc-300 transition-transform duration-300 group-hover:scale-105" />
                    
                    {{-- Badges on preview --}}
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        <span class="storefront-pill bg-white/82">
                            {{ $product->category->name }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Product Details Information --}}
            <div class="flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    {{-- Brand & Stock badge --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="storefront-pill">
                            <x-icon name="badge-percent" class="size-3.5" />
                            <span>Merek: <strong class="text-brand-black">{{ $product->brand?->name ?? 'Pixel Komunika' }}</strong></span>
                        </span>

                        @if ($product->inventorySnapshot?->quantity_available > 0)
                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                Stok Ready: {{ $product->inventorySnapshot->quantity_available }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-bold text-red-600">
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
                    <div class="rounded-[1.75rem] border border-brand-black/8 bg-brand-yellow-muted/45 p-5 space-y-2">
                        <span class="text-xs text-zinc-500 font-medium block">Harga Grosir B2B (Tier 1):</span>
                        
                        @if (auth()->user()?->canViewPrices())
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-extrabold text-zinc-950">
                                    Rp {{ number_format($product->listPriceAmount() ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-zinc-400">/ unit (grosir)</span>
                            </div>

                            @if ($product->partaiPriceAmount())
                                <p class="text-xs text-amber-700 font-semibold pt-1">
                                    💡 Partai: Rp {{ number_format($product->partaiPriceAmount(), 0, ',', '.') }} (min. 5 unit/SKU)
                                    @if ($product->grosirMinimumQuantity())
                                        · Grosir: min. {{ $product->grosirMinimumQuantity() }} unit/SKU
                                    @endif
                                </p>
                            @endif
                        @else
                            <div class="flex items-center justify-between rounded-2xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                                <span>🔒 Harga grosir hanya dapat dilihat oleh pelanggan terverifikasi.</span>
                                <a href="{{ route('login') }}" class="font-bold underline ml-2 shrink-0">Masuk Akun</a>
                            </div>
                        @endif
                    </div>

                    {{-- Specification List --}}
                    <div class="space-y-2 pt-2">
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Spesifikasi Fisik</h4>
                        <div class="grid grid-cols-2 gap-2 rounded-[1.5rem] border border-brand-black/8 bg-white p-4 text-xs text-zinc-600">
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
                <div class="space-y-4 border-t border-zinc-200 pt-6">
                    <div class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-center">
                        
                        {{-- Quantity Selector --}}
                        <div class="flex items-center justify-between overflow-hidden rounded-2xl border border-brand-black/10 bg-white px-2 py-1">
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
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-brand-black px-6 py-4 font-bold text-brand-white transition-all hover:bg-brand-black/88 active:scale-[0.99]"
                        >
                            <x-icon name="shopping-cart" class="size-5" />
                            <span>+ Tambahkan ke Keranjang</span>
                        </button>
                    </div>
                    <p class="text-xs leading-relaxed text-brand-black/52">
                        Harga, stok, dan checkout tetap mengikuti data operasional toko. Tidak ada simulasi harga di sisi klien.
                    </p>
                </div>
            </div>
        </div>

        {{-- Related Products Section --}}
        @if ($relatedProducts->isNotEmpty())
            <div class="space-y-6">
                <x-storefront.section-header
                    eyebrow="Pilihan lain"
                    :title="'Produk terkait di '.$product->category->name"
                    description="Lanjutkan pencarian produk serupa tanpa keluar dari alur belanja."
                />
                
                <div class="grid gap-5 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $rel)
                        <x-storefront.product-card
                            :title="$rel->name"
                            :href="route('products.show', $rel)"
                            :category="$rel->category->name"
                            :sku="$rel->sku"
                            :stock-label="$rel->inventorySnapshot?->quantity_available > 0 ? 'Stok: '.$rel->inventorySnapshot->quantity_available : 'Habis'"
                            :stock-variant="$rel->inventorySnapshot?->quantity_available > 0 ? 'available' : 'unavailable'"
                            :show-price="auth()->user()?->canViewPrices()"
                            :price="'Rp '.number_format($rel->listPriceAmount() ?? 0, 0, ',', '.')"
                        />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Mobile Sticky Bottom Purchase Bar --}}
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-zinc-200 bg-white/95 p-4 shadow-2xl backdrop-blur-md lg:hidden">
        <div class="flex items-center gap-3">
            <div class="flex items-center border border-zinc-300 rounded-xl overflow-hidden bg-white shrink-0">
                <button wire:click="decrementQuantity" class="px-3 py-2 text-zinc-600 font-bold text-xs">-</button>
                <span class="px-3 py-2 font-bold text-zinc-900 text-xs">{{ $quantity }}</span>
                <button wire:click="incrementQuantity" class="px-3 py-2 text-zinc-600 font-bold text-xs">+</button>
            </div>

            <button
                wire:click="addToCart"
                type="button"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-black py-3 text-xs font-bold text-brand-white shadow-xs"
            >
                <x-icon name="shopping-cart" class="size-4" />
                <span>+ Keranjang</span>
            </button>
        </div>
    </div>
</div>
