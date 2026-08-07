<x-layout.app-page>
    <x-ui.page-header
        eyebrow="E-Commerce"
        title="Keranjang Belanja"
        description="Periksa item pilihan Anda, kuantitas, serta rincian estimasi pajak PPh 22 sebelum checkout."
    />

    @if (session()->has('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($summary['items']->isEmpty())
        <x-ui.empty-state
            title="Keranjang Belanja Kosong"
            description="Anda belum menambahkan produk ke keranjang belanja."
            icon="shopping-cart"
        />
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-[#F8B818] px-6 py-3 font-semibold text-[#181818] hover:bg-[#F8D820] transition-all shadow-sm">
                Jelajahi Produk
            </a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                <x-ui.section-card title="Daftar Produk ({{ $summary['total_items'] }} Item)">
                    <div class="divide-y divide-zinc-200">
                        @foreach ($summary['items'] as $item)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <h4 class="font-semibold text-zinc-900 text-lg">{{ $item['product']->name }}</h4>
                                    <p class="text-xs text-zinc-500">SKU: {{ $item['product']->sku }} | Tipe Harga: <span class="font-medium text-amber-700">{{ $item['price_type'] }}</span></p>
                                    <p class="text-sm font-medium text-zinc-700">Rp {{ number_format($item['unit_price'], 0, ',', '.') }} / unit</p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="flex items-center border border-zinc-300 rounded-xl overflow-hidden bg-white">
                                        <button
                                            wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                            class="px-3 py-1.5 text-zinc-600 hover:bg-zinc-100 font-bold"
                                        >-</button>
                                        <span class="px-4 py-1.5 font-semibold text-zinc-800 text-sm">{{ $item['quantity'] }}</span>
                                        <button
                                            wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                            class="px-3 py-1.5 text-zinc-600 hover:bg-zinc-100 font-bold"
                                        >+</button>
                                    </div>

                                    <div class="text-right min-w-[100px]">
                                        <p class="font-bold text-zinc-900">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</p>
                                    </div>

                                    <button
                                        wire:click="removeItem({{ $item['id'] }})"
                                        class="text-red-500 hover:text-red-700 p-2 text-sm font-semibold"
                                        title="Hapus item"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.section-card>
            </div>

            <div class="space-y-4">
                <x-ui.section-card title="Ringkasan Belanja">
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
                            <span>Total Berat</span>
                            <span class="font-semibold text-zinc-900">{{ number_format($summary['total_weight_grams'] / 1000, 2) }} kg</span>
                        </div>

                        <div class="border-t border-zinc-200 pt-3 flex justify-between text-base font-bold text-zinc-900">
                            <span>Estimasi Total</span>
                            <span class="text-amber-700">Rp {{ number_format($summary['subtotal'] + $summary['pph22'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        @auth
                            @if (auth()->user()->customerStatus() === \App\Models\CustomerProfile::ACTIVE || auth()->user()->isAdmin())
                                <a href="{{ route('checkout.index') }}" class="block w-full text-center rounded-xl bg-[#F8B818] py-3.5 font-bold text-[#181818] hover:bg-[#F8D820] transition-all shadow-md">
                                    Lanjut ke Checkout
                                </a>
                            @else
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 text-center font-medium">
                                    Akun Anda masih menunggu verifikasi admin untuk melakukan checkout.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center rounded-xl bg-[#181818] py-3.5 font-bold text-white hover:bg-zinc-800 transition-all shadow-md">
                                Login untuk Checkout
                            </a>
                        @endauth
                    </div>
                </x-ui.section-card>
            </div>
        </div>
    @endif
</x-layout.app-page>
