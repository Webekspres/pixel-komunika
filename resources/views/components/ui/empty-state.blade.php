@props([
    'title',
    'description' => null,
    'icon'        => 'inbox',
    'mascot'      => false,
    'action'      => null,
    'actionHref'  => null,
    'actionLabel' => null,
])

<div {{ $attributes->class('storefront-panel-soft flex flex-col items-center px-6 py-14 text-center sm:px-8 sm:py-16') }}>
    @if ($mascot)
        <img
            src="{{ asset('assets/mascot/Maskot-base.webp') }}"
            alt="Pixel Komunika Mascot"
            class="mb-6 h-32 w-auto opacity-80"
            width="128"
            height="128"
            loading="lazy"
        >
    @else
        <div class="mb-6 inline-flex size-16 items-center justify-center rounded-[1.4rem] bg-brand-yellow/20 text-brand-black">
            <x-icon :name="$icon" class="size-7 text-brand-black/75" />
        </div>
    @endif

    <h3 class="text-lg font-bold text-brand-black">{{ $title }}</h3>

    @if ($description)
        <p class="mt-2 max-w-md text-sm leading-relaxed text-brand-black/60">{{ $description }}</p>
    @endif

    @if (isset($action))
        <div class="mt-6">{{ $action }}</div>
    @elseif ($actionHref && $actionLabel)
        <div class="mt-6">
            <flux:button :href="$actionHref" variant="primary" color="amber" size="sm">
                {{ $actionLabel }}
            </flux:button>
        </div>
    @endif
</div>
