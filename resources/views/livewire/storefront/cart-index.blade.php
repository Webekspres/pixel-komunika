<x-layout.app-page>
    <x-storefront.breadcrumb :items="[['label' => 'Keranjang Belanja', 'href' => null]]" />

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
            mascot
        />
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-brand-black px-6 py-3 font-semibold text-white transition-all hover:bg-brand-black/88">
                Jelajahi Produk
            </a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                <x-ui.section-card title="Daftar Produk ({{ $summary['total_items'] }} Item)" description="Semua harga dan estimasi tetap dihitung server-side mengikuti data POS aktif.">
                    <div class="divide-y divide-zinc-200">
                        @foreach ($summary['items'] as $item)
                            <div class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="flex size-16 shrink-0 items-center justify-center rounded-[1.4rem] bg-brand-yellow-muted text-brand-black">
                                        <x-icon name="package" class="size-7 text-brand-black/45" />
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="text-lg font-semibold text-zinc-900">{{ $item['product']->name }}</h4>
                                        <p class="text-xs text-zinc-500">SKU: {{ $item['product']->sku }} | Tipe Harga: <span class="font-medium text-amber-700">{{ $item['price_type'] }}</span></p>
                                        <p class="text-sm font-medium text-zinc-700">Rp {{ number_format($item['unit_price'], 0, ',', '.') }} / unit</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="flex items-center overflow-hidden rounded-2xl border border-brand-black/10 bg-white">
                                        <button
                                            wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                            class="px-3 py-2 text-zinc-600 hover:bg-zinc-100 font-bold"
                                        >-</button>
                                        <span class="px-4 py-2 text-sm font-semibold text-zinc-800">{{ $item['quantity'] }}</span>
                                        <button
                                            wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                            class="px-3 py-2 text-zinc-600 hover:bg-zinc-100 font-bold"
                                        >+</button>
                                    </div>

                                    <div class="min-w-[110px] text-right">
                                        <p class="font-bold text-zinc-900">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</p>
                                    </div>

                                    <button
                                        wire:click="removeItem({{ $item['id'] }})"
                                        class="rounded-full p-2 text-sm font-semibold text-red-500 hover:bg-red-50 hover:text-red-700"
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
                <x-ui.section-card title="Ringkasan Belanja" description="Ringkasan ini membantu Anda mengecek kesiapan sebelum lanjut ke checkout.">
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

                        <div class="flex justify-between border-t border-zinc-200 pt-3 text-base font-bold text-zinc-900">
                            <span>Estimasi Total</span>
                            <span class="text-amber-700">Rp {{ number_format($summary['subtotal'] + $summary['pph22'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        @auth
                            @if (auth()->user()->customerStatus() === \App\Models\CustomerProfile::ACTIVE || auth()->user()->isAdmin())
                                <a href="{{ route('checkout.index') }}" class="block w-full rounded-full bg-brand-black py-3.5 text-center font-bold text-white transition-all hover:bg-brand-black/88">
                                    Lanjut ke Checkout
                                </a>
                            @else
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3 text-center text-xs font-medium text-amber-800">
                                    Akun Anda masih menunggu verifikasi admin untuk melakukan checkout.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full rounded-full bg-brand-yellow py-3.5 text-center font-bold text-brand-black transition-all hover:bg-brand-yellow-soft">
                                Login untuk Checkout
                            </a>
                        @endauth
                    </div>
                </x-ui.section-card>
            </div>
        </div>
    @endif
</x-layout.app-page>
