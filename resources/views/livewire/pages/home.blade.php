{{--
    Storefront Homepage — Pixel Komunika (Figma Make parity)
    Rhythm: Hero → Categories → Products → Promo → Benefits → CTA → Footer (layout)
    Brand assets: hero-storefront.webp (mascot baked in) + brand-logo.png
--}}
<div>
    @if (session()->has('success'))
        <div class="fixed right-5 bottom-5 z-50 flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
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

    {{-- Hero: fills remaining viewport under sticky header (no leftover on scroll) --}}
    <section
        class="relative flex w-full flex-col overflow-hidden"
        style="height: calc(100svh - var(--storefront-header-height, 8rem)); min-height: calc(100svh - var(--storefront-header-height, 8rem));"
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

        {{-- Left-weighted overlay for copy contrast --}}
        <div
            class="absolute inset-0 bg-linear-to-r from-brand-black/75 via-brand-black/45 to-brand-black/15 sm:via-brand-black/40 sm:to-transparent"
            aria-hidden="true"
        ></div>

        <div class="container-2xl relative z-10 flex flex-1 flex-col justify-center py-12 sm:py-16 lg:py-20">
            <div class="max-w-xl text-center lg:max-w-2xl lg:text-left">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm">
                    <x-icon name="star" class="size-3 fill-current text-brand-yellow" />
                    Platform B2B Terpercaya
                </div>

                <h1 class="text-4xl leading-tight font-black text-white sm:text-5xl xl:text-6xl">
                    Belanja Elektronik<br>
                    <span class="text-brand-yellow">Harga Grosir</span>
                </h1>

                <p class="mt-4 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg {{ auth()->check() ? '' : 'mx-auto lg:mx-0' }}">
                    Dapatkan akses eksklusif ke harga partai dan grosir untuk elektronik, aksesoris, dan produk telekomunikasi berkualitas tinggi.
                </p>

                <div class="mt-6 flex flex-wrap justify-center gap-4 lg:justify-start">
                    @foreach (['500+ Produk', '200+ Reseller', 'Pengiriman Bandung', 'Harga Terjamin'] as $badge)
                        <div class="flex items-center gap-1.5 text-sm font-semibold text-white">
                            <div class="flex size-4 items-center justify-center rounded-full bg-brand-yellow">
                                <span class="text-[10px] text-brand-black">✓</span>
                            </div>
                            {{ $badge }}
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                    <a
                        href="{{ route('products.index') }}"
                        wire:navigate
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-yellow px-7 py-3.5 text-base font-bold text-brand-black transition-colors hover:bg-brand-yellow-soft"
                    >
                        Belanja Sekarang
                        <x-icon name="arrow-right" class="size-4.5" />
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-white/40 bg-white/15 px-7 py-3.5 text-base font-bold text-white backdrop-blur-sm transition-colors hover:bg-white/25"
                    >
                        Daftar Pelanggan
                    </a>
                </div>
            </div>
        </div>

        <div class="relative z-10 h-8 w-full shrink-0 bg-white" style="clip-path: ellipse(60% 100% at 50% 100%)" aria-hidden="true"></div>
    </section>

    {{-- Categories --}}
    <section id="kategori" class="w-full bg-white py-12 sm:py-16">
        <div class="container-2xl">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 sm:text-3xl">Kategori Produk</h2>
                    <p class="mt-1 text-sm text-zinc-500">Temukan produk sesuai kebutuhanmu</p>
                </div>
                <a href="{{ route('products.index') }}" wire:navigate class="hidden items-center gap-1 text-sm font-semibold text-brand-black transition-colors hover:text-brand-yellow sm:inline-flex">
                    Lihat Semua
                    <x-icon name="arrow-right" class="size-3.5" />
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                @forelse ($categories as $category)
                    @php
                        $icons = ['battery', 'plug', 'headphones', 'database', 'camera', 'phone', 'keyboard', 'package'];
                        $icon = $icons[$loop->index % count($icons)];
                    @endphp
                    <a
                        href="{{ route('products.index', ['kategori' => $category->id]) }}"
                        wire:navigate
                        class="group flex flex-col items-center gap-2 rounded-2xl border-2 border-transparent bg-zinc-50 p-3 text-center transition-all duration-200 hover:border-brand-yellow hover:bg-brand-yellow/10 sm:p-4"
                    >
                        <div class="inline-flex size-11 items-center justify-center rounded-xl bg-white shadow-sm">
                            <x-icon :name="$icon" class="size-5 text-brand-black" />
                        </div>
                        <span class="text-xs leading-tight font-semibold text-zinc-700 group-hover:text-brand-black">{{ $category->name }}</span>
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
    <section id="katalog" class="section-gray w-full py-12 sm:py-16">
        <div class="container-2xl">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 sm:text-3xl">Produk Pilihan</h2>
                    <p class="mt-1 text-sm text-zinc-500">Produk terlaris dengan kualitas terjamin</p>
                </div>
                <a href="{{ route('products.index') }}" wire:navigate class="hidden items-center gap-1 text-sm font-semibold text-brand-black transition-colors hover:text-brand-yellow sm:inline-flex">
                    Lihat Semua
                    <x-icon name="arrow-right" class="size-3.5" />
                </a>
            </div>

            <div class="mb-6 flex flex-wrap gap-2">
                <button
                    wire:click="$set('selectedCategory', 'all')"
                    class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $selectedCategory === 'all' ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                >
                    Semua
                </button>
                @foreach ($categories as $category)
                    <button
                        wire:click="$set('selectedCategory', '{{ $category->id }}')"
                        class="rounded-full px-4 py-2 text-xs font-semibold transition {{ (string) $selectedCategory === (string) $category->id ? 'bg-brand-black text-white' : 'bg-white text-brand-black/72 hover:bg-zinc-100' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                @forelse ($products as $product)
                    <x-storefront.product-card
                        :title="$product->name"
                        :category="$product->category->name"
                        :sku="$product->sku"
                        :stock-label="$product->inventorySnapshot?->quantity_available > 0 ? 'Stok: '.$product->inventorySnapshot->quantity_available : 'Habis'"
                        :stock-variant="$product->inventorySnapshot?->quantity_available > 0 ? 'available' : 'unavailable'"
                        :show-price="auth()->user()?->canViewPrices()"
                        :price="'Rp '.number_format($product->listPriceAmount() ?? 0, 0, ',', '.')"
                    >
                        <x-slot:actions>
                            <div class="grid grid-cols-2 gap-2">
                                <a
                                    href="{{ route('products.show', $product) }}"
                                    wire:navigate
                                    class="inline-flex items-center justify-center rounded-2xl bg-zinc-100 px-3 py-2.5 text-xs font-bold text-brand-black transition hover:bg-zinc-200"
                                >
                                    Detail
                                </a>
                                <button
                                    wire:click="addToCart({{ $product->id }})"
                                    class="inline-flex items-center justify-center gap-1 rounded-2xl bg-brand-yellow px-3 py-2.5 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-soft"
                                >
                                    <x-icon name="shopping-cart" class="size-3.5" />
                                    + Keranjang
                                </button>
                            </div>
                        </x-slot:actions>
                    </x-storefront.product-card>
                @empty
                    <div class="col-span-full">
                        <x-ui.empty-state
                            title="Tidak ada produk ditemukan"
                            description="Coba gunakan kata kunci pencarian atau kategori lain."
                            icon="package-search"
                            mascot
                        />
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Promo dark banner --}}
    <x-storefront.banner
        theme="dark"
        title="Hemat Lebih Banyak dengan Harga Partai"
        description="Beli minimal 5 unit untuk 1 produk dan nikmati harga partai yang lebih hemat. Semakin banyak, semakin murah!"
        primary-label="Mulai Belanja"
        :primary-href="route('products.index')"
        :secondary-label="auth()->guest() ? 'Daftar Gratis' : null"
        :secondary-href="auth()->guest() ? route('register') : null"
    />

    {{-- Benefits --}}
    <section id="unggulan" class="w-full bg-white py-12 sm:py-16">
        <div class="container-2xl">
            <div class="mb-10 text-center">
                <h2 class="text-2xl font-black text-zinc-900 sm:text-3xl">Mengapa Pixel Komunika?</h2>
                <p class="mt-2 text-sm text-zinc-500">Keunggulan yang membuat kami berbeda</p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-zinc-50 p-5 transition-shadow hover:shadow-md">
                    <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-emerald-100">
                        <x-icon name="shield-check" class="size-5 text-emerald-600" />
                    </div>
                    <h3 class="mb-2 font-bold text-zinc-900">Produk Asli</h3>
                    <p class="text-sm leading-relaxed text-zinc-500">Semua produk 100% original bergaransi dari distributor resmi</p>
                </div>
                <div class="rounded-2xl bg-zinc-50 p-5 transition-shadow hover:shadow-md">
                    <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-blue-100">
                        <x-icon name="truck" class="size-5 text-blue-600" />
                    </div>
                    <h3 class="mb-2 font-bold text-zinc-900">Pengiriman Cepat</h3>
                    <p class="text-sm leading-relaxed text-zinc-500">Kurir toko H+1 ke seluruh Bandung, atau ekspedisi nasional</p>
                </div>
                <div class="rounded-2xl bg-zinc-50 p-5 transition-shadow hover:shadow-md">
                    <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-brand-yellow/20">
                        <x-icon name="users" class="size-5 text-brand-yellow-dark" />
                    </div>
                    <h3 class="mb-2 font-bold text-zinc-900">Khusus Terverifikasi</h3>
                    <p class="text-sm leading-relaxed text-zinc-500">Harga partai eksklusif hanya untuk reseller yang telah diverifikasi</p>
                </div>
                <div class="rounded-2xl bg-zinc-50 p-5 transition-shadow hover:shadow-md">
                    <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-red-100">
                        <x-icon name="star" class="size-5 text-red-500" />
                    </div>
                    <h3 class="mb-2 font-bold text-zinc-900">Layanan Prioritas</h3>
                    <p class="text-sm leading-relaxed text-zinc-500">Dukungan via WhatsApp dan penanganan order yang cepat</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Guest registration CTA --}}
    @guest
        <x-storefront.cta
            theme="yellow"
            align="left"
            mascot
            title="Siap Bergabung?"
            description="Daftarkan usahamu sekarang dan dapatkan akses ke harga grosir dan partai eksklusif. Proses verifikasi cepat dan mudah."
            primary-label="Daftar Sekarang"
            :primary-href="route('register')"
            secondary-label="Sudah punya akun? Masuk"
            :secondary-href="route('login')"
        />
    @endguest
</div>
