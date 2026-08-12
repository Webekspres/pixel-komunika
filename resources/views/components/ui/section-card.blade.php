@props([
    'title' => null,
    'description' => null,
    'variant' => 'storefront',
])

@php
    $isAdmin = $variant === 'admin';
    $wrapperClass = $isAdmin
        ? 'space-y-5 rounded-2xl border border-zinc-200 bg-white p-5 shadow-card sm:p-6'
        : 'storefront-panel space-y-5 p-5 sm:p-6 lg:p-7';
    $titleClass = $isAdmin
        ? 'text-lg font-semibold tracking-tight text-zinc-950 sm:text-xl'
        : 'text-lg font-bold tracking-tight text-brand-black sm:text-xl';
    $descriptionClass = $isAdmin
        ? 'mt-2 text-sm leading-relaxed text-zinc-500'
        : 'mt-2 text-sm leading-relaxed text-brand-black/60';
@endphp

<section {{ $attributes->class($wrapperClass) }}>
    @if ($title || $description || isset($actions))
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                @if ($title)
                    <h2 class="{{ $titleClass }}">{{ $title }}</h2>
                @endif

                @if ($description)
                    <p class="{{ $descriptionClass }}">{{ $description }}</p>
                @endif
            </div>

            @if (isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</section>
