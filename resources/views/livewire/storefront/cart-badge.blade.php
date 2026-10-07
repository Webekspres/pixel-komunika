<button
    type="button"
    @click="$dispatch('open-cart-drawer')"
    class="relative inline-flex size-11 items-center justify-center rounded-xl text-zinc-700 transition hover:bg-zinc-100 hover:text-brand-black"
    aria-label="Keranjang belanja, {{ $cartCount }} produk"
    wire:key="cart-badge-{{ $cartCount }}"
>
    <x-icon name="shopping-cart" class="size-5" />
    @if ($cartCount > 0)
        <span class="absolute -top-0.5 -right-0.5 inline-flex size-5 items-center justify-center rounded-full bg-danger text-[10px] font-bold text-white">
            {{ $cartCount > 9 ? '9+' : $cartCount }}
        </span>
    @endif
</button>
