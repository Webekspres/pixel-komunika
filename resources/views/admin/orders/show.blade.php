<x-layouts.app :title="'Detail Order Admin - Pixel Komunika'">
    <x-layout.admin-page
        :title="'Order '.$order->order_number"
        description="Detail order tetap berada di application shell agar review admin, pembayaran, dan pengiriman tidak keluar context."
    >
        <x-slot name="actions">
            <flux:button href="{{ route('admin.orders.index') }}" variant="ghost" size="sm" icon="arrow-left">
                Kembali
            </flux:button>
        </x-slot>

        <div class="grid gap-6 md:grid-cols-3">
            <x-ui.stat-card label="Pelanggan" :value="$order->user->name" icon="users" variant="admin" />
            <x-ui.stat-card label="Status order" :value="\App\Services\Admin\AdminDashboardService::orderStatusLabel($order->status)" icon="clipboard-list" variant="admin" />
            <x-ui.stat-card label="Status invoice" :value="strtoupper($order->invoice?->status ?? 'draft')" icon="badge-check" variant="admin" />
        </div>

        <livewire:admin.order-fulfillment-actions :order="$order" />

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <x-ui.section-card title="Informasi order" description="Snapshot transaksi untuk review admin." variant="admin">
                <div class="grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <p class="text-zinc-500">Nomor order</p>
                        <p class="font-bold text-zinc-900">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">Tanggal transaksi</p>
                        <p class="font-semibold text-zinc-900">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">Pelanggan</p>
                        <p class="font-semibold text-zinc-900">{{ $order->user->name }} • {{ $order->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">Batas pembayaran</p>
                        <p class="font-semibold text-zinc-900">{{ $order->expires_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-zinc-200 pt-4">
                    <p class="text-sm font-semibold text-zinc-900">Alamat pengiriman</p>
                    <p class="mt-2 text-sm text-zinc-700">{{ $order->recipient_name }} • {{ $order->recipient_phone }}</p>
                    <p class="mt-1 text-sm leading-relaxed text-zinc-600">
                        {{ $order->shipping_address_line }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                    </p>
                    <p class="mt-3 inline-flex rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-700">
                        {{ strtoupper($order->courier_code) }} - {{ $order->courier_service }}
                    </p>
                </div>
            </x-ui.section-card>

            <x-ui.section-card title="Invoice snapshot" description="Nominal akhir untuk validasi pembayaran dan pengiriman." variant="admin">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-zinc-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-600">
                        <span>PPh 22</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->tax_pph22, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-600">
                        <span>Ongkos kirim</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-zinc-200 pt-3">
                        <div class="flex justify-between text-base font-bold text-zinc-900">
                            <span>Total invoice</span>
                            <span class="text-amber-700">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if ($order->latestPaymentProof)
                        <div class="rounded-[1.25rem] border border-zinc-200 bg-zinc-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500">Bukti pembayaran terakhir</p>
                            <p class="mt-2 text-sm font-semibold text-zinc-900">
                                {{ $order->latestPaymentProof->bank_name }} • {{ $order->latestPaymentProof->account_name }}
                            </p>
                            <p class="mt-1 text-sm text-zinc-600">Rp {{ number_format($order->latestPaymentProof->amount, 0, ',', '.') }}</p>
                            <p class="mt-2 text-xs font-semibold uppercase text-zinc-500">{{ $order->latestPaymentProof->status }}</p>
                        </div>
                    @endif
                </div>
            </x-ui.section-card>
        </div>

        <x-ui.section-card title="Item produk" description="Daftar item yang dibeli pelanggan pada order ini." variant="admin">
            <div class="divide-y divide-zinc-200">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between gap-4 py-4 text-sm">
                        <div>
                            <p class="font-semibold text-zinc-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-zinc-500">SKU: {{ $item->sku }} • {{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <span class="font-bold text-zinc-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </x-ui.section-card>
    </x-layout.admin-page>
</x-layouts.app>
