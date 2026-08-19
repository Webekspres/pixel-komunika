@props([
    'name' => null,
    'title',
    'description' => null,
    'icon' => 'triangle-alert',
    'confirmLabel' => 'Konfirmasi',
    'cancelLabel' => 'Batal',
    'confirmVariant' => 'danger',
    'action' => null,
    'method' => 'POST',
])

@php
    $name = $name ?? 'confirm-' . str()->uuid();
    $danger = $confirmVariant === 'danger';
    $iconClass = $danger ? 'bg-red-50 text-red-600' : 'bg-brand-yellow/20 text-brand-yellow-dark';
@endphp

<flux:modal.trigger :name="$name">
    {{ $trigger }}
</flux:modal.trigger>

<flux:modal :name="$name">
    <div class="space-y-5">
        <div class="flex items-start gap-4">
            <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-2xl {{ $iconClass }}">
                <x-icon :name="$icon" class="size-5" />
            </div>
            <div class="min-w-0 flex-1">
                <flux:heading size="lg">{{ $title }}</flux:heading>
                @if ($description)
                    <flux:subheading>{{ $description }}</flux:subheading>
                @endif
            </div>
        </div>

        @if ($action)
            <form method="POST" action="{{ $action }}">
                @csrf
                @method($method)

                {{ $slot }}

                <div class="mt-5 flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ $cancelLabel }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="{{ $confirmVariant }}">
                        {{ $confirmLabel }}
                    </flux:button>
                </div>
            </form>
        @else
            {{ $slot }}

            <div class="mt-5 flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ $cancelLabel }}</flux:button>
                </flux:modal.close>
                @if (isset($confirm))
                    {{ $confirm }}
                @else
                    <flux:button variant="{{ $confirmVariant }}">{{ $confirmLabel }}</flux:button>
                @endif
            </div>
        @endif
    </div>
</flux:modal>