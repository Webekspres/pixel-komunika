@props(['heading'])

{{-- Pembungkus dokumen legal: lebar baca dibatasi agar teks panjang tetap terbaca (alasan UX untuk max-w). --}}
<div class="container-2xl py-8 sm:py-12">
    <x-storefront.breadcrumb :items="[['label' => $heading, 'href' => null]]" />

    <article class="mt-6 max-w-3xl rounded-2xl border border-zinc-200 bg-white p-6 text-sm leading-relaxed text-zinc-800 sm:p-10 sm:text-base [&_a]:font-semibold [&_a]:underline [&_a]:underline-offset-2 [&_h2]:mt-10 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-zinc-950 [&_h2:first-of-type]:mt-6 [&_li]:mt-1.5 [&_ol]:mt-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-5">
        <h1 class="text-2xl font-black text-zinc-950 sm:text-3xl">{{ $heading }}</h1>
        <p class="text-sm text-zinc-600">Berlaku sejak 7 Oktober 2026</p>

        {{ $slot }}

        <h2>Kontak</h2>
        <p>
            Pixel Komunika, Jl. Sawahkurung IV No. 18B, Bandung, Jawa Barat. NPWP 0821.4146.0442.4000.<br>
            WhatsApp <a href="https://wa.me/6281546407702" target="_blank" rel="noopener">0815-4640-7702</a>,
            email <a href="mailto:info@pixelkomunika.com">info@pixelkomunika.com</a>.
        </p>
    </article>
</div>
