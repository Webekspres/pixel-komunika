<x-ui.confirm-dialog
    :name="'remove-cart-item-'.$item['id'].'-'.$context"
    title="Hapus dari keranjang"
    :description="$item['product']->name.' akan dihapus dari keranjang. Tindakan ini tidak dapat dibatalkan.'"
>
    <x-slot:trigger>
        <button
            type="button"
            class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg text-zinc-600 transition hover:bg-red-50 hover:text-red-700"
            aria-label="Hapus {{ $item['product']->name }} dari keranjang"
        >
            <x-icon name="trash-2" class="size-4" />
        </button>
    </x-slot:trigger>
    <x-slot:confirm>
        <flux:modal.close>
            <flux:button variant="danger" wire:click="removeItem({{ $item['id'] }})">Ya, hapus</flux:button>
        </flux:modal.close>
    </x-slot:confirm>
</x-ui.confirm-dialog>
