<x-layout.app-page>
    <x-ui.page-header
        eyebrow="Akun Pelanggan"
        title="Riwayat Pesanan"
        description="Daftar seluruh pesanan Anda beserta status invoice dan pengiriman."
    />

    @if ($orders->isEmpty())
        <x-ui.empty-state
            title="Belum Ada Pesanan"
            description="Anda belum pernah membuat pesanan di Pixel Komunika."
            icon="shopping-bag"
        />
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-[#F8B818] px-6 py-3 font-semibold text-[#181818] hover:bg-[#F8D820] transition-all shadow-sm">
                Jelajahi Produk
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-100 pb-3">
                        <div>
                            <span class="font-bold text-zinc-900 text-lg">{{ $order->order_number }}</span>
                            <span class="text-xs text-zinc-500 ml-2">• {{ $order->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                {{ $order->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-700 font-medium">{{ $item->product_name }} ({{ $item->quantity }}x)</span>
                                <span class="font-semibold text-zinc-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-3 border-t border-zinc-100 gap-4">
                        <div class="text-sm">
                            <span class="text-zinc-500">Total Tagihan:</span>
                            <span class="font-bold text-amber-800 text-lg ml-1">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-2 text-xs font-bold text-white hover:bg-zinc-800 transition-all">
                                Lihat Detail Order & Invoice
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</x-layout.app-page>
