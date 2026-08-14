<div class="min-h-screen bg-surface-2 py-6 sm:py-8">
    <div class="container-2xl">
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-zinc-500 transition hover:text-zinc-700">
                <x-icon name="arrow-left" class="size-4" />
                Lanjut Belanja
            </a>
            <h1 class="text-xl font-black text-zinc-900 sm:text-2xl">
                Keranjang Belanja
                @if ($summary['items']->isNotEmpty())
                    <span class="ml-2 text-sm font-semibold text-zinc-400">({{ $summary['total_items'] }} item)</span>
                @endif
            </h1>
            @if ($summary['items']->isNotEmpty())
                <button
                    type="button"
                    wire:click="clearCart"
                    wire:confirm="Kosongkan seluruh isi keranjang?"
                    class="ml-auto inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                >
                    <x-icon name="trash-2" class="size-3.5" />
                    Hapus Semua
                </button>
            @endif
        </div>

        @if (session()->has('success'))
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($summary['items']->isEmpty())
            <div class="rounded-3xl border border-zinc-100 bg-white">
                <x-ui.empty-state
                    title="Keranjang kosong"
                    description="Belum ada produk di keranjangmu. Yuk belanja!"
                    icon="shopping-cart"
                    mascot
                    :action-href="route('products.index')"
                    action-label="Mulai Belanja"
                />
            </div>
        @else
            <div class="flex flex-col gap-5 lg:flex-row">
                <div class="flex-1 space-y-3">
                    @foreach ($summary['items'] as $item)
                        <div class="flex gap-4 rounded-2xl border border-zinc-100 bg-white p-4">
                            <div class="flex size-20 shrink-0 items-center justify-center rounded-xl bg-brand-yellow-muted sm:size-24">
                                <x-icon name="package" class="size-8 text-brand-black/40" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-zinc-400">SKU: {{ $item['product']->sku }}</p>
                                <h3 class="text-sm font-semibold leading-snug text-zinc-900 sm:text-base">{{ $item['product']->displayName() }}</h3>
                                <p class="mt-0.5 text-xs font-semibold text-zinc-500">
                                    Rp {{ number_format($item['unit_price'], 0, ',', '.') }} / pcs · {{ $item['price_type'] }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center overflow-hidden rounded-xl border-2 border-zinc-200">
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="flex size-8 items-center justify-center hover:bg-zinc-50" aria-label="Kurangi">
                                            <x-icon name="minus" class="size-3.5" />
                                        </button>
                                        <span class="w-10 text-center text-sm font-bold">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="flex size-8 items-center justify-center hover:bg-zinc-50" aria-label="Tambah">
                                            <x-icon name="plus" class="size-3.5" />
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <p class="font-bold text-zinc-900">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</p>
                                        <button
                                            wire:click="removeItem({{ $item['id'] }})"
                                            class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-red-50 hover:text-red-500"
                                            aria-label="Hapus"
                                        >
                                            <x-icon name="trash-2" class="size-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="w-full shrink-0 lg:w-80 xl:w-96">
                    <div class="sticky top-28 space-y-4 rounded-2xl border border-zinc-100 bg-white p-5">
                        <h2 class="text-base font-black text-zinc-900">Ringkasan Belanja</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-zinc-600">
                                <span>Subtotal</span>
                                <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-zinc-600">
                                <span>Estimasi PPh 22</span>
                                <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['pph22'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-zinc-600">
                                <span>Total Berat</span>
                                <span class="font-semibold text-zinc-900">{{ number_format($summary['total_weight_grams'] / 1000, 2) }} kg</span>
                            </div>
                            <div class="flex justify-between border-t border-zinc-100 pt-3 text-base font-bold text-zinc-900">
                                <span>Estimasi Total</span>
                                <span class="text-brand-yellow-dark">Rp {{ number_format($summary['subtotal'] + $summary['pph22'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @auth
                            @if (auth()->user()->isActiveCustomer())
                                <a href="{{ route('checkout.index') }}" wire:navigate class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-black py-3.5 text-sm font-bold text-white transition hover:bg-zinc-700">
                                    Lanjut Checkout
                                    <x-icon name="arrow-right" class="size-4" />
                                </a>
                            @else
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center text-xs font-medium text-amber-800">
                                    {{ auth()->user()->customerStatus() === \App\Models\CustomerProfile::PENDING
                                        ? 'Akun masih menunggu verifikasi admin untuk checkout.'
                                        : 'Checkout hanya untuk pelanggan terverifikasi.' }}
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full rounded-xl bg-brand-yellow py-3.5 text-center text-sm font-bold text-brand-black transition hover:bg-brand-yellow-dark">
                                Login untuk Checkout
                            </a>
                        @endauth
                    </div>
                </aside>
            </div>
        @endif
    </div>
</div>
