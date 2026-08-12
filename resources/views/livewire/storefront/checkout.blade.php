<x-layout.app-page>
    <x-storefront.breadcrumb :items="[
        ['label' => 'Keranjang Belanja', 'href' => route('cart.index')],
        ['label' => 'Checkout', 'href' => null],
    ]" />

    <x-ui.page-header
        eyebrow="E-Commerce"
        title="Checkout"
        description="Pilih alamat pengiriman, ekspedisi kurir, dan tinjau rincian akhir pesanan Anda."
    />

    @if (session()->has('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800 mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if ($summary['items']->isEmpty())
        <x-ui.empty-state
            title="Keranjang Belanja Kosong"
            description="Anda belum memiliki produk di keranjang belanja untuk dicheckout."
            icon="shopping-cart"
            mascot
        />
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-brand-black px-6 py-3 font-semibold text-white transition-all hover:bg-brand-black/88">
                Jelajahi Produk
            </a>
        </div>
    @else
    <form wire:submit.prevent="placeOrder">
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <!-- 1. Alamat Pengiriman -->
                <x-ui.section-card title="1. Alamat Pengiriman" description="Pilih alamat yang dipakai untuk pengiriman pesanan ini.">
                    @if ($addresses->isEmpty())
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            Anda belum memiliki alamat tersimpan. Silakan <a href="{{ route('account.dashboard') }}" class="font-bold underline">tambah alamat di halaman Akun</a> terlebih dahulu.
                        </div>
                    @else
                        <div class="grid gap-4">
                            @foreach ($addresses as $address)
                                <label class="flex cursor-pointer items-start gap-4 rounded-[1.6rem] border p-4 transition-all {{ $selectedAddressId == $address->id ? 'border-[#F8B818] bg-amber-50/50 shadow-sm' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                                    <input
                                        type="radio"
                                        name="selectedAddressId"
                                        value="{{ $address->id }}"
                                        wire:model.live="selectedAddressId"
                                        class="mt-1 accent-[#F8B818]"
                                    />
                                    <div class="flex-1 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-zinc-900">{{ $address->label ?: 'Alamat Pengiriman' }}</span>
                                            @if ($address->is_default)
                                                <span class="px-2 py-0.5 text-xs font-bold bg-amber-200 text-amber-900 rounded-md">Default</span>
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
                </x-ui.section-card>

                <!-- 2. Ekspedisi Pengiriman -->
                <x-ui.section-card title="2. Pilihan Ekspedisi Pengiriman" description="Opsi ekspedisi dihitung dari kota tujuan dan total berat belanja Anda.">
                    @if (empty($shippingRates))
                        <p class="text-sm text-zinc-500">Pilih alamat pengiriman untuk melihat opsi ekspedisi.</p>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($shippingRates as $rate)
                                @php($key = $rate['code'] . ':' . $rate['service'])
                                <label class="flex cursor-pointer items-start gap-3 rounded-[1.6rem] border p-4 transition-all {{ $selectedCourierKey == $key ? 'border-[#F8B818] bg-amber-50/50 shadow-sm' : 'border-zinc-200 bg-white hover:border-zinc-300' }}">
                                    <input
                                        type="radio"
                                        name="selectedCourierKey"
                                        value="{{ $key }}"
                                        wire:model.live="selectedCourierKey"
                                        class="mt-1 accent-[#F8B818]"
                                    />
                                    <div>
                                        <p class="font-semibold text-zinc-900">{{ $rate['name'] }}</p>
                                        <p class="text-xs text-zinc-500">Estimasi {{ $rate['etd'] }}</p>
                                        <p class="mt-1 text-sm font-bold text-amber-800">Rp {{ number_format($rate['cost'], 0, ',', '.') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </x-ui.section-card>

                <!-- 3. Rincian Item -->
                <x-ui.section-card title="3. Item Pesanan" description="Pastikan item, kuantitas, dan nilai line item sudah sesuai sebelum order dibuat.">
                    <div class="divide-y divide-zinc-200">
                        @foreach ($summary['items'] as $item)
                            <div class="flex items-center justify-between gap-4 py-4 text-sm">
                                <div class="min-w-0">
                                    <p class="font-semibold text-zinc-900">{{ $item['product']->name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $item['quantity'] }} x Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</p>
                                </div>
                                <span class="font-bold text-zinc-900">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-ui.section-card>
            </div>

            <!-- Ringkasan Tagihan & Tombol Order -->
            <div class="space-y-4">
                <x-ui.section-card title="Ringkasan Akhir" description="Nilai total akan diperbarui mengikuti kurir yang Anda pilih.">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-zinc-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between text-zinc-600">
                            <span>Estimasi PPh 22</span>
                            <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['pph22'], 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between text-zinc-600">
                            <span>Ongkos Kirim</span>
                            <span class="font-semibold text-zinc-900">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between border-t border-zinc-200 pt-3 text-base font-bold text-zinc-900">
                            <span>Total Pembayaran</span>
                            <span class="text-amber-700 text-lg">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-2xl bg-brand-yellow-muted/45 px-4 py-3 text-xs leading-relaxed text-brand-black/65">
                            Pesanan akan dibuat setelah alamat dan ekspedisi dipilih. Semua perhitungan akhir tetap divalidasi di server.
                        </div>
                        <button
                            type="submit"
                            class="w-full rounded-full bg-brand-black py-4 text-center text-base font-bold text-white transition-all hover:bg-brand-black/88"
                            @if ($addresses->isEmpty()) disabled @endif
                        >
                            Buat Pesanan Sekarang
                        </button>
                    </div>
                </x-ui.section-card>
            </div>
        </div>
    </form>
    @endif
</x-layout.app-page>
