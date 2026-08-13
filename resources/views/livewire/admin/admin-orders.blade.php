<div class="space-y-5 p-6">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Pesanan</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Kelola transaksi, verifikasi pembayaran, dan update status pengiriman.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-neutral-100 bg-white">
        <div class="flex flex-col gap-3 border-b border-neutral-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-sm">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari No. Order / Penerima / HP..."
                    class="w-full rounded-xl border border-neutral-200 py-2.5 pr-3 pl-10 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                >
            </div>
            <select
                wire:model.live="statusFilter"
                class="rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm text-zinc-700 focus:border-brand-yellow focus:outline-none"
            >
                <option value="all">Semua Status</option>
                <option value="unpaid">Menunggu Pembayaran</option>
                <option value="payment_pending">Pembayaran Diajukan</option>
                <option value="paid">Dibayar</option>
                <option value="processing">Diproses</option>
                <option value="shipped">Dikirim</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>

        @if ($orders->isEmpty())
            <div class="px-5 py-12">
                <x-ui.empty-state
                    title="Tidak Ada Pesanan"
                    description="Belum ada data pesanan yang sesuai dengan filter."
                    icon="inbox"
                />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">No. Order</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Pelanggan</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Total</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @foreach ($orders as $order)
                            <tr class="transition-colors hover:bg-neutral-50">
                                <td class="px-5 py-3.5">
                                    <p class="font-bold text-zinc-900">{{ $order->order_number }}</p>
                                    <p class="text-xs text-zinc-500">{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-zinc-900">{{ $order->recipient_name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $order->recipient_phone }}</p>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-zinc-900">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <x-ui.status-badge :status="$order->status" />
                                    @if ($order->latestPaymentProof)
                                        <div class="mt-1">
                                            <span class="rounded border border-purple-200 bg-purple-50 px-2 py-0.5 text-[10px] font-semibold text-purple-700">
                                                Bukti: {{ $order->latestPaymentProof->bank_name }} ({{ strtoupper($order->latestPaymentProof->status) }})
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-bold text-zinc-800 hover:bg-zinc-200">
                                            Detail
                                        </a>

                                        @if ($order->latestPaymentProof && $order->latestPaymentProof->status === 'pending')
                                            <button
                                                wire:click="approvePayment({{ $order->latestPaymentProof->id }})"
                                                class="rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700"
                                            >
                                                Verifikasi Bayar
                                            </button>
                                        @elseif ($order->status === 'unpaid')
                                            <button
                                                wire:click="updateOrderStatus({{ $order->id }}, 'paid')"
                                                class="rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700"
                                            >
                                                Mark Paid
                                            </button>
                                        @elseif ($order->status === 'paid')
                                            <button
                                                wire:click="updateOrderStatus({{ $order->id }}, 'shipped')"
                                                class="rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700"
                                            >
                                                Kirim
                                            </button>
                                        @endif

                                        @if (! in_array($order->status, ['cancelled', 'completed', 'returned']))
                                            <button
                                                wire:click="cancelOrder({{ $order->id }})"
                                                wire:confirm="Yakin membatalkan order ini?"
                                                class="rounded-xl bg-red-100 px-2 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200"
                                            >
                                                Batal
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-neutral-100 px-5 py-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
