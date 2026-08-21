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
                            <p class="mt-1 text-sm text-zinc-500">Opsi dihitung dari kecamatan, kota, dan berat belanja.</p>
                            <div class="mt-5 space-y-6" wire:loading.class="opacity-60 pointer-events-none">
                                @if (! $selectedAddress)
                                    <p class="text-sm text-zinc-500">Pilih alamat untuk melihat opsi ekspedisi.</p>
                                @else
                                    @php
                                        $hasStoreCourier = ! empty($storeRates);
                                        $hasBiteshipRates = ! empty($biteshipRates);
                                        $usingBiteshipDriver = config('store.shipping.driver') === 'biteship';
                                        $showFallbackNotice = $usingBiteshipDriver && ! $hasBiteshipRates && $hasStoreCourier;
                                    @endphp

                                    {{-- Loading skeleton saat ganti alamat --}}
                                    <div wire:loading.flex class="items-center gap-2 text-sm text-zinc-500">
                                        <svg class="size-4 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                        <span>Memuat tarif ekspedisi...</span>
                                    </div>

                                    @if ($showFallbackNotice)
                                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 flex items-start gap-3">
                                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                            <div>
                                                <p class="font-semibold">Ongkir otomatis tidak tersedia</p>
                                                <p class="mt-1">Tidak dapat menghitung ongkir dari Biteship. Silakan pilih <strong>Kurir Toko</strong> di bawah, atau hubungi admin via WhatsApp <a href="https://wa.me/6281546407702" target="_blank" class="underline font-bold">0815-4640-7702</a>.</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Seksi A: Pengiriman Toko (Internal) --}}
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex size-7 items-center justify-center rounded-lg bg-brand-yellow text-brand-black">
                                                <x-icon name="truck" class="size-4" />
                                            </div>
                                            <h3 class="text-sm font-bold text-zinc-900">🛵 Pengiriman Toko (Internal Pixel Komunika)</h3>
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold tracking-wide text-emerald-700 uppercase">Rekomendasi Hemat / Internal Fleet</span>
                                        </div>
                                        <p class="mt-1 text-xs text-zinc-500">Armada toko — H+1 kerja, hanya untuk area terjangkau.</p>

                                        <div class="mt-3">
                                            @if (empty($storeRates))
                                                <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600 flex items-start gap-2.5">
                                                    <x-icon name="info" class="size-4 shrink-0 text-zinc-400 mt-0.5" />
                                                    <span>Kurir Toko tidak menjangkau area <strong>{{ $selectedAddress->district_name }}, {{ $selectedAddress->city_name }}</strong>, silakan pilih Ekspedisi Luar di bawah.</span>
                                                </div>
                                            @else
                                                <div class="grid gap-3">
                                                    @foreach ($storeRates as $rate)
                                                        @php $key = $rate['code'] . ':' . $rate['service']; @endphp
                                                        @php $isActive = $selectedCourierKey === $key; @endphp
                                                        <label class="flex cursor-pointer items-center justify-between gap-3 w-full border-2 rounded-2xl p-4 transition-all {{ $isActive ? 'border-amber-400 bg-amber-50/40 shadow-sm' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                                                            <div class="flex items-center gap-3 min-w-0">
                                                                <input type="radio" name="selectedCourierKey" value="{{ $key }}" wire:model.live="selectedCourierKey" class="accent-amber-500" />
                                                                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl {{ $isActive ? 'bg-amber-400 text-zinc-900' : 'bg-zinc-100 text-zinc-600' }}">
                                                                    <x-icon name="bike" class="size-4.5" />
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="text-sm font-bold text-zinc-900">Kurir Toko (Area {{ $selectedAddress->district_name }})</p>
                                                                    <p class="text-xs text-zinc-500">Estimasi H+1 Kerja — Rute harian armada toko • {{ $rate['etd'] }}</p>
                                                                </div>
                                                            </div>
                                                            <span class="shrink-0 text-base font-bold text-zinc-900">Rp {{ number_format($rate['cost'], 0, ',', '.') }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Seksi B: Ekspedisi Nasional & Instan --}}
                                    <div>
                                        <h3 class="text-sm font-bold text-zinc-900">🚚 Ekspedisi Lainnya (Reguler &amp; Instan)</h3>
                                        <p class="mt-1 text-xs text-zinc-500">Opsi nasional & instan via Biteship — tarif real-time.</p>

                                        <div wire:loading class="mt-3 grid gap-3 sm:grid-cols-2">
                                            @for ($i = 0; $i < 3; $i++)
                                                <div class="animate-pulse rounded-2xl border border-zinc-100 bg-zinc-50 p-4">
                                                    <div class="h-4 w-24 rounded bg-zinc-200"></div>
                                                    <div class="mt-2 h-3 w-32 rounded bg-zinc-200"></div>
                                                    <div class="mt-3 h-4 w-20 rounded bg-zinc-200"></div>
                                                </div>
                                            @endfor
                                        </div>

                                        <div wire:loading.remove>
                                            @if (empty($biteshipRates))
                                                <p class="mt-3 text-sm text-zinc-500">Tidak ada ekspedisi luar untuk alamat ini.</p>
                                            @else
                                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                    @foreach ($biteshipRates as $rate)
                                                        @php $key = $rate['code'] . ':' . $rate['service']; @endphp
                                                        @php $isActive = $selectedCourierKey === $key; @endphp
                                                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition {{ $isActive ? 'border-zinc-900 bg-zinc-900 text-white shadow-sm' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                                                            <input type="radio" name="selectedCourierKey" value="{{ $key }}" wire:model.live="selectedCourierKey" class="mt-1 accent-zinc-900" />
                                                            <div class="min-w-0 flex-1">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="inline-flex rounded-md bg-white px-1.5 py-0.5 text-[10px] font-black tracking-wide text-zinc-700 border border-zinc-200">{{ strtoupper(explode(' ', $rate['name'])[0]) }}</span>
                                                                    <p class="text-sm font-bold {{ $isActive ? 'text-white' : 'text-zinc-900' }} truncate">{{ $rate['name'] }}</p>
                                                                </div>
                                                                <p class="text-xs {{ $isActive ? 'text-zinc-300' : 'text-zinc-500' }}">Estimasi {{ $rate['etd'] }}</p>
                                                                <p class="mt-1 text-sm font-bold {{ $isActive ? 'text-white' : 'text-zinc-900' }}">Rp {{ number_format($rate['cost'], 0, ',', '.') }}</p>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
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
                            <div class="mt-4 space-y-3 text-sm" wire:loading.class="opacity-60">
                                <div class="flex justify-between text-zinc-600">
                                    <span>Subtotal</span>
                                    <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-zinc-600">
                                    <span>PPh 22</span>
                                    <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['pph22'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-zinc-600">
                                    <span class="flex items-center gap-1.5">Ongkir <span wire:loading wire:target="selectedAddressId,selectedCourierKey" class="inline-flex size-3 animate-spin rounded-full border-2 border-zinc-300 border-t-zinc-900"></span></span>
                                    <span class="font-semibold text-zinc-900 transition-all">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between border-t border-zinc-100 pt-3 text-base font-bold text-zinc-900">
                                    <span>Total Tagihan</span>
                                    <span class="text-brand-yellow-dark text-lg transition-all">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <div class="rounded-xl bg-brand-yellow-muted/50 px-4 py-3 text-xs leading-relaxed text-brand-black/65">
                                    Pesanan dibuat setelah alamat dan ekspedisi dipilih. Perhitungan akhir divalidasi di server.
                                </div>
                                @php
                                    $canOrder = $selectedAddress && $selectedCourierKey && isset($shippingCost) && ! $summary['items']->isEmpty();
                                    $hasRates = ! empty($shippingRates);
                                @endphp
                                <button
                                    type="submit"
                                    class="w-full rounded-xl py-4 text-center text-base font-bold transition disabled:cursor-not-allowed disabled:opacity-50 {{ $canOrder && $hasRates ? 'bg-brand-black text-white hover:bg-zinc-700' : 'bg-zinc-200 text-zinc-500' }}"
                                    @disabled(! $canOrder || ! $hasRates)
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove wire:target="selectedAddressId,selectedCourierKey">Buat Pesanan Sekarang</span>
                                    <span wire:loading wire:target="selectedAddressId,selectedCourierKey" class="inline-flex items-center gap-2"><svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memuat...</span>
                                </button>
                                @if (! $canOrder)
                                    <p class="text-center text-xs text-zinc-400">Pilih alamat dan kurir untuk melanjutkan.</p>
                                @endif
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        @endif
    </div>
</div>
