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

    @if (session()->has('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
            {{ session('error') }}
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
                <option value="packed">Dikemas</option>
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
            <flux:table :paginate="$orders" container:class="[&_ui-table-scroll-area]:max-h-[70vh]">
                <flux:table.columns>
                    <flux:table.column align="center" class="w-12">No</flux:table.column>
                    <flux:table.column>No. Order</flux:table.column>
                    <flux:table.column>Pelanggan</flux:table.column>
                    <flux:table.column align="end" class="w-36">Total</flux:table.column>
                    <flux:table.column class="w-48">Status</flux:table.column>
                    <flux:table.column align="end" class="w-28">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($orders as $order)
                        <flux:table.row
                            :key="$order->id"
                            class="cursor-pointer transition-colors hover:bg-neutral-50"
                            @click="window.location.href = '{{ route('admin.orders.show', $order) }}'"
                        >
                            <flux:table.cell align="center" class="w-12 text-xs text-zinc-400">{{ $orders->firstItem() + $loop->index }}</flux:table.cell>
                            <flux:table.cell>
                                <p class="font-bold text-zinc-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-zinc-500">{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                            </flux:table.cell>
                            <flux:table.cell>
                                <p class="font-medium text-zinc-900">{{ $order->recipient_name }}</p>
                                <p class="text-xs text-zinc-500">{{ $order->recipient_phone }}</p>
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-bold text-zinc-900">
                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="space-y-1">
                                    <x-ui.status-badge :status="$order->status" />
                                    @if ($order->latestPaymentProof)
                                        <span class="inline-flex items-center rounded border px-1.5 py-0.5 text-[11px] font-semibold
                                            {{ $order->latestPaymentProof->status === 'approved' ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                : ($order->latestPaymentProof->status === 'rejected' ? 'border-rose-200 bg-rose-50 text-rose-600'
                                                : 'border-amber-200 bg-amber-50 text-amber-800') }}">
                                            Bukti bayar: {{ $order->latestPaymentProof->bank_name }} ({{ strtoupper($order->latestPaymentProof->status) }})
                                        </span>
                                    @endif
                                    @if ($order->shipment && $order->shipment->issue_status === \App\Models\Shipment::ISSUE_TERKENDALA)
                                        <span class="inline-flex items-center rounded border border-rose-200 bg-rose-50 px-1.5 py-0.5 text-[11px] font-semibold text-rose-600">
                                            Terkendala pengiriman
                                        </span>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="end" class="w-28" @click.stop>
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('admin.orders.show', $order) }}" variant="ghost" size="sm">
                                        Detail
                                    </flux:button>

                                    <flux:dropdown align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" aria-label="Aksi lainnya" />
                                        <flux:menu>
                                            <flux:menu.item icon="eye" href="{{ route('admin.orders.show', $order) }}">Lihat Detail Pesanan</flux:menu.item>
                                            <flux:menu.item icon="document-arrow-down" href="{{ route('orders.invoice.download', $order) }}">Unduh Invoice (PDF)</flux:menu.item>

                                            @if ($this->canCancelToday($order))
                                                <flux:menu.separator />
                                                <flux:menu.item icon="x-mark" variant="danger" wire:click="openCancelModal({{ $order->id }})">Batalkan Pesanan</flux:menu.item>
                                            @endif

                                            @if ($order->status === 'shipped')
                                                <flux:menu.item icon="triangle-alert" wire:click="openTerkendalaModal({{ $order->id }})">Tandai Terkendala</flux:menu.item>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    @if ($showCancelModal)
        <flux:modal wire:model.self="showCancelModal" name="cancel-modal" class="md:w-96" @close="resetCancelModal">
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Batalkan Pesanan {{ $cancelOrder?->order_number }}</flux:heading>
                    <flux:subheading class="mt-2">Pembatalan mengembalikan stok produk, menonaktifkan invoice, dan mencatat laporan retur POS. Tindakan ini tidak dapat dibatalkan.</flux:subheading>
                </div>

                <flux:textarea
                    wire:model="cancelReason"
                    label="Alasan Pembatalan"
                    placeholder="Tuliskan alasan pembatalan (min. 5 karakter)"
                    rows="3"
                />
                @error('cancelReason')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Kembali</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" wire:click="confirmCancelOrder">Konfirmasi Batalkan</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    @if ($showTerkendalaModal)
        <flux:modal wire:model.self="showTerkendalaModal" name="terkendala-modal" class="md:w-96" @close="resetTerkendalaModal">
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Tandai Terkendala {{ $terkendalaOrder?->order_number }}</flux:heading>
                    <flux:subheading class="mt-2">Pengiriman menuju pelanggan mengalami kendala sehingga tidak dapat diselesaikan otomatis.</flux:subheading>
                </div>

                <flux:textarea
                    wire:model="terkendalaReason"
                    label="Kendala Pengiriman"
                    placeholder="Deskripsikan kendala (min. 5 karakter)"
                    rows="3"
                />
                @error('terkendalaReason')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Kembali</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" wire:click="confirmTerkendala">Tandai Terkendala</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
