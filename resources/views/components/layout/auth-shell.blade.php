@props([
    'title',
    'description' => null,
    'eyebrow' => 'Internal access',
])

<section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-14">
    <div class="hidden rounded-3xl border border-amber-200 bg-linear-to-br from-amber-50 via-white to-zinc-100 p-8 lg:flex lg:flex-col lg:justify-between">
        <div>
            <flux:badge color="amber" rounded>Pixel Komunika</flux:badge>
            <h1 class="mt-6 text-4xl font-semibold tracking-tight text-zinc-950">{{ $title }}</h1>
            @if ($description)
                <p class="mt-4 max-w-xl text-sm leading-6 text-zinc-600">{{ $description }}</p>
            @endif
        </div>

        <div class="grid gap-3">
            <div class="rounded-2xl border border-zinc-200 bg-white/80 p-4 shadow-sm">
                <p class="text-sm font-medium text-zinc-950">Verified-customer workflow</p>
                <p class="mt-1 text-sm text-zinc-600">Akses harga, checkout, dan order hanya terbuka untuk akun customer aktif.</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white/80 p-4 shadow-sm">
                <p class="text-sm font-medium text-zinc-950">Internal UI</p>
                <p class="mt-1 text-sm text-zinc-600">Shell ini dipakai untuk auth dan area operasional tanpa mengubah storefront publik.</p>
            </div>
        </div>
    </div>

    <div class="flex items-center">
        <flux:card class="w-full space-y-6">
            <div>
                <p class="text-sm font-medium text-zinc-500">{{ $eyebrow }}</p>
                <flux:heading size="xl" class="mt-2">{{ $title }}</flux:heading>
                @if ($description)
                    <flux:text class="mt-2">{{ $description }}</flux:text>
                @endif
            </div>

            {{ $slot }}
        </flux:card>
    </div>
</section>
