@props([
    'icon',
    'title',
    'description',
    'iconBg' => 'bg-brand-yellow',
    'iconColor' => 'text-brand-black',
])

<div {{ $attributes->class('group') }}>
    <div class="mb-5 inline-flex size-14 items-center justify-center rounded-2xl {{ $iconBg }} transition duration-300 group-hover:scale-110">
        <x-icon :name="$icon" class="size-6 {{ $iconColor }}" />
    </div>

    <h3 class="text-lg font-bold text-brand-black">{{ $title }}</h3>

    <p class="mt-2 text-sm leading-relaxed text-brand-black/65">
        {{ $description }}
    </p>
</div>
