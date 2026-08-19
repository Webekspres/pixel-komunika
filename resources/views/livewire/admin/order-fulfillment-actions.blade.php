<div class="space-y-4">
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

    <x-ui.section-card title="Aksi Admin" description="Tindak lanjut pesanan sesuai status terkini." variant="admin">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.status-badge :status="$order->status" />

            @if ($order->shipment && $order->shipment->isHeld())
                <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-600">
                    <x-icon name="triangle-alert" class="size-3" />
                    Terkendala pengiriman
                </span>
            @endif

            @if ($order->shipment?->tracking_number)
                <span class="inline-flex items-center rounded-full border border-zinc-200 bg-zinc-50 px-2.5 py-1 text-[11px] font-semibold text-zinc-700">
                    Resi: {{ $order->shipment->tracking_number }}
                </span>
            @endif
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @if ($order->latestPaymentProof && $order->latestPaymentProof->status === 'pending')
                <flux:button variant="primary" color="emerald" wire:click="approvePayment">
                    Verifikasi Pembayaran
                </flux:button>
                <flux:button variant="ghost" class="text-red-600!" wire:click="openRejectModal">
                    Tolak Bukti
                </flux:button>
            @endif

            @if ($order->status === 'paid')
                <flux:button variant="primary" color="indigo" wire:click="transitionStatus('processing')">
                    Mulai Proses
                </flux:button>
            @endif

            @if ($order->status === 'processing')
                <flux:button variant="primary" color="sky" wire:click="transitionStatus('packed')">
                    Tandai Dikemas
                </flux:button>
            @endif

            @if ($order->status === 'packed')
                <flux:button variant="primary" color="blue" wire:click="openShipModal">
                    Kirim Pesanan
                </flux:button>
            @endif

            @if ($order->status === 'shipped' && ! ($order->shipment && $order->shipment->isHeld()))
                <flux:button variant="primary" color="emerald" wire:click="transitionStatus('completed')" wire:confirm="Yakin menandai pesanan ini selesai?">
                    Tandai Selesai
                </flux:button>
                <flux:button variant="danger" wire:click="openTerkendalaModal">
                    Tandai Terkendala
                </flux:button>
            @endif

            @if ($order->shipment && $order->shipment->isHeld())
                <flux:button variant="ghost" class="text-emerald-700!" wire:click="resolveTerkendala" wire:confirm="Kendala sudah teratasi dan pengiriman dilanjutkan?">
                    Tandai Teratasi
                </flux:button>
            @endif

            @if ($this->canCancelToday())
                <flux:button variant="danger" wire:click="openCancelModal">
                    Batalkan Pesanan
                </flux:button>
            @endif
        </div>
    </x-ui.section-card>

    @if ($showRejectModal)
        <flux:modal wire:model.self="showRejectModal" name="reject-modal" class="md:w-96" @close="resetRejectModal">
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Tolak Bukti Pembayaran</flux:heading>
                    <flux:subheading class="mt-2">Bukti pembayaran akan ditolak dan pelanggan dapat mengunggah ulang.</flux:subheading>
                </div>

                <flux:textarea
                    wire:model="rejectionReason"
                    label="Alasan Penolakan"
                    placeholder="Jelaskan alasan penolakan (min. 5 karakter)"
                    rows="3"
                />
                @error('rejectionReason')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Kembali</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" wire:click="confirmRejectPayment">Tolak Bukti</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    @if ($showShipModal)
        <flux:modal wire:model.self="showShipModal" name="ship-modal" class="md:w-96" @close="resetShipModal">
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Kirim Pesanan</flux:heading>
                    <flux:subheading class="mt-2">Order akan ditandai Dikirim. Nomor resi hanya wajib untuk kurir yang memilikinya.</flux:subheading>
                </div>

                <flux:input
                    wire:model="trackingNumber"
                    label="Nomor Resi"
                    placeholder="Contoh: JP0001234567 (opsional)"
                />
                @error('trackingNumber')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Kembali</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" color="blue" wire:click="confirmShip">Kirim Pesanan</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    @if ($showCancelModal)
        <flux:modal wire:model.self="showCancelModal" name="cancel-modal" class="md:w-96" @close="resetCancelModal">
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Batalkan Pesanan {{ $order->order_number }}</flux:heading>
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
                    <flux:heading size="lg">Tandai Terkendala {{ $order->order_number }}</flux:heading>
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
