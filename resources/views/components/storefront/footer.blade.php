@props([
    'categories' => collect(),
])

{{-- Brand assets: brand-logo.png (Laravel) — not Figma text-mark --}}
<footer class="w-full bg-brand-black text-white">
    <div class="container-2xl py-12">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" wire:navigate class="mb-4 inline-block" aria-label="Pixel Komunika beranda">
                    <img
                        src="{{ asset('assets/brand-logo.png') }}"
                        alt="Pixel Komunika"
                        class="h-9 w-auto brightness-0 invert"
                        width="140"
                        height="36"
                        loading="lazy"
                    >
                </a>
                <p class="mb-4 text-sm leading-relaxed text-zinc-400">
                    Platform B2B ecommerce untuk pelanggan terverifikasi. Dapatkan harga terbaik untuk elektronik, aksesoris, dan produk telekomunikasi.
                </p>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold">Kategori Produk</h4>
                <ul class="space-y-2">
                    @forelse ($categories as $category)
                        <li>
                            <a
                                href="{{ route('products.index', ['kategori' => $category->id]) }}"
                                wire:navigate
                                class="text-sm text-zinc-400 transition-colors hover:text-brand-yellow"
                            >
                                {{ $category->name }}
                            </a>
                        </li>
                    @empty
                        <li>
                            <a href="{{ route('products.index') }}" wire:navigate class="text-sm text-zinc-400 transition-colors hover:text-brand-yellow">
                                Lihat katalog
                            </a>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold">Layanan</h4>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="{{ route('products.index') }}" wire:navigate class="transition-colors hover:text-brand-yellow">Cara Berbelanja</a></li>
                    <li><span class="text-zinc-500">Kebijakan Pengiriman</span></li>
                    <li><span class="text-zinc-500">Syarat &amp; Ketentuan</span></li>
                    <li><span class="text-zinc-500">Kebijakan Privasi</span></li>
                    <li>
                        <a href="https://wa.me/6281546407702" class="transition-colors hover:text-brand-yellow" target="_blank" rel="noopener">
                            Hubungi Kami
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold">Kontak</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5">
                        <x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-brand-yellow" />
                        <span class="text-sm text-zinc-400">Jl. Sawahkurung IV No. 18B, Bandung, Jawa Barat</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <x-icon name="phone" class="size-4 shrink-0 text-brand-yellow" />
                        <a href="https://wa.me/6281546407702" class="text-sm text-zinc-400 transition-colors hover:text-brand-yellow" target="_blank" rel="noopener">0815-4640-7702</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <x-icon name="mail" class="size-4 shrink-0 text-brand-yellow" />
                        <a href="mailto:info@pixelkomunika.com" class="text-sm text-zinc-400 transition-colors hover:text-brand-yellow">info@pixelkomunika.com</a>
                    </li>
                </ul>
                <div class="mt-4 rounded-xl bg-white/5 p-3">
                    <p class="mb-1 text-xs font-semibold text-zinc-300">Transfer ke:</p>
                    <p class="text-xs text-zinc-400">BCA / Mandiri a.n. Pixel Komunika</p>
                    <p class="mt-1 text-xs text-zinc-500">Detail rekening dikirim setelah order dikonfirmasi.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-2xl flex flex-col items-center justify-between gap-2 py-4 sm:flex-row">
            <p class="text-xs text-zinc-500">&copy; {{ date('Y') }} Pixel Komunika. Dikembangkan oleh PT Webekspres Teknologi Indonesia.</p>
            <p class="text-xs text-zinc-500">NPWP: 0821.4146.0442.4000</p>
        </div>
    </div>
</footer>
