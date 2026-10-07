{{--
    Storefront Homepage — Pixel Komunika (Figma Make parity)
    Rhythm: Hero → Categories → Products → Promo → Benefits → CTA → Footer (layout)
    Brand assets: hero-storefront.webp (mascot baked in) + brand-logo.png
--}}
<div>
    @if (session()->has('success'))
        <div class="fixed right-5 bottom-5 z-50 flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="check-circle" class="size-5" />
            <span>{{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="ml-2 text-emerald-100 underline hover:text-white">Lihat Keranjang</a>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed right-5 bottom-5 z-50 flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <x-icon name="x-circle" class="size-5" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $partaiMinimum = app(\App\Domains\Pricing\PriceCalculator::class)->partaiMinimumQuantity();
        $freeShippingThreshold = 'Rp '.number_format(config('store.shipping.free_store_courier_threshold'), 0, ',', '.');
    @endphp

    {{-- Hero: fills remaining viewport under sticky header --}}
    <section
        class="relative flex w-full flex-col overflow-hidden"
        style="min-height: calc(100svh - var(--storefront-header-height, 7rem));"
    >
        <img
            src="{{ asset('assets/hero/hero-storefront.webp') }}"
            alt=""
            aria-hidden="true"
            class="absolute inset-0 h-full w-full object-cover object-center sm:object-[70%_center]"
            width="1536"
            height="1024"
            fetchpriority="high"
            decoding="async"
        >

        {{-- Mobile: copy is centered over the whole image, so the scrim is uniform. Desktop: left-weighted. --}}
        <div class="absolute inset-0 bg-brand-black/65 sm:hidden" aria-hidden="true"></div>
        <div
            class="absolute inset-0 bg-linear-to-r from-brand-black/75 via-brand-black/45 to-brand-black/15 sm:via-brand-black/40 sm:to-transparent"
            aria-hidden="true"
        ></div>

        <div class="container-2xl relative z-10 flex flex-1 flex-col justify-center py-12 sm:py-16 lg:py-20">
            <div class="max-w-xl text-center lg:max-w-2xl lg:text-left">
                <h1 class="text-4xl leading-tight font-black text-white sm:text-5xl xl:text-6xl">
                    Belanja Elektronik<br>
                    <span class="text-brand-yellow">Harga Grosir</span>
                </h1>

                <p class="mt-4 max-w-xl text-base leading-relaxed text-white/90 sm:text-lg {{ auth()->check() ? '' : 'mx-auto lg:mx-0' }}">
                    Harga partai dan grosir untuk aksesoris elektronik, kartu data, voucher, dan pulsa. Terbuka untuk reseller yang akunnya sudah diverifikasi admin.
                </p>

                <ul class="mt-6 flex flex-wrap justify-center gap-x-5 gap-y-2 lg:justify-start">
                    @foreach (['Harga partai mulai '.$partaiMinimum.' unit per produk', 'Kurir toko H+1 area Bandung'] as $fact)
                        <li class="flex items-center gap-1.5 text-sm font-semibold text-white">
                            <x-icon name="check" class="size-4 shrink-0 text-brand-yellow" />
                            {{ $fact }}
                        </li>
                    @endforeach
                </ul>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                    <a
                        href="{{ route('products.index') }}"
                        wire:navigate
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-yellow px-7 py-3.5 text-base font-bold text-brand-black transition-colors hover:bg-brand-yellow-soft"
                    >
                        Lihat Katalog Produk
                    </a>
                    @guest
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border-2 border-white/70 px-7 py-3.5 text-base font-bold text-white transition-colors hover:bg-white/10"
                        >
                            Daftar Reseller
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <div class="relative z-10 h-8 w-full shrink-0 bg-white" style="clip-path: ellipse(60% 100% at 50% 100%)" aria-hidden="true"></div>
    </section>

    {{-- Categories: text-only; POS categories have no reliable icon mapping (R-04) --}}
    <section id="kategori" class="w-full border-b border-zinc-200/80 bg-white py-12 sm:py-16">
        <div class="container-2xl">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 sm:text-3xl">Kategori Produk</h2>
                    <p class="mt-1 text-sm text-zinc-600">Pilih kategori untuk melihat produknya di katalog.</p>
                </div>
                <a href="{{ route('products.index') }}" wire:navigate class="hidden min-h-11 items-center text-sm font-semibold text-brand-black underline-offset-4 hover:underline sm:inline-flex">
                    Lihat semua produk
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
                @forelse ($categories as $category)
                    <a
                        href="{{ route('products.index', ['kategori' => $category->id]) }}"
                        wire:navigate
                        class="flex min-h-16 items-center justify-center rounded-xl border border-zinc-200 bg-white p-4 text-center text-sm leading-tight font-semibold text-zinc-800 transition-colors hover:border-brand-black hover:bg-brand-yellow-muted"
                    >
                        {{ $category->name }}
                    </a>
                @empty
                    <div class="col-span-full">
                        <x-ui.empty-state title="Belum ada kategori" description="Kategori produk akan muncul setelah data POS tersedia." icon="layout-grid" />
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Featured products --}}
    <section id="katalog" class="w-full bg-surface-2 py-12 sm:py-16">
        <div class="container-2xl">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 sm:text-3xl">Produk Pilihan</h2>
                    <p class="mt-1 text-sm text-zinc-600">Sebagian produk dari katalog toko.</p>
                </div>
                <a href="{{ route('products.index') }}" wire:navigate class="hidden min-h-11 items-center text-sm font-semibold text-brand-black underline-offset-4 hover:underline sm:inline-flex">
                    Lihat semua produk
                </a>
            </div>

            <div class="mb-6 flex flex-wrap gap-2" role="group" aria-label="Filter kategori produk pilihan">
                <button
                    type="button"
                    wire:click="$set('selectedCategory', 'all')"
                    aria-pressed="{{ $selectedCategory === 'all' ? 'true' : 'false' }}"
                    class="min-h-11 rounded-full px-4 text-xs font-semibold transition {{ $selectedCategory === 'all' ? 'bg-brand-black text-white' : 'bg-white text-brand-black/80 hover:bg-zinc-100' }}"
                >
                    Semua
                </button>
                @foreach ($categories as $category)
                    <button
                        type="button"
                        wire:click="$set('selectedCategory', '{{ $category->id }}')"
                        aria-pressed="{{ (string) $selectedCategory === (string) $category->id ? 'true' : 'false' }}"
                        class="min-h-11 rounded-full px-4 text-xs font-semibold transition {{ (string) $selectedCategory === (string) $category->id ? 'bg-brand-black text-white' : 'bg-white text-brand-black/80 hover:bg-zinc-100' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4">
                @forelse ($products as $product)
                    @php $primaryMedia = $product->media->firstWhere('is_primary', true) ?? $product->media->first(); @endphp
                    <x-storefront.product-card
                        :title="$product->displayName()"
                        :image="$primaryMedia?->url()"
                        :badge="$product->enrichment?->label"
                        :category="$product->category->name"
                        :sku="$product->sku"
                        :stock-label="$product->inventorySnapshot?->quantity_available > 0 ? 'Stok: '.$product->inventorySnapshot->quantity_available : 'Habis'"
                        :stock-variant="$product->inventorySnapshot?->quantity_available > 0 ? 'available' : 'unavailable'"
                        :show-price="auth()->user()?->canViewPrices()"
                        :price="'Rp '.number_format($product->listPriceAmount() ?? 0, 0, ',', '.')"
                    >
                        <x-slot:actions>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <a
                                    href="{{ route('products.show', $product) }}"
                                    wire:navigate
                                    class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-md bg-zinc-100 px-3 text-xs font-bold text-brand-black transition hover:bg-zinc-200"
                                >
                                    Detail
                                </a>
                                @if (auth()->guest() || auth()->user()->canViewPrices())
                                <button
                                    type="button"
                                    wire:click="addToCart({{ $product->id }})"
                                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-1 rounded-md bg-brand-yellow px-3 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-soft"
                                >
                                    <x-icon name="shopping-cart" class="size-3.5" />
                                    + Keranjang
                                </button>
                                @endif
                            </div>
                        </x-slot:actions>
                    </x-storefront.product-card>
                @empty
                    <div class="col-span-full">
                        <x-ui.empty-state
                            title="Belum ada produk di kategori ini"
                            description="Pilih kategori lain atau buka katalog lengkap."
                            icon="package-search"
                            mascot
                            :action-href="route('products.index')"
                            action-label="Buka katalog"
                        />
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Promo + facts: guest acquisition only --}}
    @guest
    <x-storefront.banner
        theme="dark"
        background-image="{{ asset('assets/hero/cta-partai.webp') }}"
        title="Hemat Lebih Banyak dengan Harga Partai"
        :description="'Beli minimal '.$partaiMinimum.' unit untuk 1 produk dan nikmati harga partai yang lebih hemat. Semakin banyak, semakin murah!'"
        primary-label="Lihat Katalog Produk"
        :primary-href="route('products.index')"
    >
        <x-slot:aside>
            <dl class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach ([
                    ['value' => 'Min. '.$partaiMinimum.' unit', 'label' => 'Syarat harga partai per produk', 'icon' => 'package'],
                    ['value' => 'H+1 kerja', 'label' => 'Kurir toko area Bandung', 'icon' => 'truck'],
                    ['value' => 'Transfer bank', 'label' => 'Metode pembayaran', 'icon' => 'landmark'],
                    ['value' => 'Ongkir Rp0', 'label' => 'Kurir toko, belanja mulai '.$freeShippingThreshold, 'icon' => 'badge-percent'],
                ] as $stat)
                    <div class="rounded-xl border border-white/15 bg-brand-black/70 p-4 text-center sm:p-5">
                        <x-icon :name="$stat['icon']" class="mx-auto mb-2 size-5 text-brand-yellow" />
                        <dd class="text-sm font-bold text-white">{{ $stat['value'] }}</dd>
                        <dt class="mt-0.5 text-xs text-white/75">{{ $stat['label'] }}</dt>
                    </div>
                @endforeach
            </dl>
        </x-slot:aside>
    </x-storefront.banner>

    {{-- Facts a new reseller needs before buying (BR-006, BR-023, FR-AUTH) --}}
    <section id="unggulan" class="w-full bg-white py-12 sm:py-16">
        <div class="container-lg grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] lg:gap-16">
            <div>
                <h2 class="text-2xl font-black text-zinc-900 sm:text-3xl">Yang perlu diketahui sebelum belanja</h2>
                <p class="mt-2 text-sm text-zinc-600">Aturan yang sama berlaku untuk semua reseller.</p>
            </div>

            <dl class="divide-y divide-zinc-200 border-y border-zinc-200">
                <div class="flex gap-4 py-5">
                    <x-icon name="user-check" class="mt-0.5 size-5 shrink-0 text-brand-black" />
                    <div>
                        <dt class="font-bold text-zinc-900">Harga terbuka setelah akun disetujui</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-zinc-600">Daftarkan toko Anda. Setelah admin memverifikasi, harga dan checkout terbuka untuk akun tersebut.</dd>
                    </div>
                </div>
                <div class="flex gap-4 py-5">
                    <x-icon name="package" class="mt-0.5 size-5 shrink-0 text-brand-black" />
                    <div>
                        <dt class="font-bold text-zinc-900">Harga partai per produk, bukan per keranjang</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-zinc-600">Harga partai berlaku untuk seluruh pesanan bila minimal satu produk dibeli {{ $partaiMinimum }} unit atau lebih. Jumlah produk berbeda tidak dijumlahkan.</dd>
                    </div>
                </div>
                <div class="flex gap-4 py-5">
                    <x-icon name="truck" class="mt-0.5 size-5 shrink-0 text-brand-black" />
                    <div>
                        <dt class="font-bold text-zinc-900">Kurir toko H+1 untuk Kota dan Kabupaten Bandung</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-zinc-600">Ongkir kurir toko Rp0 bila subtotal barang ditambah PPh 22 mencapai {{ $freeShippingThreshold }}. Di luar area, pilih ekspedisi lain saat checkout.</dd>
                    </div>
                </div>
            </dl>
        </div>
    </section>

    <x-storefront.cta
        :primary-href="route('register')"
        :secondary-href="route('login')"
    />
    @endguest
</div>
