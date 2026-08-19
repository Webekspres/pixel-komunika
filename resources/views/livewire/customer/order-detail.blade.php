<x-layout.app-page>
    @if (session()->has('success'))
        <div class="overflow-hidden rounded-3xl border border-zinc-100 bg-white">
            <div class="relative bg-brand-yellow px-6 py-8 text-center">
                <img
                    src="{{ asset('assets/mascot/Maskot-base.webp') }}"
                    alt="Maskot Pixel Komunika"
                    class="mx-auto mb-3 h-28 w-auto drop-shadow-lg"
                    width="112"
                    height="112"
                >
                <div class="flex items-center justify-center gap-2">
                    <x-icon name="check-circle" class="size-5 text-emerald-700" />
                    <h2 class="text-xl font-black text-brand-black">Pesanan Berhasil!</h2>
                </div>
                <p class="mt-1 text-sm text-brand-black/70">{{ session('success') }}</p>
            </div>
            <div class="space-y-3 bg-amber-50 p-5 text-sm text-amber-800">
                <p class="font-bold">Langkah selanjutnya</p>
                <ol class="space-y-2 text-xs text-amber-700">
                    <li class="flex gap-2"><span class="flex size-4 shrink-0 items-center justify-center rounded-full bg-amber-200 text-[10px] font-bold text-amber-800">1</span> Transfer ke rekening yang tersedia di bawah ini</li>
                    <li class="flex gap-2"><span class="flex size-4 shrink-0 items-center justify-center rounded-full bg-amber-200 text-[10px] font-bold text-amber-800">2</span> Unggah bukti transfer di halaman ini</li>
                    <li class="flex gap-2"><span class="flex size-4 shrink-0 items-center justify-center rounded-full bg-amber-200 text-[10px] font-bold text-amber-800">3</span> Tunggu verifikasi pembayaran dari admin</li>
                </ol>
                @if ($bankAccounts->isNotEmpty())
                    <div class="space-y-2 border-t border-amber-200 pt-3">
                        <p class="text-xs font-bold text-amber-800">Rekening tujuan pembayaran</p>
                        @foreach ($bankAccounts as $account)
                            <div class="rounded-xl bg-white/80 px-3 py-2.5 text-xs text-amber-900">
                                <p class="font-bold">{{ $account->bank_name }} <span class="font-mono font-semibold text-amber-800">{{ $account->account_number }}</span></p>
                                <p class="text-amber-800/80">a.n. {{ $account->account_holder }}</p>
                                @if ($account->instructions)
                                    <p class="mt-1 text-[11px] leading-relaxed text-amber-700">{{ $account->instructions }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <x-storefront.breadcrumb :items="[
        ['label' => 'Riwayat Pesanan', 'href' => route('orders.index')],
        ['label' => $order->order_number, 'href' => null],
    ]" />

    <x-ui.page-header
        eyebrow="Pesanan #{{ $order->order_number }}"
        title="Detail Pesanan & Invoice"
        description="Rincian lengkap pesanan, alamat pengiriman, ekspedisi, serta status pembayaran invoice."
    />

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <!-- Summary Info Card -->
            <x-ui.section-card title="Informasi Pesanan">
                <div class="grid gap-4 text-sm sm:grid-cols-2">
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
                            {{ $order->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : ($order->status === 'payment_rejected' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800') }}
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
                <div class="space-y-1 text-sm">
                    <p class="font-bold text-zinc-900">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                    <p class="text-zinc-700">{{ $order->shipping_address_line }}</p>
                    <p class="text-zinc-600">{{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                    <div class="pt-2">
                        <span class="inline-block rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-800">
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
            @if (in_array($order->status, ['unpaid', 'payment_pending', 'payment_rejected'], true))
                @if ($bankAccounts->isNotEmpty())
                    <x-ui.section-card title="Rekening Tujuan Pembayaran">
                        <div class="space-y-3">
                            @foreach ($bankAccounts as $account)
                                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                                    <p class="text-sm font-bold text-zinc-900">{{ $account->bank_name }}</p>
                                    <p class="mt-0.5 font-mono text-sm font-semibold text-zinc-800">{{ $account->account_number }}</p>
                                    <p class="text-xs text-zinc-500">a.n. {{ $account->account_holder }}</p>
                                    @if ($account->instructions)
                                        <p class="mt-2 rounded-xl bg-white px-3 py-2 text-xs leading-relaxed text-zinc-600">{{ $account->instructions }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </x-ui.section-card>
                @endif

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
                            <input type="file" wire:model="proof_file" class="mt-1 block w-full rounded-2xl border border-brand-black/10 p-3 text-xs text-zinc-600" required />
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
