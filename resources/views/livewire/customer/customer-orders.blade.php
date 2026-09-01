<div class="space-y-6">
    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1" aria-label="Filter status pesanan">
        @php
            $tabs = [
                'all' => ['label' => 'Semua', 'count' => $counts['all']],
                'unpaid' => ['label' => 'Menunggu Pembayaran', 'count' => $counts['unpaid']],
                'processing' => ['label' => 'Diproses', 'count' => $counts['processing']],
                'shipped' => ['label' => 'Dikirim', 'count' => $counts['shipped']],
                'completed' => ['label' => 'Selesai', 'count' => $counts['completed']],
                'cancelled' => ['label' => 'Dibatalkan', 'count' => $counts['cancelled']],
            ];
        @endphp

        @foreach ($tabs as $key => $tab)
            <button
                type="button"
                wire:click="setFilter('{{ $key }}')"
                class="inline-flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold whitespace-nowrap transition-all {{ $status === $key ? 'bg-zinc-900 text-white shadow-2xs' : 'bg-white border border-zinc-200/80 text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900' }}"
            >
                <span>{{ $tab['label'] }}</span>
                @if ($tab['count'] > 0)
                    <span class="inline-flex items-center justify-center rounded-full px-1.5 py-0.2 text-[10px] font-bold {{ $status === $key ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-700' }}">
                        {{ $tab['count'] }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    <!-- Orders List -->
    @if ($orders->isEmpty())
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-12 text-center shadow-2xs">
            <x-icon name="shopping-bag" class="size-12 mx-auto text-zinc-300" />
            <h3 class="mt-3 text-base font-bold text-zinc-800">
                @if ($status !== 'all')
                    Tidak Ada Pesanan dengan Status Ini
                @else
                    Belum Ada Pesanan
                @endif
            </h3>
            <p class="mt-1 text-xs text-zinc-500 max-w-sm mx-auto">
                @if ($status !== 'all')
                    Tidak ditemukan riwayat pesanan dengan filter status yang dipilih.
                @else
                    Anda belum pernah membuat transaksi pesanan di Pixel Komunika.
                @endif
            </p>
            <div class="mt-5 flex items-center justify-center gap-3">
                @if ($status !== 'all')
                    <button
                        type="button"
                        wire:click="setFilter('all')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200/80 bg-zinc-50 px-4 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-100 transition"
                    >
                        <span>Tampilkan Semua Status</span>
                    </button>
                @endif
                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                >
                    <x-icon name="store" class="size-3.5" />
                    <span>Mulai Belanja</span>
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                @php
                    $isWaiting = in_array($order->status, ['unpaid', 'payment_pending', 'payment_rejected'], true);
                @endphp
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 sm:p-6 space-y-4 shadow-2xs transition-all hover:border-zinc-300">
                    <!-- Baris 1: Header Order -->
                    <div class="flex flex-col gap-2 border-b border-zinc-100 pb-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-bold text-zinc-900 text-sm sm:text-base">#{{ $order->order_number }}</span>
                            <span class="text-xs text-zinc-400">•</span>
                            <span class="text-xs text-zinc-500 font-medium">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            @if ($order->courier_code)
                                <span class="text-xs text-zinc-400">•</span>
                                <span class="text-xs font-medium text-zinc-600 uppercase">{{ $order->courier_code }} {{ $order->courier_service }}</span>
                            @endif
                        </div>
                        <div>
                            <x-ui.status-badge :status="$order->status" />
                        </div>
                    </div>

                    <!-- Baris 2: Item Preview -->
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between gap-3 text-xs sm:text-sm">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 shrink-0">
                                        <x-icon name="package" class="size-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-zinc-900 truncate">{{ $item->product_name }}</p>
                                        <p class="text-xs text-zinc-500 font-medium">{{ $item->quantity }} barang × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold text-zinc-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach

                        @if ($order->status === 'shipped' && $order->shipment && $order->shipment->tracking_number)
                            <div class="rounded-lg bg-blue-50/70 border border-blue-100 p-2.5 text-xs text-blue-900 flex items-center gap-2">
                                <x-icon name="truck" class="size-4 text-blue-600 shrink-0" />
                                <span>Nomor Resi: <strong class="font-mono font-bold">{{ $order->shipment->tracking_number }}</strong> ({{ strtoupper($order->courier_code) }})</span>
                            </div>
                        @endif
                    </div>

                    <!-- Baris 3: Footer Order -->
                    <div class="flex flex-col gap-3 border-t border-zinc-100 pt-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center text-xs sm:text-sm">
                            <span class="text-zinc-500 font-medium">Total Pembayaran:</span>
                            <span class="font-extrabold text-zinc-900 text-base sm:text-lg ml-2">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($order->invoice && in_array($order->status, ['paid', 'processing', 'packed', 'shipped', 'completed']))
                                <a
                                    href="{{ route('orders.invoice.download', $order) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition shadow-2xs"
                                >
                                    <x-icon name="download" class="size-3.5" />
                                    <span>Invoice PDF</span>
                                </a>
                            @endif

                            @if ($isWaiting)
                                <a
                                    href="{{ route('orders.show', $order) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                                >
                                    <x-icon name="upload" class="size-3.5" />
                                    <span>Unggah Bukti Bayar</span>
                                </a>
                            @else
                                <a
                                    href="{{ route('orders.show', $order) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-100 border border-zinc-200/80 px-4 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-200 transition"
                                >
                                    <span>Detail Pesanan</span>
                                    <x-icon name="chevron-right" class="size-3.5" />
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="pt-2">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
