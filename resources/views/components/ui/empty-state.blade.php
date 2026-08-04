@props([
    'title' => 'Belum ada data',
    'description' => null,
    'icon' => 'inbox',
])

<flux:card class="py-10 text-center">
    <div class="mx-auto flex max-w-md flex-col items-center">
        <div class="rounded-full bg-zinc-100 p-3 text-zinc-500">
            <flux:icon :name="$icon" class="size-5" />
        </div>
        <flux:heading size="lg" class="mt-4">{{ $title }}</flux:heading>
        @if ($description)
            <flux:text class="mt-2">{{ $description }}</flux:text>
        @endif

        @if (isset($actions))
            <div class="mt-5 flex flex-wrap justify-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</flux:card>
