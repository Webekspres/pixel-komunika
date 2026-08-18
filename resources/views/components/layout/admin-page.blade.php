@props([
    'title',
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Kembali',
])

<x-layout.app-page>
    <x-ui.page-header
        eyebrow="Admin"
        :title="$title"
        :description="$description"
        variant="admin"
        :back-href="$backHref"
        :back-label="$backLabel"
    >
        @if (isset($actions))
            <x-slot name="actions">
                {{ $actions }}
            </x-slot>
        @endif
    </x-ui.page-header>

    {{ $slot }}
</x-layout.app-page>
