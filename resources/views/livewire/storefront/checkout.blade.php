<div class="min-h-screen bg-surface-2 py-6 sm:py-8">
    <div class="container-lg space-y-6">
        <div>
            <x-storefront.breadcrumb :items="[
                ['label' => 'Keranjang Belanja', 'href' => route('cart.index')],
                ['label' => 'Checkout', 'href' => null],
            ]" />
            <h1 class="mt-4 text-2xl font-black text-zinc-900 sm:text-3xl">Checkout</h1>
            <p class="mt-1 text-sm text-zinc-500">Pilih alamat, ekspedisi, dan tinjau rincian akhir pesanan.</p>
        </div>

        @if (session()->has('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($summary['items']->isEmpty())
            <div class="rounded-3xl border border-zinc-100 bg-white">
                <x-ui.empty-state
                    title="Keranjang kosong"
                    description="Belum ada produk untuk dicheckout."
                    icon="shopping-cart"
                    mascot
                    :action-href="route('products.index')"
                    action-label="Jelajahi Produk"
                />
            </div>
        @else
            <form wire:submit.prevent="placeOrder">
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="space-y-5 lg:col-span-2">
                        <div class="rounded-2xl border border-zinc-100 bg-white p-5 sm:p-6">
                            <h2 class="text-base font-black text-zinc-900">1. Alamat Pengiriman</h2>
                            <p class="mt-1 text-sm text-zinc-500">Pilih alamat yang dipakai untuk pengiriman.</p>
                            <div class="mt-4">
                                @if ($addresses->isEmpty())
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                        Belum ada alamat. <a href="{{ route('account.addresses.index') }}" class="font-bold underline">Tambah alamat</a> dulu.
                                    </div>
                                @else
                                    <div class="grid gap-3">
                                        @foreach ($addresses as $address)
                                            <label class="flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition {{ $selectedAddressId == $address->id ? 'border-brand-yellow bg-brand-yellow-muted/40 shadow-sm' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                                                <input type="radio" name="selectedAddressId" value="{{ $address->id }}" wire:model.live="selectedAddressId" class="mt-1 accent-brand-yellow" />
                                                <div class="flex-1 space-y-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-zinc-900">{{ $address->label ?: 'Alamat Pengiriman' }}</span>
                                                        @if ($address->is_default)
                                                            <span class="rounded-md bg-brand-yellow/30 px-2 py-0.5 text-xs font-bold text-brand-black">Default</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm font-medium text-zinc-800">{{ $address->recipient_name }} ({{ $address->recipient_phone }})</p>
                                                    <p class="text-xs text-zinc-600">
                                                        {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-2xl border border-zinc-100 bg-white p-5 sm:p-6">
                            <h2 class="text-base font-black text-zinc-900">2. Ekspedisi</h2>
                            <p class="mt-1 text-sm text-zinc-500">Opsi dihitung dari kota tujuan dan berat belanja.</p>
                            <div class="mt-4">
                                @if (empty($shippingRates))
                                    <p class="text-sm text-zinc-500">Pilih alamat untuk melihat opsi ekspedisi.</p>
                                @else
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        @foreach ($shippingRates as $rate)
                                            @php($key = $rate['code'] . ':' . $rate['service'])
                                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition {{ $selectedCourierKey == $key ? 'border-brand-yellow bg-brand-yellow-muted/40 shadow-sm' : 'border-zinc-200 hover:border-zinc-300' }}">
                                                <input type="radio" name="selectedCourierKey" value="{{ $key }}" wire:model.live="selectedCourierKey" class="mt-1 accent-brand-yellow" />
                                                <div>
                                                    <p class="font-semibold text-zinc-900">{{ $rate['name'] }}</p>
                                                    <p class="text-xs text-zinc-500">Estimasi {{ $rate['etd'] }}</p>
                                                    <p class="mt-1 text-sm font-bold text-brand-yellow-dark">Rp {{ number_format($rate['cost'], 0, ',', '.') }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-2xl border border-zinc-100 bg-white p-5 sm:p-6">
                            <h2 class="text-base font-black text-zinc-900">3. Item Pesanan</h2>
                            <div class="mt-4 divide-y divide-zinc-100">
                                @foreach ($summary['items'] as $item)
                                    <div class="flex items-center justify-between gap-4 py-3 text-sm">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-zinc-900">{{ $item['product']->displayName() }}</p>
                                            <p class="text-xs text-zinc-500">{{ $item['quantity'] }} × Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</p>
                                        </div>
                                        <span class="font-bold text-zinc-900">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-4">
                        <div class="sticky top-28 rounded-2xl border border-zinc-100 bg-white p-5 sm:p-6">
                            <h2 class="text-base font-black text-zinc-900">Ringkasan Akhir</h2>
                            <div class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between text-zinc-600">
                                    <span>Subtotal</span>
                                    <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-zinc-600">
                                    <span>PPh 22</span>
                                    <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['pph22'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-zinc-600">
                                    <span>Ongkir</span>
                                    <span class="font-semibold text-zinc-900">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between border-t border-zinc-100 pt-3 text-base font-bold text-zinc-900">
                                    <span>Total</span>
                                    <span class="text-brand-yellow-dark text-lg">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <div class="rounded-xl bg-brand-yellow-muted/50 px-4 py-3 text-xs leading-relaxed text-brand-black/65">
                                    Pesanan dibuat setelah alamat dan ekspedisi dipilih. Perhitungan akhir divalidasi di server.
                                </div>
                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-brand-black py-4 text-center text-base font-bold text-white transition hover:bg-zinc-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    @if ($addresses->isEmpty()) disabled @endif
                                >
                                    Buat Pesanan Sekarang
                                </button>
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        @endif
    </div>
</div>
