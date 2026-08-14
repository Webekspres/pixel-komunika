<x-layouts.app :title="'Kelola Produk - '.$product->name">
    <div class="space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <a href="{{ route('admin.products.index') }}" wire:navigate class="mb-2 inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-700">
                    <x-icon name="arrow-left" class="size-4" />
                    Kembali ke daftar
                </a>
                <h1 class="text-xl font-black text-zinc-900">{{ $product->name }}</h1>
                <p class="mt-0.5 text-sm text-zinc-500">Enrichment presentasi untuk storefront. Data POS hanya dibaca.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-zinc-900">Data POS (read-only)</h2>
                        <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-[10px] font-bold tracking-wide text-zinc-500 uppercase">Sumber: POS</span>
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium text-zinc-500">SKU</dt>
                            <dd class="mt-1 font-mono text-sm font-semibold text-zinc-900">{{ $product->sku }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-zinc-500">Kategori</dt>
                            <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->category?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-zinc-500">Brand</dt>
                            <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->brand?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-zinc-500">Stok tersedia</dt>
                            <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->inventorySnapshot?->quantity_available ?? 0 }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 border-t border-neutral-100 pt-4">
                        <p class="mb-3 text-xs font-semibold text-zinc-500">Harga per tier</p>
                        <div class="space-y-2">
                            @foreach ($product->prices as $price)
                                <div class="flex items-center justify-between rounded-xl bg-neutral-50 px-4 py-2.5 text-sm">
                                    <span class="text-xs font-bold tracking-wide text-zinc-500 uppercase">{{ $price->price_type }}</span>
                                    <span class="font-semibold text-zinc-900">
                                        Rp {{ number_format($price->amount, 0, ',', '.') }}
                                        @if ($price->minimum_quantity)
                                            <span class="text-xs text-zinc-400">· min {{ $price->minimum_quantity }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6">
                    <h2 class="mb-4 text-sm font-bold text-zinc-900">Presentasi storefront</h2>

                    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="grid gap-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="display_name" class="mb-1.5 block text-xs font-semibold text-zinc-700">Nama tampilan</label>
                            <input
                                id="display_name"
                                name="display_name"
                                type="text"
                                value="{{ old('display_name', $product->enrichment?->display_name) }}"
                                placeholder="{{ $product->name }}"
                                class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                            >
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="slug" class="mb-1.5 block text-xs font-semibold text-zinc-700">Slug URL</label>
                                <input
                                    id="slug"
                                    name="slug"
                                    type="text"
                                    value="{{ old('slug', $product->enrichment?->slug) }}"
                                    placeholder="otomatis dari nama"
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                                >
                            </div>
                            <div>
                                <label for="label" class="mb-1.5 block text-xs font-semibold text-zinc-700">Label</label>
                                <input
                                    id="label"
                                    name="label"
                                    type="text"
                                    value="{{ old('label', $product->enrichment?->label) }}"
                                    placeholder="mis. Best Seller"
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="short_description" class="mb-1.5 block text-xs font-semibold text-zinc-700">Deskripsi singkat</label>
                            <textarea
                                id="short_description"
                                name="short_description"
                                rows="2"
                                maxlength="500"
                                class="w-full rounded-xl border border-neutral-200 p-3 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                            >{{ old('short_description', $product->enrichment?->short_description) }}</textarea>
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-xs font-semibold text-zinc-700">Deskripsi lengkap</label>
                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="w-full rounded-xl border border-neutral-200 p-3 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                            >{{ old('description', $product->enrichment?->description) }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="seo_title" class="mb-1.5 block text-xs font-semibold text-zinc-700">SEO title</label>
                                <input
                                    id="seo_title"
                                    name="seo_title"
                                    type="text"
                                    maxlength="191"
                                    value="{{ old('seo_title', $product->enrichment?->seo_title) }}"
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                                >
                            </div>
                            <div>
                                <label for="display_order" class="mb-1.5 block text-xs font-semibold text-zinc-700">Urutan tampil</label>
                                <input
                                    id="display_order"
                                    name="display_order"
                                    type="number"
                                    min="0"
                                    value="{{ old('display_order', $product->enrichment?->display_order ?? 0) }}"
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="seo_description" class="mb-1.5 block text-xs font-semibold text-zinc-700">SEO description</label>
                            <textarea
                                id="seo_description"
                                name="seo_description"
                                rows="2"
                                maxlength="320"
                                class="w-full rounded-xl border border-neutral-200 p-3 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                            >{{ old('seo_description', $product->enrichment?->seo_description) }}</textarea>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-neutral-100 pt-4">
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-700">
                                <input
                                    type="checkbox"
                                    name="is_visible"
                                    value="1"
                                    class="rounded border-zinc-300"
                                    @checked(old('is_visible', $product->enrichment?->is_visible ?? true))
                                >
                                <x-icon name="{{ ($product->enrichment?->is_visible ?? true) ? 'eye' : 'eye-off' }}" class="size-4 text-zinc-400" />
                                Tampilkan di storefront
                            </label>

                            <button type="submit" class="rounded-xl bg-brand-yellow px-5 py-2.5 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark">
                                Simpan Presentasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <livewire:admin.product-media-manager :product="$product" wire:key="media-manager-{{ $product->id }}" />

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <div class="flex items-start gap-2.5">
                        <x-icon name="info" class="mt-0.5 size-4 shrink-0 text-amber-600" />
                        <div class="text-xs leading-relaxed text-amber-900">
                            <p class="font-bold">Data katalog sumber POS</p>
                            <p class="mt-1">SKU, kategori, merek, harga, dan stok dikelola di POS (FR-CAT-003). Halaman ini hanya mengelola presentasi tampilan website (FR-CAT-004/006).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
