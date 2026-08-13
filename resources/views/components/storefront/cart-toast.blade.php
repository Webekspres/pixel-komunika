{{-- Add-to-cart toasts; open drawer only via "Buka keranjang" --}}
<div
    x-data="{
        toasts: [],
        push(name) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, name: name || 'Produk' });
            setTimeout(() => this.dismiss(id), 3500);
        },
        dismiss(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
        openCart() {
            this.$dispatch('open-cart-drawer');
        },
    }"
    @cart-item-added.window="push($event.detail.name ?? $event.detail[0] ?? 'Produk')"
    class="pointer-events-none fixed bottom-4 right-4 z-[9998] flex max-w-sm flex-col gap-2"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex items-start gap-3 rounded-2xl bg-brand-black px-4 py-3 text-white shadow-lg"
        >
            <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-yellow/20 text-brand-yellow">
                <x-icon name="check" class="size-4" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold leading-snug">
                    <span class="line-clamp-2" x-text="toast.name"></span>
                    <span class="font-normal text-white/70"> berhasil ditambahkan</span>
                </p>
                <button
                    type="button"
                    @click="openCart()"
                    class="mt-1.5 text-[11px] font-bold text-brand-yellow underline-offset-2 hover:underline"
                >
                    Buka keranjang
                </button>
            </div>
            <button
                type="button"
                @click="dismiss(toast.id)"
                class="shrink-0 rounded-full p-1 text-white/50 transition hover:bg-white/10 hover:text-white"
                aria-label="Tutup notifikasi"
            >
                <x-icon name="x" class="size-3.5" />
            </button>
        </div>
    </template>
</div>
