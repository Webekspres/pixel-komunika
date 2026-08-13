<div class="min-h-screen bg-surface-2 pb-24 lg:pb-12">

    @if (session()->has('success'))
        <div class="fixed right-5 bottom-5 z-50 flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="check-circle" class="size-5" />
            <span>{{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="ml-2 text-emerald-100 underline hover:text-white">Lihat Keranjang</a>
        </div>
    @endif

    @php
        $stockQty = (int) ($product->inventorySnapshot?->quantity_available ?? 0);
        $inStock = $stockQty > 0;
        $canViewPrices = auth()->user()?->canViewPrices() ?? false;
        $isPendingCustomer = auth()->check()
            && auth()->user()->isCustomer()
            && auth()->user()->customerStatus() === \App\Models\CustomerProfile::PENDING;
        $description = $product->enrichment?->description
            ?: $product->enrichment?->short_description
            ?: 'Deskripsi produk belum tersedia. Hubungi toko untuk detail spesifikasi lebih lanjut.';
    @endphp

    <div class="container-2xl py-6 sm:py-8 lg:py-10">

        <div class="mb-6">
            <x-storefront.breadcrumb :items="[
                ['label' => 'Beranda', 'href' => route('home')],
                ['label' => 'Produk', 'href' => route('products.index')],
                ['label' => $product->name, 'href' => null],
            ]" />
        </div>

        <div class="storefront-panel mb-10 overflow-hidden">
            <div class="grid gap-0 lg:grid-cols-[minmax(0,1.05fr)_minmax(22rem,0.95fr)]">
                {{-- Gallery (no product image column yet — polished placeholder) --}}
                <div class="p-6 sm:p-8">
                    <div class="relative flex aspect-square w-full items-center justify-center overflow-hidden rounded-2xl border border-brand-black/8 bg-zinc-50 group">
                        <x-icon name="package" class="relative z-10 size-28 text-zinc-300 transition-transform duration-300 group-hover:scale-105 sm:size-32" />
                        <div class="absolute top-4 left-4">
                            <span class="storefront-pill bg-white/90">{{ $product->category->name }}</span>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex flex-col justify-between space-y-6 border-t border-zinc-100 p-6 sm:p-8 lg:border-t-0 lg:border-l">
                    <div class="space-y-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                            {{ $product->brand?->name ?? 'Pixel Komunika' }} · {{ $product->category->name }}
                        </p>
                        <h1 class="text-xl font-black leading-tight tracking-tight text-zinc-950 sm:text-2xl">
                            {{ $product->name }}
                        </h1>
                        <p class="font-mono text-xs text-zinc-400">SKU: {{ $product->sku }}</p>

                        @if ($inStock)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                <x-icon name="check" class="size-3.5" />
                                Stok Tersedia ({{ $stockQty }} unit)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600">
                                <x-icon name="x" class="size-3.5" />
                                Stok Habis
                            </span>
                        @endif

                        {{-- Price box --}}
                        <div class="rounded-2xl bg-zinc-50 p-4">
                            @if ($canViewPrices)
                                <p class="text-2xl font-black text-zinc-950">
                                    Rp {{ number_format($product->listPriceAmount() ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="mt-0.5 text-xs text-zinc-500">Harga Grosir B2B / unit</p>
                                @if ($product->partaiPriceAmount())
                                    <div class="mt-2 border-t border-zinc-200 pt-2">
                                        <p class="text-xs text-zinc-500">
                                            Harga Partai:
                                            <span class="font-semibold text-zinc-700">
                                                Rp {{ number_format($product->partaiPriceAmount(), 0, ',', '.') }}
                                            </span>
                                            (min. 5 unit/SKU)
                                            @if ($product->grosirMinimumQuantity())
                                                · Grosir min. {{ $product->grosirMinimumQuantity() }} unit
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @elseif ($isPendingCustomer)
                                <div class="flex items-start gap-2.5">
                                    <x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-amber-500" />
                                    <div>
                                        <p class="text-sm font-semibold text-amber-700">Akun sedang diverifikasi</p>
                                        <p class="text-xs text-zinc-500">Harga akan terlihat setelah akun disetujui.</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2.5">
                                    <x-icon name="lock" class="size-4 shrink-0 text-zinc-400" />
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-zinc-700">Harga tersembunyi</p>
                                        <p class="text-xs text-zinc-400">Login untuk melihat harga grosir</p>
                                    </div>
                                    <a href="{{ route('login') }}" class="shrink-0 rounded-lg bg-brand-black px-3 py-1.5 text-xs font-bold text-white">
                                        Login
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Qty + Add to cart --}}
                        @if ($inStock)
                            <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                                <div class="flex items-center overflow-hidden rounded-xl border-2 border-zinc-200 bg-white">
                                    <button
                                        wire:click="decrementQuantity"
                                        type="button"
                                        class="flex size-10 items-center justify-center text-zinc-600 transition hover:bg-zinc-50"
                                        aria-label="Kurangi jumlah"
                                    >
                                        <x-icon name="minus" class="size-4" />
                                    </button>
                                    <span class="w-10 text-center text-sm font-bold text-zinc-900">{{ $quantity }}</span>
                                    <button
                                        wire:click="incrementQuantity"
                                        type="button"
                                        class="flex size-10 items-center justify-center text-zinc-600 transition hover:bg-zinc-50"
                                        aria-label="Tambah jumlah"
                                    >
                                        <x-icon name="plus" class="size-4" />
                                    </button>
                                </div>

                                <button
                                    wire:click="addToCart"
                                    type="button"
                                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-yellow px-5 py-2.5 font-bold text-brand-black transition hover:bg-brand-yellow-dark active:scale-[0.99]"
                                >
                                    <x-icon name="shopping-cart" class="size-5" />
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </div>
                        @endif

                        {{-- Trust strip --}}
                        <div class="grid grid-cols-3 gap-3 pt-1">
                            <div class="flex flex-col items-center gap-1.5 text-center">
                                <div class="flex size-9 items-center justify-center rounded-xl bg-zinc-50">
                                    <x-icon name="shield" class="size-4 text-zinc-500" />
                                </div>
                                <span class="text-xs font-medium leading-tight text-zinc-500">Produk Original</span>
                            </div>
                            <div class="flex flex-col items-center gap-1.5 text-center">
                                <div class="flex size-9 items-center justify-center rounded-xl bg-zinc-50">
                                    <x-icon name="truck" class="size-4 text-zinc-500" />
                                </div>
                                <span class="text-xs font-medium leading-tight text-zinc-500">Kurir Toko H+1</span>
                            </div>
                            <div class="flex flex-col items-center gap-1.5 text-center">
                                <div class="flex size-9 items-center justify-center rounded-xl bg-zinc-50">
                                    <x-icon name="package" class="size-4 text-zinc-500" />
                                </div>
                                <span class="text-xs font-medium leading-tight text-zinc-500">Dikemas Aman</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs: Deskripsi | Spesifikasi --}}
            <div
                class="border-t border-zinc-100 p-6 sm:p-8"
                x-data="{ tab: 'desc' }"
            >
                <div class="mb-5 flex gap-1">
                    <button
                        type="button"
                        @click="tab = 'desc'"
                        :class="tab === 'desc' ? 'bg-brand-yellow text-brand-black' : 'text-zinc-600 hover:bg-zinc-100'"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition-colors"
                    >
                        Deskripsi
                    </button>
                    <button
                        type="button"
                        @click="tab = 'specs'"
                        :class="tab === 'specs' ? 'bg-brand-yellow text-brand-black' : 'text-zinc-600 hover:bg-zinc-100'"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition-colors"
                    >
                        Spesifikasi
                    </button>
                </div>

                <div x-show="tab === 'desc'" x-cloak>
                    <p class="text-sm leading-relaxed text-zinc-700 whitespace-pre-line">{{ $description }}</p>
                </div>

                <div x-show="tab === 'specs'" x-cloak class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 px-4 py-2.5">
                        <span class="w-28 shrink-0 text-xs font-semibold text-zinc-500">Berat</span>
                        <span class="text-sm font-medium text-zinc-800">{{ $product->weight_grams }} gram</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 px-4 py-2.5">
                        <span class="w-28 shrink-0 text-xs font-semibold text-zinc-500">Dimensi</span>
                        <span class="text-sm font-medium text-zinc-800">
                            {{ $product->length_cm ?? '-' }} × {{ $product->width_cm ?? '-' }} × {{ $product->height_cm ?? '-' }} cm
                        </span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 px-4 py-2.5">
                        <span class="w-28 shrink-0 text-xs font-semibold text-zinc-500">Merek</span>
                        <span class="text-sm font-medium text-zinc-800">{{ $product->brand?->name ?? 'Pixel Komunika' }}</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 px-4 py-2.5">
                        <span class="w-28 shrink-0 text-xs font-semibold text-zinc-500">Kategori</span>
                        <span class="text-sm font-medium text-zinc-800">{{ $product->category->name }}</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 px-4 py-2.5">
                        <span class="w-28 shrink-0 text-xs font-semibold text-zinc-500">SKU</span>
                        <span class="font-mono text-sm font-medium text-zinc-800">{{ $product->sku }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <div class="space-y-6">
                <h2 class="text-xl font-black text-zinc-900">Produk Serupa</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
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

    @if ($inStock)
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-zinc-200 bg-white/95 p-4 shadow-2xl backdrop-blur-md lg:hidden">
            <div class="flex items-center gap-3">
                <div class="flex shrink-0 items-center overflow-hidden rounded-xl border border-zinc-300 bg-white">
                    <button wire:click="decrementQuantity" type="button" class="px-3 py-2 text-xs font-bold text-zinc-600" aria-label="Kurangi jumlah">-</button>
                    <span class="px-3 py-2 text-xs font-bold text-zinc-900">{{ $quantity }}</span>
                    <button wire:click="incrementQuantity" type="button" class="px-3 py-2 text-xs font-bold text-zinc-600" aria-label="Tambah jumlah">+</button>
                </div>

                <button
                    wire:click="addToCart"
                    type="button"
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-yellow py-3 text-xs font-bold text-brand-black shadow-xs"
                >
                    <x-icon name="shopping-cart" class="size-4" />
                    <span>Tambah ke Keranjang</span>
                </button>
            </div>
        </div>
    @endif
</div>
