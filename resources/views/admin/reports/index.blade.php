@php
    $periods = [
        '7d' => '7 Hari',
        '30d' => '30 Hari',
        'month' => 'Bulan Ini',
        'all' => 'Semua',
    ];
@endphp

<x-layouts.app :title="'Laporan - Pixel Komunika'">
    <div class="space-y-6 p-6">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Laporan Penjualan</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Omzet dihitung dari pesanan terkirim (shipped) dan selesai (completed), timezone Jakarta (FR-RPT-001).</p>
        </div>

        <div class="flex flex-col gap-3 rounded-2xl border border-neutral-100 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach ($periods as $key => $label)
                    <a
                        href="{{ route('admin.reports.index', array_filter(['period' => $key !== 'all' ? $key : null, 'district' => $district !== '' ? $district : null])) }}"
                        class="rounded-full px-3.5 py-1.5 text-xs font-bold transition {{ $period === $key ? 'bg-brand-yellow text-brand-black' : 'bg-neutral-100 text-zinc-500 hover:bg-neutral-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                @if ($period !== '')
                    <input type="hidden" name="period" value="{{ $period }}">
                @endif
                <select
                    name="district"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-neutral-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 focus:border-brand-yellow focus:outline-none"
                >
                    <option value="">Semua Kecamatan</option>
                    @foreach ($districts as $name)
                        <option value="{{ $name }}" @selected($district === $name)>{{ $name }}</option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="rounded-xl bg-zinc-900 px-3 py-2 text-xs font-bold text-white">Terapkan</button></noscript>
            </form>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-ui.stat-card
                label="Omzet"
                :value="'Rp '.number_format($omzet, 0, ',', '.')"
                icon="trending-up"
                variant="admin"
                description="Subtotal + ongkir (shipped)"
            />
            <x-ui.stat-card
                label="PPh 22 Terutang"
                :value="'Rp '.number_format($pph22, 0, ',', '.')"
                icon="percent"
                variant="admin"
                description="Dilaporkan terpisah (FR-RPT-004)"
            />
            <x-ui.stat-card
                label="Jumlah Pesanan"
                :value="number_format($orderCount)"
                icon="shopping-bag"
                variant="admin"
                description="Order shipped + completed"
            />
            <x-ui.stat-card
                label="Rata-rata per Pesanan"
                :value="'Rp '.number_format($avgPerOrder, 0, ',', '.')"
                icon="calculator"
                variant="admin"
                description="Omzet ÷ jumlah pesanan"
            />
        </div>

        <div class="rounded-2xl border border-neutral-100 bg-white">
            <div class="border-b border-neutral-100 px-5 py-4">
                <h2 class="text-sm font-bold text-zinc-900">Transaksi</h2>
                <p class="mt-0.5 text-xs text-zinc-500">Daftar pesanan yang masuk omzet pada periode terpilih.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100">
                            <th class="w-12 px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">No</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">No. Order</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Tanggal</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Pelanggan</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Kecamatan</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Subtotal</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Ongkir</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">PPh 22</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse ($transactions as $order)
                            <tr class="transition-colors hover:bg-neutral-50">
                                <td class="w-12 px-5 py-3.5 text-center text-xs text-zinc-400">{{ $transactions->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.orders.show', $order) }}" wire:navigate class="font-bold text-zinc-900 hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-xs whitespace-nowrap text-zinc-500">
                                    {{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $order->recipient_name }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $order->shipping_district ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-right text-xs text-zinc-600">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-xs text-zinc-600">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-xs text-zinc-600">Rp {{ number_format($order->tax_pph22, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-semibold text-zinc-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-sm text-zinc-400">
                                    Tidak ada transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($transactions->isNotEmpty())
                        <tfoot>
                            <tr class="border-t border-neutral-100 bg-neutral-50/70">
                                <td colspan="7" class="px-5 py-3.5 text-right text-xs font-bold tracking-wide text-zinc-500 uppercase">Total omzet periode (subtotal + ongkir)</td>
                                <td class="px-5 py-3.5 text-right text-xs font-bold text-zinc-800">Rp {{ number_format($pph22, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-xs font-bold text-zinc-900">Rp {{ number_format($omzet, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="border-t border-neutral-100 px-5 py-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
