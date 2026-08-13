<button
    type="button"
    @click="$dispatch('open-cart-drawer')"
    class="relative inline-flex size-10 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-50 hover:text-brand-yellow"
    aria-label="Keranjang belanja"
    wire:key="cart-badge-{{ $cartCount }}"
>
    <x-icon name="shopping-cart" class="size-5" />
    @if ($cartCount > 0)
        <span class="absolute -top-0.5 -right-0.5 inline-flex size-5 items-center justify-center rounded-full bg-brand-red text-[10px] font-bold text-white">
            {{ $cartCount > 9 ? '9+' : $cartCount }}
        </span>
    @endif
</button>
