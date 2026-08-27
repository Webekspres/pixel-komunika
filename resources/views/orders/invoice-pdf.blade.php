<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice?->invoice_number ?? $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #18181b; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #71717a; }
        .header { width: 100%; margin-bottom: 20px; border-bottom: 1px solid #e4e4e7; padding-bottom: 12px; }
        .header td { vertical-align: top; }
        .badge { background: #18181b; color: #fff; padding: 3px 8px; font-size: 10px; text-transform: uppercase; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.items th, table.items td { border-bottom: 1px solid #e4e4e7; padding: 8px 4px; text-align: left; }
        table.items th { font-size: 9px; text-transform: uppercase; color: #52525b; }
        .right { text-align: right; }
        .center { text-align: center; }
        .totals { width: 280px; margin-left: auto; margin-top: 16px; }
        .totals td { padding: 4px 0; }
        .totals .grand td { font-weight: bold; font-size: 13px; border-top: 1px solid #e4e4e7; padding-top: 8px; }
        .footer { margin-top: 24px; border-top: 1px solid #e4e4e7; padding-top: 12px; color: #71717a; font-size: 10px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h1>PIXEL KOMUNIKA</h1>
                <div class="muted">
                    {{ $storeProfile?->company_name ?? config('store.company_name') }}<br>
                    NPWP: {{ $storeProfile?->company_npwp ?? config('store.npwp') }}<br>
                    {{ $storeProfile?->address ?? config('store.address') }}
                    • Telp: {{ $storeProfile?->contact_number ?? config('store.phone') }}
                </div>
            </td>
            <td class="right">
                <span class="badge">Faktur Penjualan</span>
                <div style="margin-top:8px;font-weight:bold;">
                    No: {{ $invoice?->invoice_number ?? ('INV/'.$order->created_at->format('Ymd').'/'.$order->id) }}
                </div>
                <div class="muted">
                    Pesanan: #{{ $order->order_number }}<br>
                    Tanggal: {{ $order->created_at->format('d M Y, H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="header" style="border:none;">
        <tr>
            <td width="50%">
                <strong class="muted" style="font-size:9px;text-transform:uppercase;">Ditagihkan Kepada</strong><br>
                <strong>{{ $order->user->name }}</strong><br>
                {{ $order->user->customerProfile?->business_name ?: 'Pelanggan Toko' }}<br>
                {{ $order->user->email }} • {{ $order->user->phone ?? '-' }}
            </td>
            <td width="50%">
                <strong class="muted" style="font-size:9px;text-transform:uppercase;">Tujuan Pengiriman</strong><br>
                <strong>{{ $order->recipient_name }} ({{ $order->recipient_phone }})</strong><br>
                {{ $order->shipping_address_line }}<br>
                {{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                @if ($order->courier_code)
                    <br>Ekspedisi: {{ strtoupper($order->courier_code.' '.$order->courier_service) }}
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi / SKU</th>
                <th class="right">Harga</th>
                <th class="center">Qty</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product_name }}
                        @if ($item->sku)
                            <br><span class="muted">SKU: {{ $item->sku }}</span>
                        @endif
                    </td>
                    <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal Produk</td>
            <td class="right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($order->tax_pph22 > 0)
            <tr>
                <td>PPh 22</td>
                <td class="right">Rp {{ number_format($order->tax_pph22, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td>Ongkos Kirim</td>
            <td class="right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand">
            <td>Total Tagihan</td>
            <td class="right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <strong>Catatan:</strong><br>
        1. Faktur ini merupakan bukti transaksi yang sah dari Pixel Komunika.<br>
        2. Barang yang telah dibeli sesuai pesanan tidak dapat dikembalikan kecuali ada kesepakatan tertulis.
    </div>
</body>
</html>
