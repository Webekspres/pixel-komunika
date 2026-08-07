<x-layout.app-page>
    <x-ui.page-header
        eyebrow="Pesanan #{{ $order->order_number }}"
        title="Detail Pesanan & Invoice"
        description="Rincian lengkap pesanan, alamat pengiriman, ekspedisi, serta status pembayaran invoice."
    />

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <!-- Summary Info Card -->
            <x-ui.section-card title="Informasi Pesanan">
                <div class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <p class="text-zinc-500">Nomor Order:</p>
                        <p class="font-bold text-zinc-900">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">Tanggal Transaksi:</p>
                        <p class="font-semibold text-zinc-900">{{ $order->created_at->format('d M Y, H:i WIB') }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">Status Pesanan:</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold
                            {{ $order->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}
                        ">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-zinc-500">Batas Waktu Pembayaran:</p>
                        <p class="font-semibold text-red-600">{{ $order->expires_at ? $order->expires_at->format('d M Y, H:i WIB') : '-' }}</p>
                    </div>
                </div>
            </x-ui.section-card>

            <!-- Shipping Address -->
            <x-ui.section-card title="Tujuan Pengiriman">
                <div class="text-sm space-y-1">
                    <p class="font-bold text-zinc-900">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                    <p class="text-zinc-700">{{ $order->shipping_address_line }}</p>
                    <p class="text-zinc-600">{{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                    <div class="pt-2">
                        <span class="inline-block px-3 py-1 bg-zinc-100 text-zinc-800 font-semibold text-xs rounded-lg">
                            Ekspedisi: {{ strtoupper($order->courier_code) }} - {{ $order->courier_service }}
                        </span>
                    </div>
                </div>
            </x-ui.section-card>

            <!-- Order Items -->
            <x-ui.section-card title="Item Produk">
                <div class="divide-y divide-zinc-200">
                    @foreach ($order->items as $item)
                        <div class="py-3 flex justify-between items-center text-sm">
                            <div>
                                <p class="font-semibold text-zinc-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-zinc-500">SKU: {{ $item->sku }} | {{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-bold text-zinc-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.section-card>
        </div>

        <!-- Invoice & Payment Proof Side -->
        <div class="space-y-4">
            <x-ui.section-card title="Invoice Snapshot">
                @if ($order->invoice)
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-zinc-500">Nomor Invoice:</p>
                            <p class="font-bold text-zinc-900 text-base">{{ $order->invoice->invoice_number }}</p>
                        </div>

                        <div class="border-t border-zinc-200 pt-3 space-y-2">
                            <div class="flex justify-between text-zinc-600">
                                <span>Subtotal</span>
                                <span class="font-semibold text-zinc-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-zinc-600">
                                <span>PPh 22</span>
                                <span class="font-semibold text-zinc-900">Rp {{ number_format($order->tax_pph22, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-zinc-600">
                                <span>Ongkos Kirim</span>
                                <span class="font-semibold text-zinc-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-zinc-200 pt-2 flex justify-between text-base font-bold text-zinc-900">
                                <span>Total Invoice</span>
                                <span class="text-amber-700">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-200 text-center">
                            <span class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider block bg-amber-50 text-amber-800 border border-amber-200">
                                Status Invoice: {{ $order->invoice->status }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-zinc-500">Invoice belum diterbitkan.</p>
                @endif
            </x-ui.section-card>

            <!-- Payment Upload / Proof Section -->
            @if ($order->status === 'unpaid' || $order->status === 'payment_pending')
                <x-ui.section-card title="Upload Bukti Pembayaran">
                    @if (session()->has('success'))
                        <div class="p-3 bg-emerald-50 text-emerald-800 text-xs rounded-xl font-medium mb-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="p-3 bg-red-50 text-red-800 text-xs rounded-xl font-medium mb-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="uploadPaymentProof" class="space-y-4">
                        <flux:input label="Nama Bank Transfer" wire:model="bank_name" placeholder="contoh: BCA / Mandiri / BNI" required />
                        <flux:input label="Nama Pemilik Rekening" wire:model="account_name" placeholder="Nama pengirim" required />
                        <flux:input label="Jumlah Transfer (Rp)" wire:model="amount" type="number" required />
                        <div>
                            <flux:label>File Bukti Pembayaran (Gambar / PDF max 5MB)</flux:label>
                            <input type="file" wire:model="proof_file" class="mt-1 block w-full text-xs text-zinc-600 border border-zinc-300 rounded-lg p-2" required />
                            @error('proof_file') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <flux:button type="submit" variant="primary" color="amber" class="w-full">
                            Kirim Bukti Pembayaran
                        </flux:button>
                    </form>

                    <div class="pt-4 border-t border-zinc-200">
                        <flux:button wire:click="cancelOrder" wire:confirm="Yakin ingin membatalkan pesanan ini?" variant="ghost" class="w-full text-red-600">
                            Batalkan Pesanan Ini
                        </flux:button>
                    </div>
                </x-ui.section-card>
            @endif

            @if ($order->paymentProofs->isNotEmpty())
                <x-ui.section-card title="Riwayat Upload Bukti">
                    <div class="space-y-3">
                        @foreach ($order->paymentProofs as $proof)
                            <div class="p-3 rounded-xl bg-zinc-50 border border-zinc-200 text-xs space-y-1">
                                <div class="flex justify-between font-bold text-zinc-900">
                                    <span>{{ $proof->bank_name }} - {{ $proof->account_name }}</span>
                                    <span class="uppercase {{ $proof->status === 'approved' ? 'text-emerald-700' : ($proof->status === 'rejected' ? 'text-red-600' : 'text-amber-700') }}">
                                        {{ $proof->status }}
                                    </span>
                                </div>
                                <p class="text-zinc-600">Total: Rp {{ number_format($proof->amount, 0, ',', '.') }}</p>
                                <p class="text-zinc-400 text-[10px]">{{ $proof->created_at->format('d M Y, H:i WIB') }}</p>
                                @if ($proof->status === 'rejected' && $proof->rejection_reason)
                                    <div class="p-2 bg-red-50 text-red-800 rounded mt-1">
                                        Catatan Penolakan: {{ $proof->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-ui.section-card>
            @endif
        </div>
    </div>
</x-layout.app-page>
