@props([
    'name',
    'class' => 'size-5',
])

<i
    data-lucide="{{ $name }}"
    {{ $attributes->merge(['class' => $class, 'aria-hidden' => 'true']) }}
></i>
