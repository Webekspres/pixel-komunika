@props([
    'title',
    'description' => null,
    'icon'        => 'inbox',
    'mascot'      => false,
    'action'      => null,
    'actionHref'  => null,
    'actionLabel' => null,
])

<div {{ $attributes->class('flex flex-col items-center py-16 text-center') }}>
    @if ($mascot)
        <img
            src="{{ asset('assets/mascot/Maskot-base.webp') }}"
            alt="Pixel Komunika Mascot"
            class="mb-6 h-32 w-auto opacity-60"
            width="128"
            height="128"
            loading="lazy"
        >
    @else
        <div class="mb-6 inline-flex size-16 items-center justify-center rounded-2xl bg-zinc-100">
            <x-icon :name="$icon" class="size-7 text-zinc-400" />
        </div>
    @endif

    <h3 class="text-base font-semibold text-zinc-800">{{ $title }}</h3>

    @if ($description)
        <p class="mt-2 max-w-sm text-sm leading-relaxed text-zinc-500">{{ $description }}</p>
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
