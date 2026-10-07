@props([
    'primaryHref',
    'secondaryHref' => null,
])

<section {{ $attributes->class(['container-lg my-12 sm:my-16']) }}>
    <div class="flex w-full items-center rounded-2xl bg-brand-black p-8 text-white sm:p-12 lg:p-14">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                Belum punya akun reseller?
            </h2>

            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-zinc-300 sm:text-base">
                Daftarkan toko atau konter Anda. Setelah admin memverifikasi akun, harga partai dan grosir serta checkout langsung terbuka.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a
                    href="{{ $primaryHref }}"
                    wire:navigate
                    class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-yellow px-6 py-3 text-sm font-bold text-brand-black transition hover:bg-brand-yellow-soft"
                >
                    Daftar Reseller
                </a>

                @if ($secondaryHref)
                    <a
                        href="{{ $secondaryHref }}"
                        wire:navigate
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-zinc-600 bg-zinc-800 px-6 py-3 text-sm font-medium text-zinc-200 transition hover:bg-zinc-700"
                    >
                        Masuk ke Akun
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
