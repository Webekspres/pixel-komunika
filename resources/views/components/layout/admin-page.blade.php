@props([
    'title',
    'description' => null,
])

<x-layout.app-page>
    <x-ui.page-header eyebrow="Admin" :title="$title" :description="$description" variant="admin">
        @if (isset($actions))
            <x-slot name="actions">
                {{ $actions }}
            </x-slot>
        @endif
    </x-ui.page-header>

    {{ $slot }}
</x-layout.app-page>
