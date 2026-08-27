<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $invoice?->invoice_number ?? $order->order_number }} - Pixel Komunika</title>
    <x-favicon />
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 12px; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e4e4e7 !important; }
        }
    </style>
</head>
<body class="bg-zinc-100 font-sans text-zinc-900 antialiased p-4 sm:p-8">
    <div class="max-w-4xl mx-auto space-y-4">
        <!-- Top Action Bar (Hidden on Print) -->
        <div class="no-print flex items-center justify-between bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-2xs">
            <a
                href="{{ url()->previous() ?: route('orders.index') }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-zinc-50 px-3.5 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-100 transition"
            >
                <x-icon name="arrow-left" class="size-4" />
                <span>Kembali</span>
            </a>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('orders.invoice.download', $order) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-zinc-50 px-3.5 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-100 transition"
                >
                    <x-icon name="document-arrow-down" class="size-4" />
                    <span>Unduh PDF</span>
                </a>
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                >
                    <x-icon name="printer" class="size-4" />
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- Printable Invoice Sheet -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-10 shadow-md print-shadow-none space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 border-b border-zinc-200 pb-6">
                <div>
                    <h1 class="text-2xl font-black text-zinc-900 tracking-tight">PIXEL KOMUNIKA</h1>
                    <p class="text-xs text-zinc-500 mt-1">
                        {{ $storeProfile?->company_name ?? 'PT Webekspres Teknologi Indonesia' }}<br>
                        NPWP: {{ $storeProfile?->company_npwp ?? '0821.4146.0442.4000' }}<br>
                        {{ $storeProfile?->address ?? 'Bandung, Jawa Barat' }} • Telp: {{ $storeProfile?->contact_number ?? '0821-4146-0442' }}
                    </p>
                </div>

                <div class="sm:text-right">
                    <span class="inline-block rounded-lg bg-zinc-900 px-3 py-1 text-xs font-black text-white uppercase tracking-widest">
                        FAKTUR PENJUALAN
                    </span>
                    <p class="font-mono font-bold text-sm text-zinc-900 mt-2">
                        No: {{ $invoice?->invoice_number ?? ('INV/' . $order->created_at->format('Ymd') . '/' . $order->id) }}
                    </p>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        Pesanan: #{{ $order->order_number }}<br>
                        Tanggal: {{ $order->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="grid sm:grid-cols-2 gap-6 text-xs">
                <div>
                    <p class="font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Ditagihkan Kepada:</p>
                    <p class="font-bold text-sm text-zinc-900 mt-1">{{ $order->user->name }}</p>
                    <p class="text-zinc-600">{{ $order->user->customerProfile?->business_name ?: 'Pelanggan Toko' }}</p>
                    <p class="text-zinc-600">{{ $order->user->email }} • {{ $order->user->phone ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Tujuan Pengiriman:</p>
                    <p class="font-bold text-sm text-zinc-900 mt-1">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                    <p class="text-zinc-600 mt-0.5 leading-relaxed">
                        {{ $order->shipping_address_line }}<br>
                        {{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                    </p>
                    @if ($order->courier_code)
                        <p class="text-zinc-500 mt-1">Ekspedisi: <strong class="uppercase text-zinc-800">{{ $order->courier_code }} {{ $order->courier_service }}</strong></p>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-y border-zinc-200 bg-zinc-50/75 text-zinc-600 font-bold uppercase text-[10px]">
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">Deskripsi Barang / SKU</th>
                            <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                            <th class="py-2.5 px-3 text-center">Qty</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($order->items as $index => $item)
                            <tr>
                                <td class="py-3 px-3 text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3 px-3 font-semibold text-zinc-900">
                                    {{ $item->product_name }}
                                    @if ($item->sku)
                                        <span class="block font-mono text-[10px] font-normal text-zinc-500">SKU: {{ $item->sku }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right text-zinc-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center font-bold text-zinc-900">{{ $item->quantity }}</td>
                                <td class="py-3 px-3 text-right font-bold text-zinc-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Calculations -->
            <div class="flex justify-end pt-4 border-t border-zinc-200">
                <div class="w-full sm:w-72 space-y-2 text-xs">
                    <div class="flex justify-between text-zinc-600">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if ($order->tax_pph22 > 0)
                        <div class="flex justify-between text-zinc-600">
                            <span>PPh 22 (0.5%)</span>
                            <span class="font-semibold text-zinc-900">Rp {{ number_format($order->tax_pph22, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-zinc-600">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-sm font-black text-zinc-900 border-t border-zinc-200 pt-2">
                        <span>Total Tagihan</span>
                        <span class="text-base">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer note -->
            <div class="border-t border-zinc-200 pt-6 text-[11px] text-zinc-500 space-y-1">
                <p class="font-bold text-zinc-700">Catatan:</p>
                <p>1. Faktur ini merupakan bukti transaksi yang sah dari Pixel Komunika.</p>
                <p>2. Barang yang telah dibeli sesuai dengan pesanan tidak dapat dikembalikan kecuali ada kesepakatan tertulis sebelumnya.</p>
            </div>
        </div>
    </div>
</body>
</html>
