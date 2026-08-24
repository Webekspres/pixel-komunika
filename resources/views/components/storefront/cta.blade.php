@props([
    'primaryHref',
    'secondaryHref' => null,
])

{{-- Width matches Produk Pilihan / Mengapa Pixel Komunika (`container-lg` = 1440px).
     Bottom margin = why-us pb (py-12/16) + this mt, so footer gap matches the gap above. --}}
<section {{ $attributes->class(['container-lg mt-16 mb-28 sm:mt-20 sm:mb-36']) }}>
    <div class="relative flex min-h-72 w-full items-center overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900 p-8 text-white shadow-xl sm:min-h-80 sm:p-12 lg:min-h-96 lg:p-14">
        <div
            class="pointer-events-none absolute inset-0 bg-linear-to-br from-zinc-900 via-zinc-900 to-amber-950/40"
            aria-hidden="true"
        ></div>

        <div class="relative">
            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                Siap Kembangkan Usaha Anda Bersama Pixel Komunika?
            </h2>

            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-zinc-400 sm:text-base">
                Daftarkan toko atau konter Anda untuk mendapatkan akses katalog harga grosir, kemudahan transaksi B2B, dan pengiriman prioritas.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a
                    href="{{ $primaryHref }}"
                    wire:navigate
                    class="inline-flex items-center justify-center rounded-xl bg-amber-400 px-6 py-3 text-sm font-bold text-zinc-950 shadow-md transition hover:bg-amber-500"
                >
                    Daftar Mitra B2B
                </a>

                @if ($secondaryHref)
                    <a
                        href="{{ $secondaryHref }}"
                        wire:navigate
                        class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 px-6 py-3 text-sm font-medium text-zinc-200 transition hover:bg-zinc-700"
                    >
                        Masuk ke Akun
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
