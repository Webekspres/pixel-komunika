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

        <!-- Invoice Snapshot -->
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
        </div>
    </div>
</x-layout.app-page>
