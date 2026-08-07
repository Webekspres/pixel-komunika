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
        class="fixed inset-0 z-50 bg-zinc-950/50 backdrop-blur-xs"
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
        class="fixed inset-y-0 right-0 z-50 hidden md:flex w-full max-w-md xl:max-w-lg flex-col bg-white shadow-2xl border-l border-zinc-200"
        x-cloak
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-zinc-200 p-5">
            <div class="flex items-center gap-2">
                <x-icon name="shopping-cart" class="size-5 text-zinc-900" />
                <h3 class="font-bold text-zinc-900 text-lg">Keranjang Belanja</h3>
                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                    {{ $summary['total_items'] }}
                </span>
            </div>
            <button @click="isOpen = false" class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 transition-colors">
                <x-icon name="x" class="size-5" />
            </button>
        </div>

        {{-- Cart Items Scrollable List --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            @if ($summary['items']->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="inline-flex size-16 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 mb-4">
                        <x-icon name="shopping-cart" class="size-8" />
                    </div>
                    <h4 class="font-bold text-zinc-900 text-base">Keranjang Anda Kosong</h4>
                    <p class="text-xs text-zinc-500 mt-1 max-w-xs">Belum ada produk yang ditambahkan ke keranjang belanja Anda.</p>
                    <button @click="isOpen = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-xs font-bold text-brand-black hover:bg-amber-300 transition-colors">
                        Mulai Belanja
                    </button>
                </div>
            @else
                @foreach ($summary['items'] as $item)
                    <div class="flex gap-4 p-3 rounded-2xl border border-zinc-200/80 bg-zinc-50/50">
                        <div class="size-16 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                            <x-icon name="package" class="size-8 text-zinc-400" />
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-bold text-zinc-900 text-xs truncate">{{ $item['product']->name }}</h4>
                                    <button wire:click="removeItem({{ $item['id'] }})" class="text-zinc-400 hover:text-red-600 transition-colors" title="Hapus">
                                        <x-icon name="trash-2" class="size-3.5" />
                                    </button>
                                </div>
                                <p class="text-[10px] text-zinc-400 mt-0.5">SKU: {{ $item['product']->sku }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <div class="flex items-center border border-zinc-300 rounded-lg overflow-hidden bg-white">
                                    <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="px-2 py-0.5 text-zinc-600 hover:bg-zinc-100 font-bold text-xs">-</button>
                                    <span class="px-2.5 py-0.5 font-bold text-zinc-800 text-xs">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="px-2 py-0.5 text-zinc-600 hover:bg-zinc-100 font-bold text-xs">+</button>
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
            <div class="border-t border-zinc-200 p-5 bg-white space-y-4">
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

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <a
                        href="{{ route('cart.index') }}"
                        wire:navigate
                        @click="isOpen = false"
                        class="inline-flex items-center justify-center rounded-xl bg-zinc-100 py-3 text-xs font-bold text-zinc-800 hover:bg-zinc-200 transition-colors"
                    >
                        Halaman Keranjang
                    </a>

                    @auth
                        @if (auth()->user()->customerStatus() === \App\Models\CustomerProfile::ACTIVE || auth()->user()->isAdmin())
                            <a
                                href="{{ route('checkout.index') }}"
                                wire:navigate
                                @click="isOpen = false"
                                class="inline-flex items-center justify-center rounded-xl bg-amber-400 py-3 text-xs font-bold text-brand-black hover:bg-amber-300 transition-colors shadow-xs"
                            >
                                Checkout Sekarang
                            </a>
                        @else
                            <button disabled class="inline-flex items-center justify-center rounded-xl bg-zinc-200 py-3 text-xs font-bold text-zinc-500 cursor-not-allowed">
                                Menunggu Verifikasi
                            </button>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-zinc-900 py-3 text-xs font-bold text-white hover:bg-zinc-800 transition-colors"
                        >
                            Login Checkout
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
        class="fixed inset-x-0 bottom-0 z-50 md:hidden flex flex-col bg-white rounded-t-3xl shadow-2xl max-h-[90vh]"
        x-cloak
    >
        {{-- Touch Pill & Header --}}
        <div class="flex flex-col items-center pt-3 pb-2 border-b border-zinc-100">
            <div class="w-12 h-1.5 bg-zinc-300 rounded-full mb-3"></div>
            <div class="flex items-center justify-between w-full px-5">
                <div class="flex items-center gap-2">
                    <x-icon name="shopping-cart" class="size-5 text-zinc-900" />
                    <h3 class="font-bold text-zinc-900 text-base">Keranjang Belanja</h3>
                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">
                        {{ $summary['total_items'] }}
                    </span>
                </div>
                <button @click="isOpen = false" class="text-zinc-400 hover:text-zinc-700 p-1">
                    <x-icon name="x" class="size-6" />
                </button>
            </div>
        </div>

        {{-- Mobile Scrollable Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @if ($summary['items']->isEmpty())
                <div class="text-center py-10">
                    <x-icon name="shopping-cart" class="size-10 text-zinc-300 mx-auto mb-2" />
                    <p class="font-bold text-zinc-800 text-sm">Keranjang Anda Kosong</p>
                    <button @click="isOpen = false" class="mt-4 px-4 py-2 bg-amber-400 rounded-xl text-xs font-bold text-brand-black">
                        Mulai Belanja
                    </button>
                </div>
            @else
                @foreach ($summary['items'] as $item)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl border border-zinc-200/80 bg-zinc-50/50">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="size-12 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                                <x-icon name="package" class="size-6 text-zinc-400" />
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-zinc-900 text-xs truncate">{{ $item['product']->name }}</h4>
                                <p class="text-[10px] text-zinc-500 font-bold mt-0.5">Rp {{ number_format($item['line_subtotal'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <div class="flex items-center border border-zinc-300 rounded-lg overflow-hidden bg-white">
                                <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="px-2 py-1 text-zinc-600 font-bold text-xs">-</button>
                                <span class="px-2 py-1 font-bold text-zinc-800 text-xs">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="px-2 py-1 text-zinc-600 font-bold text-xs">+</button>
                            </div>
                            <button wire:click="removeItem({{ $item['id'] }})" class="text-red-500 p-1">
                                <x-icon name="trash-2" class="size-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Mobile Sticky Checkout Summary --}}
        @if ($summary['items']->isNotEmpty())
            <div class="sticky bottom-0 bg-white border-t border-zinc-200 p-4 space-y-3 shadow-lg">
                <div class="flex justify-between text-xs font-bold text-zinc-900">
                    <span>Total Estimasi:</span>
                    <span class="text-amber-700 text-sm">Rp {{ number_format($summary['subtotal'] + $summary['pph22'], 0, ',', '.') }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('cart.index') }}"
                        wire:navigate
                        @click="isOpen = false"
                        class="inline-flex items-center justify-center rounded-xl bg-zinc-100 py-3 text-xs font-bold text-zinc-800 text-center"
                    >
                        Keranjang Full
                    </a>

                    @auth
                        @if (auth()->user()->customerStatus() === \App\Models\CustomerProfile::ACTIVE || auth()->user()->isAdmin())
                            <a
                                href="{{ route('checkout.index') }}"
                                wire:navigate
                                @click="isOpen = false"
                                class="inline-flex items-center justify-center rounded-xl bg-amber-400 py-3 text-xs font-bold text-brand-black text-center shadow-xs"
                            >
                                Checkout
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-zinc-900 py-3 text-xs font-bold text-white text-center"
                        >
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</div>
