<x-layout.admin-page
    title="Manajemen Pesanan"
    description="Kelola seluruh pesanan pelanggan, ubah status transaksi, dan pantau status invoice."
>
    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Order di halaman ini" :value="(string) $orders->count()" icon="clipboard-list" variant="admin" />
        <x-ui.stat-card label="Filter status" :value="$statusFilter === 'all' ? 'Semua' : strtoupper($statusFilter)" icon="funnel" variant="admin" />
        <x-ui.stat-card label="Keyword" :value="$search !== '' ? $search : '-'" description="Cari nomor order, penerima, atau nomor HP." icon="search" variant="admin" />
    </div>

    @if (session()->has('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <x-ui.filter-bar>
        <div class="flex w-full flex-col items-center justify-between gap-4 sm:flex-row">
            <div class="w-full sm:w-1/3">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari No. Order / Penerima / HP..."
                    icon="magnifying-glass"
                />
            </div>
            <div class="flex gap-2">
                <flux:select wire:model.live="statusFilter">
                    <option value="all">Semua Status</option>
                    <option value="unpaid">UNPAID</option>
                    <option value="paid">PAID</option>
                    <option value="processing">PROCESSING</option>
                    <option value="shipped">SHIPPED</option>
                    <option value="completed">COMPLETED</option>
                    <option value="cancelled">CANCELLED</option>
                </flux:select>
            </div>
        </div>
    </x-ui.filter-bar>

    <x-ui.section-card title="Daftar pesanan" description="Workspace order ini dipakai untuk review transaksi, verifikasi pembayaran, dan tindak lanjut pengiriman." variant="admin">
        @if ($orders->isEmpty())
            <x-ui.empty-state
                title="Tidak Ada Pesanan"
                description="Belum ada data pesanan yang sesuai dengan filter."
                icon="inbox"
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-700">
                    <thead class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">No. Order / Tanggal</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Total Tagihan</th>
                            <th class="px-4 py-3">Status Order</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach ($orders as $order)
                            <tr class="transition-colors hover:bg-zinc-50/80">
                                <td class="px-4 py-3">
                                    <p class="font-bold text-zinc-900">{{ $order->order_number }}</p>
                                    <p class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-zinc-900">{{ $order->recipient_name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $order->recipient_phone }}</p>
                                </td>
                                <td class="px-4 py-3 font-bold text-amber-800">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold
                                        {{ $order->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $order->status === 'payment_pending' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ strtoupper($order->status) }}
                                    </span>

                                    @if ($order->latestPaymentProof)
                                        <div class="mt-1">
                                            <span class="rounded border border-purple-200 bg-purple-50 px-2 py-0.5 text-[10px] font-semibold text-purple-700">
                                                Bukti: {{ $order->latestPaymentProof->bank_name }} ({{ strtoupper($order->latestPaymentProof->status) }})
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
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

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </x-ui.section-card>
</x-layout.admin-page>
