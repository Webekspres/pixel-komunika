<div
    x-data="{ isOpen: @entangle('isOpen') }"
    @keydown.escape.window="isOpen = false"
    @open-cart-drawer.window="isOpen = true"
>
    {{-- Backdrop Overlay --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition-opacity duration-300 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-zinc-950/50"
        @click="isOpen = false"
        x-cloak
        aria-hidden="true"
    ></div>

    {{-- =========================================================================
         DESKTOP SLIDE-OVER DRAWER (Right 38% width)
         ========================================================================= --}}
    <aside
        x-show="isOpen"
        x-transition:enter="transition transform duration-300 ease-out"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform duration-250 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 hidden w-full max-w-md flex-col border-l border-brand-black/8 bg-white md:flex xl:max-w-lg"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-drawer-title"
        x-trap="isOpen && window.innerWidth >= 768"
        x-cloak
    >
        {{-- Header --}}
        <div class="border-b border-brand-black/8 bg-brand-yellow p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-icon name="shopping-cart" class="size-5 text-brand-black" />
                    <h3 id="cart-drawer-title" class="text-lg font-bold text-brand-black">Keranjang Belanja</h3>
                    <span class="rounded-full bg-brand-black px-2.5 py-0.5 text-xs font-bold text-brand-yellow">
                        {{ $summary['total_items'] }}
                    </span>
                </div>
                <button type="button" @click="isOpen = false" class="inline-flex size-11 items-center justify-center rounded-full text-brand-black transition-colors hover:bg-white/60" aria-label="Tutup keranjang">
                    <x-icon name="x" class="size-5" />
                </button>
            </div>
        </div>

        @if ($summary['items']->isNotEmpty())
            <div class="flex items-center justify-between border-b border-brand-black/8 bg-white px-5 py-2.5">
                <span class="text-[11px] font-medium text-brand-black/65">{{ $summary['items']->count() }} jenis produk</span>
                <x-ui.confirm-dialog name="clear-cart-drawer" title="Kosongkan keranjang" description="Semua produk di keranjang akan dihapus. Tindakan ini tidak dapat dibatalkan.">
                    <x-slot:trigger>
                    <button
                        type="button"
                        class="inline-flex min-h-11 items-center gap-1.5 text-xs font-bold text-red-700 transition-colors hover:underline"
                    >
                        <x-icon name="trash-2" class="size-3.5" />
                        Hapus Semua
                    </button>
                    </x-slot:trigger>
                    <x-slot:confirm>
                        <flux:modal.close>
                            <flux:button variant="danger" wire:click="clearCart">Ya, kosongkan</flux:button>
                        </flux:modal.close>
                    </x-slot:confirm>
                </x-ui.confirm-dialog>
            </div>
        @endif

        {{-- Cart Items Scrollable List --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            @if ($summary['items']->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="inline-flex size-16 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 mb-4">
                        <x-icon name="shopping-cart" class="size-8" />
                    </div>
                    <h4 class="font-bold text-zinc-900 text-base">Keranjang Anda Kosong</h4>
                    <p class="text-xs text-zinc-500 mt-1 max-w-xs">Belum ada produk yang ditambahkan ke keranjang belanja Anda.</p>
                    <a href="{{ route('products.index') }}" wire:navigate @click="isOpen = false" class="mt-6 inline-flex min-h-11 items-center gap-2 rounded-xl bg-brand-yellow px-5 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition-colors">
                        Lihat Katalog Produk
                    </a>
                </div>
            @else
                @foreach ($summary['items'] as $item)
                    <div class="flex gap-4 rounded-xl border border-brand-black/8 bg-zinc-50/70 p-3">
                        <div class="size-16 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                            <x-icon name="package" class="size-8 text-zinc-500" />
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-zinc-900 text-xs truncate">{{ $item['product']->name }}</h4>
                                    <p class="text-[11px] text-zinc-600 mt-0.5">SKU: {{ $item['product']->sku }}</p>
                                </div>
                                @include('livewire.storefront.partials.cart-remove-item', ['item' => $item, 'context' => 'drawer'])
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <div class="flex items-center overflow-hidden rounded-xl border border-brand-black/15 bg-white">
                                    <button type="button" wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="inline-flex size-11 items-center justify-center text-zinc-700 hover:bg-zinc-100" aria-label="Kurangi jumlah {{ $item['product']->name }}"><x-icon name="minus" class="size-3.5" /></button>
                                    <span class="w-8 text-center font-bold text-zinc-800 text-xs">{{ $item['quantity'] }}</span>
                                    <button type="button" wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="inline-flex size-11 items-center justify-center text-zinc-700 hover:bg-zinc-100" aria-label="Tambah jumlah {{ $item['product']->name }}"><x-icon name="plus" class="size-3.5" /></button>
                                </div>
                                <span class="font-extrabold text-zinc-950 text-xs">
                                    Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Footer Summary & Actions --}}
        @if ($summary['items']->isNotEmpty())
            <div class="space-y-4 border-t border-brand-black/8 bg-white p-5">
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-zinc-500">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-500">
                        <span>Estimasi PPh 22</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($summary['pph22'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-zinc-900 pt-2 border-t border-zinc-100">
                        <span>Estimasi Total</span>
                        <span class="text-amber-700">Rp {{ number_format($summary['subtotal'] + $summary['pph22'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-zinc-600">Ongkir kurir toko Rp0 untuk area Bandung bila subtotal + PPh 22 mencapai Rp {{ number_format(config('store.shipping.free_store_courier_threshold'), 0, ',', '.') }}.</p>

                @if ($checkoutBlocker && auth()->user()?->isActiveCustomer())
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-medium text-amber-800">{{ $checkoutBlocker }}</p>
                @endif

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <a
                        href="{{ route('cart.index') }}"
                        wire:navigate
                        @click="isOpen = false"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-zinc-100 text-xs font-bold text-zinc-800 transition-colors hover:bg-zinc-200"
                    >
                        Halaman Keranjang
                    </a>

                    @auth
                        @if (auth()->user()->isActiveCustomer() && ! $checkoutBlocker)
                            <a
                                href="{{ route('checkout.index') }}"
                                wire:navigate
                                @click="isOpen = false"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-black text-xs font-bold text-brand-white transition-colors hover:bg-brand-black/88"
                            >
                                Checkout Sekarang
                            </a>
                        @else
                            <button type="button" disabled class="inline-flex min-h-11 cursor-not-allowed items-center justify-center rounded-xl bg-zinc-200 text-xs font-bold text-zinc-700">
                                {{ match (true) {
                                    auth()->user()->isActiveCustomer() => 'Belum Bisa Checkout',
                                    auth()->user()->customerStatus() === \App\Models\CustomerProfile::PENDING => 'Menunggu Verifikasi',
                                    default => 'Checkout Terkunci',
                                } }}
                            </button>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-yellow text-xs font-bold text-brand-black transition-colors hover:bg-brand-yellow-soft"
                        >
                            Masuk untuk Checkout
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </aside>

    {{-- =========================================================================
         MOBILE BOTTOM SHEET (Full Height Touch Sheet)
         ========================================================================= --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition transform duration-300 ease-out"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition transform duration-250 ease-in"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed inset-x-0 bottom-0 z-50 flex max-h-[90vh] flex-col rounded-t-3xl bg-white shadow-2xl md:hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-sheet-title"
        x-trap="isOpen && window.innerWidth < 768"
        x-cloak
    >
        {{-- Touch Pill & Header --}}
        <div class="flex flex-col items-center border-b border-brand-black/8 bg-brand-yellow-muted/45 pb-2 pt-3">
            <div class="w-12 h-1.5 bg-zinc-300 rounded-full mb-3"></div>
            <div class="flex items-center justify-between w-full px-5">
                <div class="flex items-center gap-2">
                    <x-icon name="shopping-cart" class="size-5 text-zinc-900" />
                    <h3 id="cart-sheet-title" class="font-bold text-zinc-900 text-base">Keranjang Belanja</h3>
                    <span class="rounded-full bg-brand-yellow px-2 py-0.5 text-xs font-bold text-brand-black">
                        {{ $summary['total_items'] }}
                    </span>
                </div>
                <button type="button" @click="isOpen = false" class="inline-flex size-11 items-center justify-center text-zinc-700 hover:text-zinc-950" aria-label="Tutup keranjang">
                    <x-icon name="x" class="size-6" />
                </button>
            </div>
            @if ($summary['items']->isNotEmpty())
                <div class="mt-2 flex w-full items-center justify-between px-5 pb-1">
                    <span class="text-[11px] font-medium text-zinc-600">{{ $summary['items']->count() }} jenis produk</span>
                    <x-ui.confirm-dialog name="clear-cart-sheet" title="Kosongkan keranjang" description="Semua produk di keranjang akan dihapus. Tindakan ini tidak dapat dibatalkan.">
                        <x-slot:trigger>
                        <button
                            type="button"
                            class="inline-flex min-h-11 items-center gap-1 text-xs font-bold text-red-700"
                        >
                            <x-icon name="trash-2" class="size-3.5" />
                            Hapus Semua
                        </button>
                        </x-slot:trigger>
                        <x-slot:confirm>
                            <flux:modal.close>
                                <flux:button variant="danger" wire:click="clearCart">Ya, kosongkan</flux:button>
                            </flux:modal.close>
                        </x-slot:confirm>
                    </x-ui.confirm-dialog>
                </div>
            @endif
        </div>

        {{-- Mobile Scrollable Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @if ($summary['items']->isEmpty())
                <div class="text-center py-10">
                    <x-icon name="shopping-cart" class="size-10 text-zinc-300 mx-auto mb-2" />
                    <p class="font-bold text-zinc-800 text-sm">Keranjang Anda Kosong</p>
                    <a href="{{ route('products.index') }}" wire:navigate @click="isOpen = false" class="mt-4 inline-flex min-h-11 items-center rounded-xl bg-brand-yellow px-4 text-xs font-bold text-brand-black">
                        Lihat Katalog Produk
                    </a>
                </div>
            @else
                @foreach ($summary['items'] as $item)
                    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="font-bold text-zinc-900 text-xs line-clamp-2">{{ $item['product']->name }}</h4>
                                <p class="text-[11px] text-zinc-700 font-bold mt-0.5">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</p>
                            </div>
                            @include('livewire.storefront.partials.cart-remove-item', ['item' => $item, 'context' => 'sheet'])
                        </div>
                        <div class="mt-2 inline-flex items-center border border-zinc-300 rounded-lg overflow-hidden bg-white">
                            <button type="button" wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="inline-flex size-11 items-center justify-center text-zinc-700" aria-label="Kurangi jumlah {{ $item['product']->name }}"><x-icon name="minus" class="size-4" /></button>
                            <span class="w-10 text-center font-bold text-zinc-800 text-sm">{{ $item['quantity'] }}</span>
                            <button type="button" wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="inline-flex size-11 items-center justify-center text-zinc-700" aria-label="Tambah jumlah {{ $item['product']->name }}"><x-icon name="plus" class="size-4" /></button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Mobile Sticky Checkout Summary --}}
        @if ($summary['items']->isNotEmpty())
            <div class="sticky bottom-0 space-y-3 border-t border-brand-black/8 bg-white p-4 shadow-lg">
                <div class="flex justify-between text-xs font-bold text-zinc-900">
                    <span>Total Estimasi:</span>
                    <span class="text-amber-700 text-sm">Rp {{ number_format($summary['subtotal'] + $summary['pph22'], 0, ',', '.') }}</span>
                </div>
                <p class="text-[11px] leading-relaxed text-zinc-600">Ongkir kurir toko Rp0 untuk area Bandung bila subtotal + PPh 22 mencapai Rp {{ number_format(config('store.shipping.free_store_courier_threshold'), 0, ',', '.') }}.</p>

                @if ($checkoutBlocker && auth()->user()?->isActiveCustomer())
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-medium text-amber-800">{{ $checkoutBlocker }}</p>
                @endif

                <div class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('cart.index') }}"
                        wire:navigate
                        @click="isOpen = false"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-zinc-100 text-center text-xs font-bold text-zinc-800"
                    >
                        Lihat Keranjang
                    </a>

                    @auth
                        @if (auth()->user()->isActiveCustomer() && ! $checkoutBlocker)
                            <a
                                href="{{ route('checkout.index') }}"
                                wire:navigate
                                @click="isOpen = false"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-black text-center text-xs font-bold text-brand-white"
                            >
                                Checkout
                            </a>
                        @else
                            <button type="button" disabled class="inline-flex min-h-11 cursor-not-allowed items-center justify-center rounded-xl bg-zinc-200 text-center text-xs font-bold text-zinc-700">
                                {{ match (true) {
                                    auth()->user()->isActiveCustomer() => 'Belum Bisa Checkout',
                                    auth()->user()->customerStatus() === \App\Models\CustomerProfile::PENDING => 'Menunggu Verifikasi',
                                    default => 'Checkout Terkunci',
                                } }}
                            </button>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-yellow text-center text-xs font-bold text-brand-black"
                        >
                            Masuk untuk Checkout
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</div>
