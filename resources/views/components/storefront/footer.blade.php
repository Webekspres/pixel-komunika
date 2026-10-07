@props([
    'categories' => collect(),
])

<footer class="w-full bg-brand-black text-white">
    <div class="container-2xl py-12">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <a href="{{ route('home') }}" wire:navigate class="mb-4 inline-flex min-h-11 items-center" aria-label="Pixel Komunika beranda">
                    <img
                        src="{{ asset('assets/brand-logo.png') }}"
                        alt="Pixel Komunika"
                        class="h-9 w-auto brightness-0 invert"
                        width="140"
                        height="36"
                        loading="lazy"
                    >
                </a>
                <p class="max-w-sm text-sm leading-relaxed text-zinc-300">
                    Toko online grosir Pixel Komunika untuk reseller terverifikasi: aksesoris elektronik, kartu data, voucher, dan pulsa.
                </p>
            </div>

            <div>
                <h2 class="mb-2 text-sm font-bold">Kategori Produk</h2>
                <ul>
                    @forelse ($categories as $category)
                        <li>
                            <a
                                href="{{ route('products.index', ['kategori' => $category->id]) }}"
                                wire:navigate
                                class="inline-flex min-h-11 min-w-11 items-center text-sm text-zinc-300 transition-colors hover:text-brand-yellow"
                            >
                                {{ $category->name }}
                            </a>
                        </li>
                    @empty
                        <li>
                            <a href="{{ route('products.index') }}" wire:navigate class="inline-flex min-h-11 min-w-11 items-center text-sm text-zinc-300 transition-colors hover:text-brand-yellow">
                                Lihat katalog
                            </a>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h2 class="mb-2 text-sm font-bold">Kontak</h2>
                <ul>
                    <li>
                        <a href="https://wa.me/6281546407702" class="inline-flex min-h-11 items-center gap-2.5 text-sm text-zinc-300 transition-colors hover:text-brand-yellow" target="_blank" rel="noopener">
                            <x-icon name="message-circle" class="size-4 shrink-0 text-brand-yellow" />
                            WhatsApp 0815-4640-7702
                        </a>
                    </li>
                    <li>
                        <a href="mailto:info@pixelkomunika.com" class="inline-flex min-h-11 items-center gap-2.5 text-sm text-zinc-300 transition-colors hover:text-brand-yellow">
                            <x-icon name="mail" class="size-4 shrink-0 text-brand-yellow" />
                            info@pixelkomunika.com
                        </a>
                    </li>
                    <li class="flex items-start gap-2.5 py-3">
                        <x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-brand-yellow" />
                        <span class="text-sm text-zinc-300">Jl. Sawahkurung IV No. 18B, Bandung, Jawa Barat</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-2xl flex flex-col items-center justify-between gap-2 py-4 sm:flex-row">
            <p class="text-xs text-zinc-400">&copy; {{ date('Y') }} Pixel Komunika. Dikembangkan oleh PT Webekspres Teknologi Indonesia.</p>
            <p class="text-xs text-zinc-400">NPWP: 0821.4146.0442.4000</p>
        </div>
    </div>
</footer>
