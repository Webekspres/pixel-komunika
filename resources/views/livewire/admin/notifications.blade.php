<div wire:poll.30s class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
    <button
        type="button"
        @click="open = !open"
        class="relative inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100"
        aria-label="Notifikasi"
    >
        <x-icon name="bell" class="size-4" />
        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex min-w-4 items-center justify-center rounded-full bg-brand-red px-1 py-0.5 text-[10px] leading-none font-bold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        @click.outside="open = false"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-lg"
        role="menu"
        aria-label="Daftar notifikasi"
    >
        <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3">
            <p class="text-sm font-bold text-zinc-900">Notifikasi</p>
            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllRead" class="text-xs font-semibold text-brand-yellow-dark hover:underline">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse ($notifications as $notification)
                @php
                    $isOrder = $notification->type === \App\Models\AppNotification::TYPE_NEW_ORDER;
                    $customer = data_get($notification->data, 'customer');
                    $total = data_get($notification->data, 'grand_total');
                @endphp
                <div
                    wire:key="notif-{{ $notification->id }}"
                    class="border-b border-zinc-50 transition-colors {{ $notification->read_at ? 'bg-white' : 'bg-amber-50/40' }} hover:bg-zinc-50"
                >
                    <button
                        type="button"
                        wire:click="openNotification({{ $notification->id }})"
                        class="flex w-full items-start gap-3 px-4 py-3 text-left"
                        role="menuitem"
                    >
                        <span class="mt-0.5 inline-flex size-8 shrink-0 items-center justify-center rounded-full {{ $notification->read_at ? 'bg-zinc-100' : 'bg-brand-yellow/20' }}">
                            <x-icon :name="$isOrder ? 'shopping-bag' : 'bell'" class="size-4 {{ $notification->read_at ? 'text-zinc-400' : 'text-brand-yellow-dark' }}" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-zinc-800">
                                {{ data_get($notification->data, 'message', 'Notifikasi baru') }}
                            </span>
                            <span class="mt-0.5 block truncate text-xs text-zinc-500">
                                {{ data_get($notification->data, 'order_number', '') }}
                                @if ($customer)
                                    · {{ $customer }}
                                @endif
                                @if ($total)
                                    · Rp {{ number_format((float) $total, 0, ',', '.') }}
                                @endif
                            </span>
                            <span class="mt-1 block text-[11px] text-zinc-400">
                                {{ $notification->created_at->timezone('Asia/Jakarta')->diffForHumans() }}
                            </span>
                        </span>
                        @unless ($notification->read_at)
                            <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-red" aria-hidden="true"></span>
                        @endunless
                    </button>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <x-icon name="bell-off" class="mx-auto mb-2 size-6 text-zinc-300" />
                    <p class="text-xs text-zinc-400">Belum ada notifikasi.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
