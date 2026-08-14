@php
    $stockBadges = [
        \App\Models\InventorySnapshot::AVAILABLE => 'bg-emerald-100 text-emerald-800',
        \App\Models\InventorySnapshot::LOW => 'bg-amber-100 text-amber-800',
        \App\Models\InventorySnapshot::OUT => 'bg-red-100 text-red-700',
    ];
@endphp

<x-layouts.app :title="'Produk - Pixel Komunika'">
    <div class="space-y-5 p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-black text-zinc-900">Produk</h1>
                <p class="mt-0.5 text-sm text-zinc-500">Kelola presentasi katalog di website. Data SKU, harga, dan stok dikelola oleh POS.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-neutral-100 bg-white">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col gap-3 border-b border-neutral-100 px-4 py-4 sm:flex-row sm:items-center">
                <div class="relative w-full sm:max-w-sm">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama produk / SKU..."
                        class="w-full rounded-xl border border-neutral-200 py-2.5 pr-3 pl-10 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                    >
                </div>
                <select
                    name="category"
                    class="rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm text-zinc-700 focus:border-brand-yellow focus:outline-none"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($selectedCategory === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-zinc-700">
                    Terapkan
                </button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100">
                            <th class="w-12 px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">No</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Produk</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">SKU</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Kategori</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Brand</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Harga Partai</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Stok</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse ($products as $product)
                            @php
                                $stockStatus = $product->inventorySnapshot?->stock_status;
                                $stockQty = $product->inventorySnapshot?->quantity_available ?? 0;
                                $isVisible = (bool) ($product->enrichment?->is_visible ?? true);
                                $primaryMedia = $product->media->firstWhere('is_primary', true) ?? $product->media->first();
                            @endphp
                            <tr class="transition-colors hover:bg-neutral-50">
                                <td class="w-12 px-5 py-3.5 text-center text-xs text-zinc-400">{{ $products->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 shrink-0 overflow-hidden rounded-xl border border-neutral-100 bg-neutral-50">
                                            @if ($primaryMedia)
                                                <img
                                                    src="{{ $primaryMedia->url() }}"
                                                    alt="{{ $product->name }}"
                                                    class="size-full object-cover"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="flex size-full items-center justify-center text-zinc-300">
                                                    <x-icon name="image" class="size-4" />
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-zinc-900">{{ $product->enrichment?->display_name ?: $product->name }}</p>
                                            <p class="truncate text-xs text-zinc-400">{{ $product->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-zinc-600">{{ $product->sku }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $product->category?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $product->brand?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 font-semibold text-zinc-800">
                                    @if ($product->partaiPriceAmount() !== null)
                                        Rp {{ number_format($product->partaiPriceAmount(), 0, ',', '.') }}
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($stockStatus)
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $stockBadges[$stockStatus] ?? 'bg-zinc-100 text-zinc-700' }}">
                                            {{ $stockStatus }} · {{ $stockQty }}
                                        </span>
                                    @else
                                        <span class="text-xs text-zinc-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if ($isVisible)
                                        <span class="inline-flex items-center gap-1 text-emerald-600" title="Tampil di storefront">
                                            <x-icon name="eye" class="size-4" />
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-zinc-300" title="Disembunyikan dari storefront">
                                            <x-icon name="eye-off" class="size-4" />
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        wire:navigate
                                        class="inline-flex items-center gap-1 whitespace-nowrap text-xs font-semibold text-zinc-500 transition hover:text-brand-black"
                                    >
                                        Kelola presentasi
                                        <x-icon name="arrow-right" class="size-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-sm text-zinc-400">
                                    Tidak ada produk yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="border-t border-neutral-100 px-5 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
